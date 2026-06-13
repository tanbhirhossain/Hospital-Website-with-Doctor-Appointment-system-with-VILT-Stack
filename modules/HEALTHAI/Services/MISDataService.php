<?php 

namespace Modules\HEALTHAI\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MISDataService
{
    public function getBeds()
    {
        $response = Http::withToken(env('HOST_API_TOKEN'))
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get('http://erp.amzhospitalbd.com/misdata/api/beds');

        if ($response->successful()) {
            return $response->json(); // This contains your bed data
        }

        return response()->json(['error' => 'Unauthorized or API error'], $response->status());
    }

    public function getPharmacy(Request $request){
        $response = Http::withToken(env('HOST_API_TOKEN'))
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get('http://erp.amzhospitalbd.com/misdata/api/medicines?search='.$request->query('search'));

        if ($response->successful()) {
            return $response->json(); // This contains your pharmacy data
        }

        return response()->json(['error' => 'Unauthorized or API error'], $response->status());
    }
}