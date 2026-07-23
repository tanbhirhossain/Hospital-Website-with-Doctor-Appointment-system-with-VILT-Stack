<?php

namespace Modules\HEALTHAI\Services\Agent;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin HTTP wrapper around the Ollama /api/chat endpoint.
 *
 * All public methods now accept a $history array of prior turns
 * so that session continuity is preserved across the full conversation.
 *
 * History format (mirrors Ollama's messages array):
 *   [['role' => 'user'|'assistant', 'content' => '…'], …]
 */
final class OllamaClient
{
    private string $endpoint;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int    $timeoutSeconds = 60,
    ) {
        $this->endpoint = rtrim($baseUrl, '/') . '/api/chat';
    }

    /**
     * Chat with JSON format enforced (used for routing).
     * History is injected so the router can see prior context too.
     *
     * @param  array<int, array{role: string, content: string}> $history
     * @return array<string, mixed>|null
     */
    public function chatJson(string $prompt, array $history = []): ?array
    {
        $messages = $this->buildMessages($prompt, $history);

        try {
            $response = Http::timeout($this->timeoutSeconds)->post($this->endpoint, [
                'model'    => $this->model,
                'messages' => $messages,
                'stream'   => false,
                'format'   => 'json',
            ]);

            $raw   = $response->json()['message']['content'] ?? '{}';
            $clean = preg_replace('#```(?:json)?\s*(.*?)\s*```#s', '$1', $raw);

            return json_decode(trim($clean), true) ?? null;
        } catch (\Throwable $e) {
            Log::error('[OllamaClient] chatJson failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Chat for free-form text (used for final reply generation).
     *
     * @param  array<int, array{role: string, content: string}> $history
     */
    public function chat(string $prompt, array $history = []): ?string
    {
        $messages = $this->buildMessages($prompt, $history);

        try {
            $response = Http::timeout($this->timeoutSeconds)->post($this->endpoint, [
                'model'    => $this->model,
                'messages' => $messages,
                'stream'   => false,
            ]);

            return $response->json()['message']['content'] ?? null;
        } catch (\Throwable $e) {
            Log::error('[OllamaClient] chat failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Build the Ollama messages array.
     * History comes first (user/assistant pairs), then the new user prompt.
     *
     * @param  array<int, array{role: string, content: string}> $history
     * @return array<int, array{role: string, content: string}>
     */
    private function buildMessages(string $prompt, array $history): array
    {
        // Sanitise history — only keep role + content, drop any frontend-only keys
        $sanitised = array_map(
            fn ($turn) => [
                'role'    => in_array($turn['role'] ?? '', ['user', 'assistant'], true) ? $turn['role'] : 'user',
                'content' => (string) ($turn['content'] ?? ''),
            ],
            $history,
        );

        $sanitised[] = ['role' => 'user', 'content' => $prompt];

        return $sanitised;
    }
}
