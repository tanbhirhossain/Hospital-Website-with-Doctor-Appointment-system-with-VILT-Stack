<?php

namespace Modules\HEALTHAI\Services\Agent;

use Illuminate\Support\Facades\Log;
use Modules\HEALTHAI\Enums\AgentIntent;
use Modules\HEALTHAI\Prompts\AgentPrompts;
use Modules\HEALTHAI\Services\Agent\Resolvers\AppointmentResolver;

final class AiAgentService
{
    public function __construct(
        private readonly IntentRouter              $intentRouter,
        private readonly AgentDataResolverFactory  $resolverFactory,
        private readonly OllamaClient              $ollama,
    ) {}

    /**
     * @param  array<int, array{role: string, content: string}> $history
     *         Full conversation so far (NOT including the current user message).
     * @return array{reply: string, history: array}
     */
    public function handle(string $userMessage, array $history = []): array
    {
        // 1. Route intent (LLM sees history for pronoun resolution etc.)
        $decision = $this->intentRouter->resolve($userMessage, $history);
        $intent   = $decision->intent;
        $keyword  = $decision->keyword;

        Log::info('[AiAgentService] Routing resolved', [
            'intent'  => $intent->value,
            'keyword' => $keyword,
        ]);

        // 2. Resolve data
        $resolver     = $this->resolverFactory->make($intent);
        $resolvedData = $resolver->resolve($keyword);

        // 3. Build reply prompt based on intent
        if ($intent === AgentIntent::BOOK_APPOINTMENT) {
            $reply = $this->handleAppointmentTurn(
                $userMessage,
                $history,
                $resolver,      // AppointmentResolver
                $resolvedData,  // available doctors list
            );
        } else {
            $prompt = AgentPrompts::finalReply($userMessage, $resolvedData);
            $reply  = $this->ollama->chat($prompt, $history)
                ?? 'দুঃখিত, এই মুহূর্তে উত্তর দিতে পারছি না। অনুগ্রহ করে পরে চেষ্টা করুন।';
        }

        // 4. Append current turn to history and return
        $updatedHistory   = $history;
        $updatedHistory[] = ['role' => 'user',      'content' => $userMessage];
        $updatedHistory[] = ['role' => 'assistant',  'content' => $reply];

        return ['reply' => $reply, 'history' => $updatedHistory];
    }

    // -------------------------------------------------------------------------

    /**
     * Handles one turn of the multi-step appointment booking conversation.
     *
     * The LLM drives the flow. When it is ready to book it embeds an action
     * block:  %%ACTION:BOOK_APPOINTMENT%%{...json...}%%END_ACTION%%
     * We extract that, call AppointmentResolver::book(), then generate a
     * human-readable confirmation/error reply.
     */
    private function handleAppointmentTurn(
        string              $userMessage,
        array               $history,
        AppointmentResolver $resolver,
        array               $availableDoctors,
    ): string {
        $prompt = AgentPrompts::appointmentFlow($userMessage, $availableDoctors);
        $raw    = $this->ollama->chat($prompt, $history) ?? '';

        // Detect action block
        if (preg_match(
            '/%%ACTION:BOOK_APPOINTMENT%%(.*?)%%END_ACTION%%/s',
            $raw,
            $matches,
        )) {
            $actionJson = trim($matches[1]);
            $data       = json_decode($actionJson, true);

            if (is_array($data) && !empty($data['doctor_id'])) {
                $result = $resolver->book($data);

                // Strip the action block from the visible reply
                $visiblePart = trim(preg_replace(
                    '/%%ACTION:BOOK_APPOINTMENT%%.*?%%END_ACTION%%/s',
                    '',
                    $raw,
                ));

                // Let the LLM generate a nice confirmation / error message
                if ($result['success']) {
                    $confirmPrompt = AgentPrompts::bookingConfirmed($result);
                } else {
                    $confirmPrompt = AgentPrompts::bookingFailed($result['error'] ?? 'Unknown error');
                }

                $confirmReply = $this->ollama->chat($confirmPrompt, $history) ?? '';
                return ($visiblePart ? $visiblePart . "\n\n" : '') . $confirmReply;
            }
        }

        // No action block yet — LLM is still collecting info
        return $raw ?: 'দুঃখিত, অ্যাপয়েন্টমেন্ট প্রক্রিয়ায় সমস্যা হয়েছে।';
    }
}
