<?php

namespace Modules\HEALTHAI\Services\Agent;

use Illuminate\Support\Facades\Log;
use Modules\HEALTHAI\Enums\AgentIntent;
use Modules\HEALTHAI\Prompts\AgentPrompts;
use Modules\HEALTHAI\Services\Agent\Resolvers\AppointmentResolver;

final class AiAgentService
{
    private const BOOKING_FLOW_MARKER      = '__BOOKING_FLOW_ACTIVE__';
    private const HEALTH_TIPS_FLOW_MARKER  = '__HEALTH_TIPS_FLOW_ACTIVE__';
    private const LAB_REPORT_FLOW_MARKER   = '__LAB_REPORT_FLOW_ACTIVE__';

    private static ?array $lastDoctorPayload    = null;
    private static ?array $lastLabReportPayload = null;

    public static function getLastDoctorPayload(): ?array    { return self::$lastDoctorPayload; }
    public static function clearLastDoctorPayload(): void    { self::$lastDoctorPayload = null; }
    public static function getLastLabReportPayload(): ?array { return self::$lastLabReportPayload; }
    public static function clearLastLabReportPayload(): void { self::$lastLabReportPayload = null; }

    public function __construct(
        private readonly IntentRouter             $intentRouter,
        private readonly AgentDataResolverFactory $resolverFactory,
        private readonly OllamaClient             $ollama,
    ) {}

    public function handle(string $userMessage, array $history = []): array
    {
        self::$lastDoctorPayload    = null;
        self::$lastLabReportPayload = null;

        // ── 1. Check for active flows ──────────────────────────────────────
        $inBookingFlow    = $this->isFlowActive($history, self::BOOKING_FLOW_MARKER);
        $inHealthTipsFlow = $this->isFlowActive($history, self::HEALTH_TIPS_FLOW_MARKER);
        $inLabReportFlow  = $this->isFlowActive($history, self::LAB_REPORT_FLOW_MARKER);

        if ($inBookingFlow) {
            $intent  = AgentIntent::BOOK_APPOINTMENT;
            $keyword = $userMessage;
            Log::info('[AiAgentService] Booking flow continuation');
        } elseif ($inHealthTipsFlow) {
            $intent  = AgentIntent::HEALTH_TIPS;
            $keyword = $userMessage;
            Log::info('[AiAgentService] Health tips flow continuation');
        } elseif ($inLabReportFlow) {
            $intent  = AgentIntent::LAB_REPORT;
            $keyword = $userMessage;
            Log::info('[AiAgentService] Lab report flow continuation');
        } else {
            $decision = $this->intentRouter->resolve($userMessage, $history);
            $intent   = $decision->intent;
            $keyword  = $decision->keyword;
            Log::info('[AiAgentService] Routing resolved', [
                'intent'  => $intent->value,
                'keyword' => $keyword,
            ]);
        }

        // ── 2. Resolve data ─────────────────────────────────────────────────
        $resolver     = $this->resolverFactory->make($intent);
        $resolvedData = $resolver->resolve($keyword);

        // ── 3. Sidecar payloads for frontend card rendering ─────────────────
        if ($intent === AgentIntent::DATABASE_DOCTOR) {
            $doctors = array_filter(
                $resolvedData,
                fn ($item) => ($item['Type'] ?? '') === 'Doctor',
            );
            if (!empty($doctors)) {
                self::$lastDoctorPayload = array_values($doctors);
            }
        }

        if ($intent === AgentIntent::LAB_REPORT) {
            $report = $resolvedData[0] ?? [];
            if (($report['Status'] ?? '') === 'FOUND') {
                self::$lastLabReportPayload = $report;
            }
        }

        // ── 4. Generate reply ───────────────────────────────────────────────
        $bookingCompleted    = false;
        $healthTipsCompleted = false;
        $labReportCompleted  = false;

        if ($intent === AgentIntent::BOOK_APPOINTMENT) {
            [$reply, $bookingCompleted] = $this->handleAppointmentTurn(
                $userMessage, $history, $resolver, $resolvedData,
            );
        } elseif ($intent === AgentIntent::HEALTH_TIPS) {
            [$reply, $healthTipsCompleted] = $this->handleHealthTipsTurn(
                $userMessage, $history, $resolvedData, $inHealthTipsFlow,
            );
        } elseif ($intent === AgentIntent::LAB_REPORT) {
            [$reply, $labReportCompleted] = $this->handleLabReportTurn(
                $userMessage, $history, $resolvedData, $inLabReportFlow,
            );
        } else {
            $prompt = match ($intent) {
                AgentIntent::DIET_PLAN => AgentPrompts::dietPlanReply($userMessage, $resolvedData),
                default                => AgentPrompts::finalReply($userMessage, $resolvedData),
            };

            $reply = $this->ollama->chat($prompt, $history)
                ?? 'দুঃখিত, এই মুহূর্তে উত্তর দিতে পারছি না। অনুগ্রহ করে পরে চেষ্টা করুন।';
        }

        // ── 5. Update history ───────────────────────────────────────────────
        $updatedHistory = $history;
        $allMarkers = [self::BOOKING_FLOW_MARKER, self::HEALTH_TIPS_FLOW_MARKER, self::LAB_REPORT_FLOW_MARKER];

        // Inject markers on first turn of each flow
        if ($intent === AgentIntent::BOOK_APPOINTMENT && !$inBookingFlow) {
            $updatedHistory[] = ['role' => 'system', 'content' => self::BOOKING_FLOW_MARKER];
        }
        if ($intent === AgentIntent::HEALTH_TIPS && !$inHealthTipsFlow) {
            $updatedHistory[] = ['role' => 'system', 'content' => self::HEALTH_TIPS_FLOW_MARKER];
        }
        if ($intent === AgentIntent::LAB_REPORT && !$inLabReportFlow) {
            $updatedHistory[] = ['role' => 'system', 'content' => self::LAB_REPORT_FLOW_MARKER];
        }

        // Remove markers when flows complete
        $anyCompleted = $bookingCompleted || $healthTipsCompleted || $labReportCompleted;
        if ($anyCompleted) {
            $completedMarkers = array_filter([
                $bookingCompleted    ? self::BOOKING_FLOW_MARKER     : null,
                $healthTipsCompleted ? self::HEALTH_TIPS_FLOW_MARKER : null,
                $labReportCompleted  ? self::LAB_REPORT_FLOW_MARKER  : null,
            ]);

            $updatedHistory = array_values(array_filter(
                $updatedHistory,
                fn ($t) => !in_array($t['content'] ?? '', $completedMarkers),
            ));
        }

        $updatedHistory[] = ['role' => 'user',     'content' => $userMessage];
        $updatedHistory[] = ['role' => 'assistant', 'content' => $reply];

        return ['reply' => $reply, 'history' => $updatedHistory];
    }

    // ─── Appointment handling (with slot checking) ──────────────────────────

    private function handleAppointmentTurn(
        string              $userMessage,
        array               $history,
        AppointmentResolver $resolver,
        array               $availableDoctors,
    ): array {
        $cleanHistory = $this->stripSystemMarkers($history);

        // Check if user has selected a doctor + date → fetch available slots
        $slotsData = $this->tryFetchSlots($userMessage, $cleanHistory, $resolver, $availableDoctors);

        $prompt = AgentPrompts::appointmentFlow($userMessage, $availableDoctors, $slotsData);
        $raw    = $this->ollama->chat($prompt, $cleanHistory) ?? '';

        if (preg_match('/%%ACTION:BOOK_APPOINTMENT%%(.*?)%%END_ACTION%%/s', $raw, $matches)) {
            $data = json_decode(trim($matches[1]), true);

            if (is_array($data) && !empty($data['doctor_id'])) {
                // Verify the chosen slot is actually available before booking
                if (!empty($data['appointment_date']) && !empty($data['start_time'])) {
                    $validSlots = $resolver->getAvailableSlots(
                        (int) $data['doctor_id'],
                        $data['appointment_date']
                    );

                    $slotValid = false;
                    foreach ($validSlots as $slot) {
                        if (($slot['start_time'] ?? '') === substr($data['start_time'], 0, 5)
                            && ($slot['available'] ?? false)) {
                            $slotValid = true;
                            // Use the exact slot times from the system
                            $data['start_time'] = $slot['start_time'];
                            $data['end_time']   = $slot['end_time'];
                            $data['slot_duration'] = 15;
                            break;
                        }
                    }

                    if (!$slotValid && !empty($validSlots)) {
                        $available = array_filter($validSlots, fn ($s) => $s['available'] ?? false);
                        $timeList = implode(', ', array_map(fn ($s) => $s['start_time'], array_slice($available, 0, 8)));
                        return ["দুঃখিত, আপনার পছন্দের সময়টি আর খালি নেই। এই তারিখে খালি স্লটগুলো হলো: {$timeList}। অনুগ্রহ করে একটি বেছে নিন।", false];
                    }
                }

                $result = $resolver->book($data);

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

        return [$raw ?: 'দুঃখিত, অ্যাপয়েন্টমেন্ট প্রক্রিয়ায় সমস্যা হয়েছে।', false];
    }

    /**
     * Try to detect if the user has selected a doctor + date and fetch available slots.
     * Returns slot data string for the prompt, or null if not applicable.
     */
    private function tryFetchSlots(string $userMessage, array $history, AppointmentResolver $resolver, array $doctors): ?string
    {
        // Collect all conversation text (user + assistant messages)
        $allText = $userMessage;
        foreach ($history as $turn) {
            $allText .= ' ' . ($turn['content'] ?? '');
        }
        $lowerText = mb_strtolower($allText);

        // ── Find doctor by matching NAME (not ID) ───────────────────────
        $doctorId = null;

        // Build name→id map and try to match doctor names in conversation
        foreach ($doctors as $doc) {
            $name = $doc['name'] ?? '';
            if (empty($name)) continue;

            // Match by full name or last name portion
            $nameLower = mb_strtolower($name);
            if (mb_stripos($lowerText, $nameLower) !== false) {
                $doctorId = (int) ($doc['doctor_id'] ?? 0);
                break;
            }

            // Also try matching by last word of name (common in Bengali: "Asaduzzaman")
            $nameParts = preg_split('/\s+/', $name);
            $lastName = mb_strtolower(end($nameParts));
            if (mb_strlen($lastName) >= 4 && mb_stripos($lowerText, $lastName) !== false) {
                $doctorId = (int) ($doc['doctor_id'] ?? 0);
                break;
            }
        }

        // ── Extract date (YYYY-MM-DD format) ────────────────────────────
        $date = null;
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $allText, $m)) {
            $date = $m[1];
        }

        // ── If we have both doctor + date, fetch live slots ─────────────
        if ($doctorId && $date) {
            Log::info('[AiAgentService] Fetching slots', ['doctor_id' => $doctorId, 'date' => $date]);

            try {
                $slots = $resolver->getAvailableSlots($doctorId, $date);
                $available = array_filter($slots, fn ($s) => $s['available'] ?? false);

                if (!empty($available)) {
                    $slotList = array_map(
                        fn ($s) => $s['start_time'] . '-' . $s['end_time'],
                        array_slice(array_values($available), 0, 20)
                    );
                    return "AVAILABLE SLOTS for doctor #{$doctorId} on {$date}:\n"
                         . implode(', ', $slotList) . "\n"
                         . "Total available: " . count($available) . " slots. "
                         . "Each slot is 15 minutes. Present these to the user and let them pick one.";
                }

                return "NO SLOTS AVAILABLE for doctor #{$doctorId} on {$date}. "
                     . "Tell the user this date has no available slots and ask them to pick a different date.";
            } catch (\Throwable $e) {
                Log::error('[AiAgentService] Slot fetch error', ['error' => $e->getMessage()]);
                return "Could not fetch slots (error). Proceed with manual time selection but keep slots to 15 minutes.";
            }
        }

        return null;
    }

    // ─── Health tips conversation ───────────────────────────────────────────

    private function handleHealthTipsTurn(
        string $userMessage,
        array  $history,
        array  $resolvedData,
        bool   $inFlow,
    ): array {
        $cleanHistory = $this->stripSystemMarkers($history);

        $prompt = AgentPrompts::healthTipsFlow($userMessage, $resolvedData, $inFlow);
        $raw    = $this->ollama->chat($prompt, $cleanHistory) ?? '';

        $completed = str_contains($raw, '%%ACTION:HEALTH_TIPS_COMPLETE%%');

        if ($completed) {
            $reply = trim(str_replace('%%ACTION:HEALTH_TIPS_COMPLETE%%', '', $raw));
            return [$reply, true];
        }

        return [$raw ?: 'আপনার সমস্যাটি আরেকটু বিস্তারিত বলুন, আমি সাহায্য করার চেষ্টা করব।', false];
    }

    // ─── Lab report conversation ────────────────────────────────────────────

    private function handleLabReportTurn(
        string $userMessage,
        array  $history,
        array  $resolvedData,
        bool   $inFlow,
    ): array {
        $status = $resolvedData[0]['Status'] ?? '';

        // If found or error — flow is complete
        if (in_array($status, ['FOUND', 'NOT_FOUND', 'ERROR'])) {
            $prompt = AgentPrompts::labReportReply($userMessage, $resolvedData);
            $reply  = $this->ollama->chat($prompt, $this->stripSystemMarkers($history))
                ?? 'রিপোর্ট সম্পর্কে তথ্য দিতে সমস্যা হয়েছে।';
            return [$reply, true];
        }

        // NEED_INFO — ask for missing data interactively
        $prompt = AgentPrompts::labReportReply($userMessage, $resolvedData);
        $reply  = $this->ollama->chat($prompt, $this->stripSystemMarkers($history))
            ?? 'আপনার মোবাইল নম্বর এবং ইনভয়েস নম্বর দিন।';
        return [$reply, false];
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function isFlowActive(array $history, string $marker): bool
    {
        foreach ($history as $turn) {
            if (($turn['content'] ?? '') === $marker) return true;
        }
        return false;
    }

    private function stripSystemMarkers(array $history): array
    {
        $markers = [self::BOOKING_FLOW_MARKER, self::HEALTH_TIPS_FLOW_MARKER, self::LAB_REPORT_FLOW_MARKER];
        return array_values(array_filter(
            $history,
            fn ($t) => ($t['role'] ?? '') !== 'system'
                    && !in_array($t['content'] ?? '', $markers),
        ));
    }
}
