<?php

namespace Modules\HEALTHAI\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MISDataService
{
    private string $baseUrl = 'http://erp.amzhospitalbd.com/misdata/api';

    private function token(): string
    {
        return config('healthai.mis_api_token', env('HOST_API_TOKEN', ''));
    }

    private function get(string $path, array $query = []): mixed
    {
        try {
            $response = Http::withToken($this->token())
                ->withHeaders(['Accept' => 'application/json'])
                ->get($this->baseUrl . $path, $query);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('[MISDataService] API error', [
                'path'   => $path,
                'status' => $response->status(),
            ]);

            return ['error' => 'API returned ' . $response->status()];
        } catch (\Throwable $e) {
            Log::error('[MISDataService] Request failed', ['path' => $path, 'error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get live bed availability from MIS ERP.
     */
    public function getBeds(): mixed
    {
        return $this->get('/beds');
    }

    /**
     * Search pharmacy/medicine stock by keyword.
     * Clean, keyword-first API — no Request object needed.
     */
    public function searchPharmacy(string $keyword): mixed
    {
        return $this->get('/medicines', ['search' => trim($keyword)]);
    }
}
