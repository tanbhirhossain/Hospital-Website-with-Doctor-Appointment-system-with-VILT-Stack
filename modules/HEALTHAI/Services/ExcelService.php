<?php

namespace Modules\HEALTHAI\Services;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExcelService
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('app/shared/test_prices.xlsx');
    }

    /**
     * Search test/investigation prices from the Excel price list.
     *
     * Normalizes both query and test names by stripping hyphens and spaces,
     * so "x-ray", "xray", and "x ray" all match correctly.
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchTestPrice(string $query): array
    {
        if (!file_exists($this->filePath)) {
            Log::error('[ExcelService] Price list file not found', ['path' => $this->filePath]);
            return [];
        }

        // Normalize query: remove hyphens, spaces, and quotes
        $normalizedQuery = $this->normalize($query);

        if (empty($normalizedQuery)) {
            return [];
        }

        $data = Excel::toArray([], $this->filePath);
        $rows = $data[0] ?? [];
        array_shift($rows); // Remove header row

        $results = [];
        foreach ($rows as $row) {
            $testName = $row[1] ?? '';
            if (stripos($this->normalize($testName), $normalizedQuery) !== false) {
                $results[] = [
                    'code'              => $row[0] ?? 'N/A',
                    'test_name'         => $testName,
                    'charge'            => $row[2] ?? '0',
                    'discount'          => trim(($row[4] ?? '0') . ' ' . ($row[5] ?? '')),
                    'report_department' => $row[6] ?? 'N/A',
                ];
            }
        }

        return $results;
    }

    private function normalize(string $value): string
    {
        return str_replace(['-', ' ', '"', "'"], '', $value);
    }
}
