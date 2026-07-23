<?php

namespace Modules\APPOINTMENT\Notifications;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public int $tries = 3;
    public int $timeout = 30;
    public int $backoff = 60;


    public function __construct(
        protected string $message,
        protected string $phoneNumber
    ) {}

    public function handle(): void
    {
        try {

            $url = env('SMS_PROVIDER_URL');

            if (blank($url)) {
                Log::error('SMS API URL is not configured.');
                return;
            }

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->post(env('SMS_PROVIDER_URL'), [
                    'api_key'  => env('SMS_API_KEY'),
                    'type'     => 'text',
                    'contacts' => $this->phoneNumber,
                    'senderid' => env('SMS_SENDER_ID'),
                    'msg'      => $this->message,
                ]);

            Log::info('SMS Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {

                Log::info('SMS Sent Successfully', [
                    'phone' => $this->phoneNumber,
                    'response' => $response->body(),
                ]);

                return;
            }

            Log::error('SMS API Failed', [
                'phone' => $this->phoneNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

        } catch (\Throwable $e) {

            Log::error('SMS Sending Exception', [
                'phone' => $this->phoneNumber,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}