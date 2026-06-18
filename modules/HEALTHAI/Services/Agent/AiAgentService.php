<?php

namespace Modules\HEALTHAI\Services\Agent;

use Illuminate\Support\Facades\Log;
use Modules\HEALTHAI\Enums\AgentIntent;
use Modules\HEALTHAI\Prompts\AgentPrompts;
use Modules\HEALTHAI\Services\Agent\Resolvers\AppointmentResolver;

final class AiAgentService
{
    /**
     * Injected into history (role=system) to mark an active booking flow.
     * Never shown to the user.
     */
    private const BOOKING_FLOW_MARKER = '__BOOKING_FLOW_ACTIVE__';

    /**
     * Sidecar: last doctor payload for the controller to attach as card data.
     * Using a static property avoids having to change the return signature.
     */
    private static ?array $lastDoctorPayload = null;

    public static function getLastDoctorPayload(): ?array  { return self::$lastDoctorPayload; }
    public static function clearLastDoctorPayload(): void  { self::$lastDoctorPayload = null; }

    public function __construct(
        private readonly IntentRouter             $intentRouter,
        private readonly AgentDataResolverFactory $resolverFactory,
        private readonly OllamaClient             $ollama,
    ) {}

    /**
     * @param  array<int, array{role: string, content: string}> $history
     * @return array{reply: string, history: array}
     */
    public function handle(string $userMessage, array $history = []): array
    {
        self::$lastDoctorPayload = null; // reset each request

        // ── 1. Intent locking for active booking flow ────────────────────────
        $inBookingFlow = $this->isBookingFlowActive($history);

        if ($inBookingFlow) {
            $intent  = AgentIntent::BOOK_APPOINTMENT;
            $keyword = $userMessage;
            Log::info('[AiAgentService] Booking flow continuation — router bypassed');
        } else {
            $decision = $this->intentRouter->resolve($userMessage, $history);
            $intent   = $decision->intent;
            $keyword  = $decision->keyword;
            Log::info('[AiAgentService] Routing resolved', [
                'intent'  => $intent->value,
                'keyword' => $keyword,
            ]);
        }

        // ── 2. Resolve data ──────────────────────────────────────────────────
        $resolver     = $this->resolverFactory->make($intent);
        $resolvedData = $resolver->resolve($keyword);

        // ── 3. Sidecar: stash doctor list for card rendering ─────────────────
        if ($intent === AgentIntent::DATABASE_DOCTOR) {
            $doctors = array_filter(
                $resolvedData,
                fn ($item) => ($item['Type'] ?? '') === 'Doctor',
            );
            if (!empty($doctors)) {
                self::$lastDoctorPayload = array_values($doctors);
            }
        }

        // ── 4. Generate reply ────────────────────────────────────────────────
        if ($intent === AgentIntent::BOOK_APPOINTMENT) {
            [$reply, $bookingCompleted] = $this->handleAppointmentTurn(
                $userMessage, $history, $resolver, $resolvedData,
            );
        } else {
            $bookingCompleted = false;
            $prompt           = AgentPrompts::finalReply($userMessage, $resolvedData);
            $reply            = $this->ollama->chat($prompt, $history)
                ?? 'দুঃখিত, এই মুহূর্তে উত্তর দিতে পারছি না। অনুগ্রহ করে পরে চেষ্টা করুন।';
        }

        // ── 5. Update history ────────────────────────────────────────────────
        $updatedHistory = $history;

        if ($intent === AgentIntent::BOOK_APPOINTMENT && !$inBookingFlow) {
            // First booking turn — inject marker
            $updatedHistory[] = ['role' => 'system', 'content' => self::BOOKING_FLOW_MARKER];
        }

        if ($bookingCompleted) {
            // Remove marker — flow is done
            $updatedHistory = array_values(array_filter(
                $updatedHistory,
                fn ($t) => $t['content'] !== self::BOOKING_FLOW_MARKER,
            ));
        }

        $updatedHistory[] = ['role' => 'user',      'content' => $userMessage];
        $updatedHistory[] = ['role' => 'assistant',  'content' => $reply];

        return ['reply' => $reply, 'history' => $updatedHistory];
    }

    // ─── Appointment handling ────────────────────────────────────────────────

    /** @return array{0: string, 1: bool} */
    private function handleAppointmentTurn(
        string              $userMessage,
        array               $history,
        AppointmentResolver $resolver,
        array               $availableDoctors,
    ): array {
        $cleanHistory = $this->stripSystemMarkers($history);

        $prompt = AgentPrompts::appointmentFlow($userMessage, $availableDoctors);
        $raw    = $this->ollama->chat($prompt, $cleanHistory) ?? '';

        if (preg_match('/%%ACTION:BOOK_APPOINTMENT%%(.*?)%%END_ACTION%%/s', $raw, $matches)) {
            $data = json_decode(trim($matches[1]), true);

            if (is_array($data) && !empty($data['doctor_id'])) {
                $result = $resolver->book($data);

                // ONE reply only — generated fresh from DB result, not from LLM draft
                $confirmPrompt = $result['success']
                    ? AgentPrompts::bookingConfirmed($result)
                    : AgentPrompts::bookingFailed($result['error'] ?? 'Unknown error');

                $reply = $this->ollama->chat($confirmPrompt, [])
                    ?? ($result['success']
                        ? 'অ্যাপয়েন্টমেন্ট সফলভাবে বুক হয়েছে।'
                        : 'বুকিং সম্পন্ন হয়নি। আবার চেষ্টা করুন।');

                return [$reply, true];
            }
        }

        // Still collecting fields
        return [$raw ?: 'দুঃখিত, অ্যাপয়েন্টমেন্ট প্রক্রিয়ায় সমস্যা হয়েছে।', false];
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function isBookingFlowActive(array $history): bool
    {
        foreach ($history as $turn) {
            if (($turn['content'] ?? '') === self::BOOKING_FLOW_MARKER) return true;
        }
        return false;
    }

    private function stripSystemMarkers(array $history): array
    {
        return array_values(array_filter(
            $history,
            fn ($t) => ($t['role'] ?? '') !== 'system',
        ));
    }
}
