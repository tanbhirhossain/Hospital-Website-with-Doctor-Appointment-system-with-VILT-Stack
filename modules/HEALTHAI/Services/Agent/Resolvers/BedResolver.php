<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\HEALTHAI\Services\MISDataService;

final class BedResolver implements DataResolverInterface
{
    public function __construct(private readonly MISDataService $misService) {}

    public function resolve(string $keyword): array
    {
        $data = KeywordHelper::unwrapJsonResponse($this->misService->getBeds());

        if (empty($data) || isset($data['error'])) {
            return [[
                'Notice' => 'এই মুহূর্তে বেডের লাইভ স্ট্যাটাস পাওয়া যাচ্ছে না। অনুগ্রহ করে হটলাইনে কল করুন।',
            ]];
        }

        return [['Type' => 'Live Bed Status', 'Data' => $data]];
    }
}
