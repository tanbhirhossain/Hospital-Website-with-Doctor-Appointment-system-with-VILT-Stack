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
            AgentIntent::LAB_REPORT->value          => '/(report|lab report|lab result|test result|রিপোর্ট|ল্যাব রিপোর্ট|টেস্ট রেজাল্ট|report check|report dekhte|amar report)/i',
            AgentIntent::DIET_PLAN->value           => '/(diet|diet plan|food plan|meal plan|খাবারের তালিকা|ডায়েট|ডায়েট প্ল্যান|khabor plan|khabar)/i',
            AgentIntent::HEALTH_TIPS->value         => '/(betha|pain|problem|symptom|sick|ill|ব্যথা|সমস্যা|অসুখ|ব্যাথা|পরামর্শ|tips|upay|উপায়|treatment|remedy|leg pain|headache|matha|pet|stomach|allergy|cough|kashi|jor|fever)/i',
            AgentIntent::DATABASE_DEPARTMENT->value => '/(bivag|department|ward|বিভাগ|ডিপার্টমেন্ট)/i',
            AgentIntent::DATABASE_DOCTOR->value     => '/(doctor|daktar|ডাক্তার|ডাক্টার|DR|Dr|Doctor Visit fee)/i',
            AgentIntent::EXCEL->value               => '/(test|price|mulla|tk|taka|খরচ|মূল্য|টেস্ট)/i',
            AgentIntent::API_BEDS->value            => '/(bed|seat|সিট|বেড|খালি)/i',
            AgentIntent::API_PHARMACY->value        => '/(medicine|pharmacy|napa|maxpro|kolchine|tablet|capsule|ঔষধ|ওষুধ|ফার্মেসি|ase to|ache to|আছে তো|আছে কি|kibolen|সিরপ|ট্যাবলেট|ক্যাপসুল|ইনজেকশন)/i',
            AgentIntent::DATABASE_COE->value        => '/(coe|center of excellence|এক্সেলেন্স|সেন্টার)/i',
        ];

        foreach ($patterns as $intentValue => $pattern) {
            if (preg_match($pattern, $lower)) {
                $intent = AgentIntent::from($intentValue);

                // Keyword logic per intent
                $keyword = match ($intent) {
                    AgentIntent::API_PHARMACY => $this->extractPharmacyKeyword($lower),
                    AgentIntent::HEALTH_TIPS  => trim($message),    // full message for health tips
                    AgentIntent::DIET_PLAN    => trim($message),    // full message for diet plan
                    AgentIntent::LAB_REPORT   => trim($message),    // full message for lab report parsing
                    default                   => 'all',
                };

                return RoutingDecision::make($intent, $keyword);
            }
        }

        // ── Final catch-all: "X ase/ache/আছে" = pharmacy availability query ──
        // Catches any medicine brand name + availability question
        // e.g. "kibolen ase?", "paracetamol ache?", "seclo ki ase?"
        if (preg_match('/\b(ase|ache|আছে)\b/u', $lower)) {
            $keyword = $this->extractPharmacyKeyword($lower);
            if (!empty($keyword)) {
                Log::info('[IntentRouter] Catch-all pharmacy match', ['keyword' => $keyword]);
                return RoutingDecision::make(AgentIntent::API_PHARMACY, $keyword);
            }
        }

        return null;
    }

    private function extractPharmacyKeyword(string $lower): string
    {
        $stripped = trim(str_ireplace(
            [
                // English
                'medicine', 'pharmacy', 'price', 'ase', 'ache', 'ki',
                'apnader', 'ekhane', 'find', 'search', 'available',
                'tablet', 'capsule', 'syrup', 'injection', 'tab', 'cap',
                'to', 'na', 'do', 'please',
                // Bengali
                'ঔষধ', 'ওষুধ', 'ফার্মেসি', 'আছে', 'কি', 'কী', 'নাকি',
                'খুঁজুন', 'খুঁজছি', 'খুঁজতে', 'চাই', 'দরকার', 'লাগবে',
                'দেখুন', 'দাও', 'বলুন', 'জানান', 'কত', 'দাম', 'মূল্য',
                'ট্যাবলেট', 'ক্যাপসুল', 'সিরাপ', 'ইনজেকশন',
                'জাতীয়', 'জাতি', 'ধরনের', 'রকম', 'তো',
                'ami', 'amar', 'amake', 'ekta', 'kichu',
                '?', '।', '!',
            ],
            '',
            $lower,
        ));

        // If nothing remains after stripping, the original message IS the medicine name
        if (empty($stripped)) {
            // Return the original message with just common fillers removed
            return trim(str_ireplace(
                ['medicine', 'pharmacy', 'ঔষধ', 'ওষুধ', 'ফার্মেসি', '?', '।'],
                '',
                $lower,
            ));
        }

        return $stripped;
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
