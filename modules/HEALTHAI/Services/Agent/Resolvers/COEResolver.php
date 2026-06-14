<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\WEBSITE\Services\COEService;

final class COEResolver implements DataResolverInterface
{
    private const STRIP_WORDS = ['center of excellence', 'center', 'excellence', 'coe', 'সেন্টার', 'এক্সেলেন্স', '?'];

    public function __construct(private readonly COEService $coeService) {}

    public function resolve(string $keyword): array
    {
        $clean     = KeywordHelper::clean($keyword, self::STRIP_WORDS);
        $coes      = (array) $this->coeService->getAllCOEs();
        $isGeneric = KeywordHelper::isGeneric($clean);

        $matched = array_filter($coes, fn ($c) => $isGeneric || stripos($c->name, $clean) !== false);
        $list    = !empty($matched) ? $matched : $coes;

        return array_values(array_map(fn ($coe) => [
            'Type'        => 'Center of Excellence',
            'Name'        => $coe->name,
            'Description' => strip_tags($coe->description ?? 'Specialized medical center of AMZ Hospital.'),
        ], $list));
    }
}
