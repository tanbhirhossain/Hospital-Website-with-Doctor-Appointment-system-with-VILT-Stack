<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Illuminate\Support\Facades\Log;
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
        // Bengali filler words
        'খুঁজুন', 'খুঁজছি', 'খুঁজতে', 'চাই', 'দরকার', 'লাগবে',
        'দেখুন', 'দাও', 'বলুন', 'জানান', 'আছে', 'কি', 'কী', 'নাকি',
        'ঔষধ', 'ওষুধ', 'ফার্মেসি', 'জাতীয়', 'জাতি', 'ধরনের', 'রকম',
        'কত', 'দাম', 'মূল্য', 'টাকা',
        // Generic query words
        'medicine', 'pharmacy', 'price', 'ase', 'ache', 'ki',
        'apnader', 'ekhane', 'find', 'search', 'available',
        'ami', 'amar', 'amake', 'ekta', 'kichu',
        // Punctuation
        '?', '.', '"', "'", '।', '!',
    ];

    public function __construct(private readonly MISDataService $misService) {}

    public function resolve(string $keyword): array
    {
        $clean = $this->clean($keyword);

        if (empty($clean)) {
            return [['Notice' => 'কোন ওষুধের নাম বলুন, আমি স্টক ও মূল্য জানাব। উদাহরণ: "Napa", "Seclo", "Calboral"']];
        }

        // Try the exact cleaned keyword first
        $data = KeywordHelper::unwrapJsonResponse($this->misService->searchPharmacy($clean));

        if (!empty($data) && !isset($data['error'])) {
            return [['Type' => 'Pharmacy Medicine Status', 'Keyword' => $clean, 'Data' => $data]];
        }

        // If exact match fails, try individual words (for multi-word queries like "calcium tablet")
        $words = array_filter(
            explode(' ', $clean),
            fn ($w) => mb_strlen($w) >= 3
        );

        if (count($words) > 1) {
            // Try each significant word separately
            foreach ($words as $word) {
                $wordData = KeywordHelper::unwrapJsonResponse($this->misService->searchPharmacy($word));
                if (!empty($wordData) && !isset($wordData['error'])) {
                    return [['Type' => 'Pharmacy Medicine Status', 'Keyword' => $word, 'Data' => $wordData]];
                }
            }

            // Try the first word (usually the medicine brand/generic name)
            $firstWord = reset($words);
            if ($firstWord !== $clean) {
                $firstData = KeywordHelper::unwrapJsonResponse($this->misService->searchPharmacy($firstWord));
                if (!empty($firstData) && !isset($firstData['error'])) {
                    return [['Type' => 'Pharmacy Medicine Status', 'Keyword' => $firstWord, 'Data' => $firstData]];
                }
            }
        }

        return [['Notice' => "ফার্মেসির স্টকে '{$clean}' ওষুধটির তথ্য পাওয়া যায়নি। ওষুধের ইংরেজি নাম বা ব্র্যান্ড নাম দিয়ে আবার চেষ্টা করুন। অথবা হটলাইন ১০৬৯৯-এ কল করুন।"]];
    }

    private function clean(string $input): string
    {
        $lower = mb_strtolower(trim($input));

        // Strip each word / phrase with a word-boundary-aware replacement
        foreach (self::STRIP_WORDS as $word) {
            $lower = preg_replace('/\b' . preg_quote($word, '/') . '\b/ui', '', $lower);
        }

        // Also use KeywordHelper's Bengali strip words
        foreach (KeywordHelper::BENGALI_STRIP as $word) {
            $lower = preg_replace('/\b' . preg_quote($word, '/') . '\b/ui', '', $lower);
        }

        return trim(preg_replace('/\s+/u', ' ', $lower));
    }
}
