<?php 

namespace Modules\HEALTHAI\Services;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExcelService
{
   public function searchTestPrice(string $query): array
    {
        $filePath = storage_path('app/shared/test_prices.xlsx');
        
        if (!file_exists($filePath)) {
            \Log::error("Excel file not found at: " . $filePath);
            return [];
        }

        $data = Excel::toArray([], $filePath);
        $rows = $data[0] ?? [];

        array_shift($rows); // হেডার বাদ দেওয়া

        // 💡 ফিক্স: ইউজার ও ওল্ল্যামা থেকে আসা কুয়েরিকে নরমাল করা (x-ray, xray, x ray সব এক হয়ে যাবে)
        $cleanQuery = trim(str_replace(['"', "'", '-', ' '], '', $query));

        if (empty($cleanQuery)) {
            return [];
        }

        $filteredResults = array_filter($rows, function($row) use ($cleanQuery) {
            $testName = $row[1] ?? ''; 
            
            // 💡 ফিক্স: এক্সেল ফাইলের নাম থেকেও হাইফেন ও স্পেস তুলে নিয়ে ম্যাচ করানো
            $normalizedTestName = str_replace(['-', ' '], '', $testName);

            return stripos($normalizedTestName, $cleanQuery) !== false;
        });

        // ওল্ল্যামার জন্য ডেটা ফরম্যাট করা
        $formattedData = [];
        foreach ($filteredResults as $row) {
            $formattedData[] = [
                'code' => $row[0] ?? 'N/A',
                'test_name' => $row[1] ?? 'N/A',
                'charge' => $row[2] ?? '0',
                'discount' => ($row[4] ?? '0') . ' ' . ($row[5] ?? ''),
                'report_department' => $row[6] ?? 'N/A'
            ];
        }

        return array_values($formattedData);
    }
}