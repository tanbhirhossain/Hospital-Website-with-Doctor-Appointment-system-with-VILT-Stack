<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\HEALTHAI\Services\Agent\DataResolverInterface;

final class LabReportResolver implements DataResolverInterface
{
    private const LAB_API_BASE   = 'http://192.168.10.215/api/lab';
    private const REPORT_VIEW_URL = 'http://erp.amzhospitalbd.com/lab';

    public function resolve(string $keyword): array
    {
        // Extract phone and invoice number from the keyword/message
        $phone   = $this->extractPhone($keyword);
        $invNo   = $this->extractInvoiceNo($keyword);

        // If we don't have both, ask for them
        if (empty($phone) && empty($invNo)) {
            return [[
                'Type'    => 'Lab Report Lookup',
                'Status'  => 'NEED_INFO',
                'Notice'  => 'আপনার ল্যাব রিপোর্ট দেখতে মোবাইল নম্বর এবং ইনভয়েস নম্বর প্রয়োজন। অনুগ্রহ করে দুটি তথ্য দিন। উদাহরণ: "রিপোর্ট দেখুন 01XXXXXXXXX 12345"',
            ]];
        }

        if (empty($phone)) {
            return [[
                'Type'    => 'Lab Report Lookup',
                'Status'  => 'NEED_INFO',
                'InvNo'   => $invNo,
                'Notice'  => "ইনভয়েস নম্বর ({$invNo}) পেয়েছি। এবার আপনার মোবাইল নম্বর দিন।",
            ]];
        }

        if (empty($invNo)) {
            return [[
                'Type'    => 'Lab Report Lookup',
                'Status'  => 'NEED_INFO',
                'Phone'   => $phone,
                'Notice'  => "মোবাইল নম্বর ({$phone}) পেয়েছি। এবার ইনভয়েস নম্বর দিন।",
            ]];
        }

        // Both found — call the external API
        try {
            $response = Http::timeout(15)->get(self::LAB_API_BASE . '/' . $phone . '/' . $invNo);

            if ($response->successful()) {
                $data = $response->json();

                if (! empty($data['matched']) && $data['matched'] === true) {
                    return [[
                        'Type'           => 'Lab Report Found',
                        'Status'         => 'FOUND',
                        'PatientName'    => trim($data['PatientName'] ?? ''),
                        'InvDate'        => $data['InvDate'] ?? '',
                        'MobileNo'       => $data['MobileNo'] ?? $phone,
                        'InvNo'          => $invNo,
                        'ReportLink'     => self::REPORT_VIEW_URL . '/' . $invNo,
                    ]];
                }

                return [[
                    'Type'    => 'Lab Report Lookup',
                    'Status'  => 'NOT_FOUND',
                    'Notice'  => 'দুঃখিত, এই তথ্য দিয়ে কোনো রিপোর্ট পাওয়া যায়নি। অনুগ্রহ করে মোবাইল নম্বর ও ইনভয়েস নম্বর যাচাই করে আবার চেষ্টা করুন।',
                ]];
            }

            Log::warning('[LabReportResolver] API returned non-success', ['status' => $response->status()]);

            return [[
                'Type'    => 'Lab Report Lookup',
                'Status'  => 'ERROR',
                'Notice'  => 'এই মুহূর্তে রিপোর্ট সিস্টেমে সংযোগ করতে সমস্যা হচ্ছে। অনুগ্রহ করে কিছুক্ষণ পরে আবার চেষ্টা করুন অথবা রিসেপশনে যোগাযোগ করুন।',
            ]];

        } catch (\Throwable $e) {
            Log::error('[LabReportResolver] API call failed', ['error' => $e->getMessage()]);

            return [[
                'Type'    => 'Lab Report Lookup',
                'Status'  => 'ERROR',
                'Notice'  => 'রিপোর্ট সার্ভারে সংযোগ করতে সমস্যা হচ্ছে। অনুগ্রহ করে পরে আবার চেষ্টা করুন।',
            ]];
        }
    }

    /**
     * Extract Bangladeshi phone number from text.
     * Matches: 01XXXXXXXXX, +8801XXXXXXXXX, 8801XXXXXXXXX
     */
    private function extractPhone(string $text): string
    {
        // Pattern: 01XXXXXXXXX (11 digits starting with 01)
        if (preg_match('/(?:\+?88)?(01[3-9]\d{8})/', $text, $m)) {
            return $m[1];
        }
        return '';
    }

    /**
     * Extract invoice number from text.
     * Looks for numeric strings that could be invoice numbers.
     */
    private function extractInvoiceNo(string $text): string
    {
        // Remove the phone number first to avoid confusion
        $text = preg_replace('/(?:\+?88)?01[3-9]\d{8}/', '', $text);

        // Look for standalone numbers (3+ digits) that could be invoice numbers
        if (preg_match('/\b(\d{3,10})\b/', $text, $m)) {
            return $m[1];
        }

        return '';
    }
}
