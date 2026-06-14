<?php

namespace Modules\HEALTHAI\Services\Agent;

use Illuminate\Support\Facades\Log;
use Modules\HEALTHAI\Enums\AgentIntent;
use Modules\HEALTHAI\Prompts\AgentPrompts;
use Modules\WEBSITE\Services\COEService;
use Modules\WEBSITE\Services\DepartmentService;

final class IntentRouter
{
    public function __construct(
        private readonly OllamaClient      $ollama,
        private readonly DepartmentService $departmentService,
        private readonly COEService        $coeService,
    ) {}

    /**
     * @param  array<int, array{role: string, content: string}> $history
     */
    public function resolve(string $userMessage, array $history = []): RoutingDecision
    {
        $availableDepartments = $this->getDepartmentNames();
        $availableCOEs        = $this->getCoeNames();

        $prompt = AgentPrompts::routing($userMessage, $availableDepartments, $availableCOEs);

        // Pass history so router LLM can resolve pronouns / follow-up messages
        $parsed = $this->ollama->chatJson($prompt, $history);

        $intent  = AgentIntent::fromString($parsed['intent'] ?? '');
        $keyword = trim($parsed['search_keyword'] ?? '');

        Log::info('[IntentRouter] LLM decision', [
            'user_message' => $userMessage,
            'intent'       => $intent->value,
            'keyword'      => $keyword,
        ]);

        // PHP hard-fallback if LLM returns GENERAL or an empty intent
        if ($intent === AgentIntent::GENERAL || empty($keyword)) {
            $fallback = $this->phpFallback($userMessage);
            if ($fallback !== null) {
                Log::info('[IntentRouter] PHP fallback applied', ['fallback_intent' => $fallback->intent->value]);
                return $fallback;
            }
        }

        return RoutingDecision::make($intent, $keyword);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function phpFallback(string $message): ?RoutingDecision
    {
        $lower = mb_strtolower($message);

        $patterns = [
            AgentIntent::BOOK_APPOINTMENT->value    => '/(appointment|apointment|book|bookings|সিরিয়াল|এপয়েন্টমেন্ট|অ্যাপয়েন্টমেন্ট|ডাক্তার দেখাতে|ডাক্তার দেখাবো)/i',
            AgentIntent::DATABASE_DEPARTMENT->value => '/(bivag|department|ward|বিভাগ|ডিপার্টমেন্ট)/i',
            AgentIntent::DATABASE_DOCTOR->value     => '/(doctor|daktar|ডাক্তার)/i',
            AgentIntent::EXCEL->value               => '/(test|price|mulla|tk|taka|খরচ|মূল্য|টেস্ট)/i',
            AgentIntent::API_BEDS->value            => '/(bed|seat|সিট|বেড|খালি)/i',
            AgentIntent::API_PHARMACY->value        => '/(medicine|pharmacy|napa|maxpro|kolchine|tablet|capsule|ঔষধ|ওষুধ|ফার্মেসি)/i',
            AgentIntent::DATABASE_COE->value        => '/(coe|center of excellence|এক্সেলেন্স|সেন্টার)/i',
        ];

        foreach ($patterns as $intentValue => $pattern) {
            if (preg_match($pattern, $lower)) {
                $intent  = AgentIntent::from($intentValue);
                $keyword = $intent === AgentIntent::API_PHARMACY
                    ? $this->extractPharmacyKeyword($lower)
                    : 'all';
                return RoutingDecision::make($intent, $keyword);
            }
        }

        return null;
    }

    private function extractPharmacyKeyword(string $lower): string
    {
        return trim(str_ireplace(
            ['medicine', 'pharmacy', 'price', 'ase', 'ache', 'ki', 'apnader', 'ekhane'],
            '',
            $lower,
        ));
    }

    private function getDepartmentNames(): string
    {
        $result = $this->departmentService->getAllDepartmentsForAI();
        $items  = $this->unwrapPaginator($result);
        return collect($items)->pluck('name')->implode(', ');
    }

    private function getCoeNames(): string
    {
        return collect($this->coeService->getAllCOEs())->pluck('name')->implode(', ');
    }

    /** @return array<mixed> */
    private function unwrapPaginator(mixed $result): array
    {
        if (is_object($result) && method_exists($result, 'items')) {
            return $result->items();
        }
        return is_array($result) ? $result : iterator_to_array($result);
    }
}
