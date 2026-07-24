<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\HEALTHAI\Services\ExcelService;

final class ExcelResolver implements DataResolverInterface
{
    private const GENERIC_KEYWORDS = [
        // English
        'test', 'list', 'all', 'everything', 'shob',
        // Bengali
        'তালিকা', 'সব', 'সকল', 'মূল্য তালিকা', 'টেস্টের মূল্য',
        'দেখুন', 'চাই', 'মূল্য',
    ];

    /**
     * Words to strip from the query before searching.
     */
    private const STRIP_WORDS = [
        'test', 'tests', 'price', 'cost', 'charge', 'rate',
        'মূল্য', 'দাম', 'খরচ', 'কত', 'টাকা', 'taka', 'tk',
        'korbe', 'korte', 'lagbe', 'hobe', 'ase', 'ache',
        'ei', 'ei ta', 'ei ti', 'eguli', 'egulo',
        'testguli', 'testgulo', 'test guló',
        'দেখুন', 'চাই', 'জানতে', 'বলুন',
    ];

    public function __construct(private readonly ExcelService $excelService) {}

    public function resolve(string $keyword): array
    {
        $clean = KeywordHelper::clean($keyword, self::STRIP_WORDS);

        if (KeywordHelper::isGeneric($clean)) {
            return [[
                'Notice' => 'আমাদের AMZ হাসপাতালে প্যাথলজি, বায়োকেমিস্ট্রি, ইমেজিং সহ সব ধরণের টেস্টের সুব্যবস্থা রয়েছে। '
                          . 'সুনির্দিষ্ট টেস্টের নাম (যেমন: CBC, Lipid Profile, USG, X-Ray, MRI) বললে আমি এখনই মূল্য জানিয়ে দিচ্ছি।',
            ]];
        }

        // Split multi-test queries: "CBC, TSH, Serum Creatinine" → ["CBC", "TSH", "Serum Creatinine"]
        $testNames = $this->splitTestNames($clean);

        if (count($testNames) <= 1) {
            // Single test — search directly
            $results = $this->excelService->searchTestPrice($clean);

            return $results ?: [[
                'Notice' => "'{$clean}' টেস্টের মূল্য ডাটাবেজে পাওয়া যায়নি। অনুগ্রহ করে টেস্টের পুরো নাম বা ইংরেজি নাম দিয়ে আবার চেষ্টা করুন। বিস্তারিত জানতে হটলাইন ১০৬৯৯-এ কল করুন।",
            ]];
        }

        // Multiple tests — search each one individually
        $allResults = [];
        $notFound   = [];

        foreach ($testNames as $testName) {
            $trimmed = trim($testName);
            if (empty($trimmed) || mb_strlen($trimmed) < 2) continue;

            $results = $this->excelService->searchTestPrice($trimmed);

            if (!empty($results)) {
                foreach ($results as $r) {
                    $allResults[] = $r;
                }
            } else {
                $notFound[] = $trimmed;
            }
        }

        if (empty($allResults) && !empty($notFound)) {
            $names = implode(', ', $notFound);
            return [[
                'Notice' => "নিম্নলিখিত টেস্টগুলোর মূল্য ডাটাবেজে পাওয়া যায়নি: {$names}। অনুগ্রহ করে টেস্টের সঠিক ইংরেজি নাম দিয়ে আবার চেষ্টা করুন। বিস্তারিত জানতে হটলাইন ১০৬৯৯-এ কল করুন।",
            ]];
        }

        // Return found results (LLM will also mention any not-found ones from context)
        if (!empty($notFound)) {
            // Prepend a notice about not-found tests
            array_unshift($allResults, [
                'Notice' => 'নিম্নলিখিত টেস্টগুলোর মূল্য পাওয়া যায়নি: ' . implode(', ', $notFound) . '। বাকিগুলোর মূল্য নিচে দেওয়া হলো।',
            ]);
        }

        return $allResults;
    }

    /**
     * Split a multi-test query into individual test names.
     * Handles: commas, "and", "এবং", "আর", semicolons, pipes
     */
    private function splitTestNames(string $query): array
    {
        // Replace common separators with a standard delimiter
        $query = preg_replace('/\s+and\s+/i', '|', $query);
        $query = str_ireplace([' এবং ', ' আর ', ';'], '|', $query);

        // Split by comma and pipe
        $parts = preg_split('/[,|]/u', $query);

        // Clean and filter empty/short parts
        $names = [];
        foreach ($parts as $part) {
            $trimmed = trim($part);
            if (mb_strlen($trimmed) >= 2) {
                $names[] = $trimmed;
            }
        }

        return $names;
    }
}
