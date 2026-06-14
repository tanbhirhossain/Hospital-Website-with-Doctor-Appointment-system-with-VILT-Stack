<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\HEALTHAI\Services\ExcelService;

final class ExcelResolver implements DataResolverInterface
{
    private const GENERIC_KEYWORDS = ['test', 'list', 'all', 'everything', 'shob'];

    public function __construct(private readonly ExcelService $excelService) {}

    public function resolve(string $keyword): array
    {
        if (empty($keyword) || in_array(mb_strtolower(trim($keyword)), self::GENERIC_KEYWORDS, true)) {
            return [[
                'Notice' => 'আমাদের AMZ হাসপাতালে প্যাথলজি, বায়োকেমিস্ট্রি, ইমেজিং সহ সব ধরণের টেস্টের সুব্যবস্থা রয়েছে। '
                          . 'সুনির্দিষ্ট টেস্টের নাম (যেমন: CBC, Lipid Profile, USG) বললে আমি এখনই মূল্য জানিয়ে দিচ্ছি।',
            ]];
        }

        $results = $this->excelService->searchTestPrice($keyword);

        return $results ?: [[
            'Notice' => 'টেস্টের মূল্য ডাটাবেজে পাওয়া যায়নি। বিস্তারিত জানতে হটলাইনে কল করুন।',
        ]];
    }
}
