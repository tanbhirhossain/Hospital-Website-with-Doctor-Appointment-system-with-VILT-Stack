<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;
use Modules\WEBSITE\Services\DepartmentService;

final class DepartmentResolver implements DataResolverInterface
{
    private const STRIP_WORDS = ['department', 'bivag', 'ডিপার্টমেন্ট', 'বিভাগ', '"', "'", '?'];

    public function __construct(private readonly DepartmentService $departmentService) {}

    public function resolve(string $keyword): array
    {
        $clean       = KeywordHelper::clean($keyword, self::STRIP_WORDS);
        $departments = KeywordHelper::unwrapPaginator($this->departmentService->getAllDepartmentsForAI());
        $isGeneric   = KeywordHelper::isGeneric($clean);

        $matched = array_filter(
            $departments,
            fn ($d) => $isGeneric || stripos($d->name, $clean) !== false,
        );

        // Fallback to full list if specific search yields nothing
        $list = !empty($matched) ? $matched : $departments;

        return array_values(array_map(
            fn ($dept) => ['Type' => 'Department', 'Name' => $dept->name],
            $list,
        ));
    }
}
