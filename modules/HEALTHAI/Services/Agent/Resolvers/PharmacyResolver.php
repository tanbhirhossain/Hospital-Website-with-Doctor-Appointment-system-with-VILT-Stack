<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\HEALTHAI\Services\MISDataService;

final class PharmacyResolver implements DataResolverInterface
{
    /**
     * Words to strip before hitting the pharmacy API.
     * Order matters — longer phrases first to avoid partial replacements.
     */
    private const STRIP_WORDS = [
        // Dosage forms (strip so "sergel inj" → "sergel")
        'injection', 'tablet', 'capsule', 'syrup', 'cream', 'ointment',
        'suspension', 'solution', 'drops', 'suppository', 'inhaler',
        'inj', 'tab', 'cap', 'syr', 'susp', 'inf',
        // Bengali dosage forms
        'ইনজেকশন', 'ট্যাবলেট', 'ক্যাপসুল', 'সিরাপ', 'ক্রিম',
        // Generic query words
        'medicine', 'pharmacy', 'price', 'ase', 'ache', 'ki',
        'apnader', 'ekhane', 'আছে', 'কি', 'নাকি', 'ঔষধ', 'ওষুধ', 'ফার্মেসি',
        // Punctuation
        '?', '.', '"', "'",
    ];

    public function __construct(private readonly MISDataService $misService) {}

    public function resolve(string $keyword): array
    {
        $clean = $this->clean($keyword);

        if (empty($clean)) {
            return [['Notice' => 'কোন ওষুধের নাম বলুন, আমি স্টক ও মূল্য জানাব।']];
        }

        $data = KeywordHelper::unwrapJsonResponse($this->misService->searchPharmacy($clean));

        if (empty($data) || isset($data['error'])) {
            return [['Notice' => "ফার্মেসির স্টকে '{$clean}' ওষুধটির তথ্য পাওয়া যায়নি।"]];
        }

        return [['Type' => 'Pharmacy Medicine Status', 'Keyword' => $clean, 'Data' => $data]];
    }

    private function clean(string $input): string
    {
        $lower = mb_strtolower(trim($input));

        // Strip each word / phrase with a word-boundary-aware replacement
        foreach (self::STRIP_WORDS as $word) {
            $lower = preg_replace('/\b' . preg_quote($word, '/') . '\b/ui', '', $lower);
        }

        return trim(preg_replace('/\s+/', ' ', $lower));
    }
}
