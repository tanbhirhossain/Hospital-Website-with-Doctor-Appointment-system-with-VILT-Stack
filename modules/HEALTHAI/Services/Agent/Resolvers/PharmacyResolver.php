<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\HEALTHAI\Services\MISDataService;

final class PharmacyResolver implements DataResolverInterface
{
    private const STRIP_WORDS = [
        'medicine', 'pharmacy', 'price', 'ase', 'ache', 'ki', 'apnader', 'ekhane',
        'tablet', 'capsule', 'ঔষধ', 'ওষুধ', 'ফার্মেসি', 'ট্যাবলেট', 'আছে', 'কি', 'নাকি',
        '?', '.', '"', "'",
    ];

    public function __construct(private readonly MISDataService $misService) {}

    public function resolve(string $keyword): array
    {
        $clean = KeywordHelper::clean($keyword, self::STRIP_WORDS);
        $data  = KeywordHelper::unwrapJsonResponse($this->misService->searchPharmacy($clean));

        if (empty($data) || isset($data['error'])) {
            return [[
                'Notice' => "দুঃখিত, ফার্মেসির স্টকে '{$clean}' ওষুধটির তথ্য পাওয়া যায়নি।",
            ]];
        }

        return [['Type' => 'Pharmacy Medicine Status', 'Keyword' => $clean, 'Data' => $data]];
    }
}
