<?php

namespace Modules\EMAILMARKETING\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\EMAILMARKETING\Interfaces\NewsletterRepositoryInterface;
use Modules\EMAILMARKETING\Models\EmailMarketingEvent;
use Modules\EMAILMARKETING\Models\Newsletter;
use Modules\EMAILMARKETING\Requests\ImportSubscribersRequest;
use Modules\EMAILMARKETING\Requests\NewsletterRequest;
use Modules\EMAILMARKETING\Requests\SubscribeNewsletterRequest;

class NewsletterController extends Controller
{
    public function __construct(private readonly NewsletterRepositoryInterface $newsletters) {}

    public function store(NewsletterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ip_address'] = $request->ip();
        $this->newsletters->create($data);

        return to_route('emailmarketing.index', ['tab' => 'subscribers'])->with('success', 'Subscriber added successfully.');
    }

    public function update(NewsletterRequest $request, Newsletter $newsletter): RedirectResponse
    {
        $this->newsletters->update($newsletter, $request->validated());

        return back()->with('success', 'Subscriber updated successfully.');
    }

    public function destroy(Request $request, Newsletter $newsletter): RedirectResponse
    {
        abort_unless($request->user()?->can('emailmarketing.delete'), 403);
        $this->newsletters->delete($newsletter);

        return back()->with('success', 'Subscriber deleted successfully.');
    }

    public function import(ImportSubscribersRequest $request): RedirectResponse
    {
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'rb');
        $imported = 0;
        $skipped = 0;
        $headers = [];

        if ($handle) {
            while (($row = fgetcsv($handle)) !== false) {
                $row = array_map(fn ($value): string => trim((string) $value), $row);
                if ($row === [] || collect($row)->filter()->isEmpty()) {
                    continue;
                }

                if ($headers === []) {
                    $lower = array_map(fn ($value): string => mb_strtolower($value), $row);
                    if (in_array('email', $lower, true)) {
                        $headers = $lower;
                        continue;
                    }

                    $headers = ['email', 'name', 'phone', 'audience_type', 'source', 'country', 'tags'];
                }

                $rowHeaders = array_slice(array_pad($headers, count($row), 'extra'), 0, count($row));
                $payload = array_combine($rowHeaders, $row) ?: [];
                $email = Arr::get($payload, 'email', $row[0] ?? null);

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    continue;
                }

                $tags = Arr::get($payload, 'tags');
                $this->newsletters->upsertSubscriber([
                    'email' => $email,
                    'name' => Arr::get($payload, 'name'),
                    'phone' => Arr::get($payload, 'phone'),
                    'audience_type' => Arr::get($payload, 'audience_type') ?: $request->input('default_audience_type'),
                    'source' => Arr::get($payload, 'source') ?: $request->input('default_source', 'csv-import'),
                    'country' => Arr::get($payload, 'country') ?: $request->input('default_country'),
                    'tags' => is_string($tags) && $tags !== '' ? array_values(array_filter(array_map('trim', explode(',', $tags)))) : [],
                    'ip_address' => $request->ip(),
                ]);
                $imported++;
            }

            fclose($handle);
        }

        return back()->with('success', "Imported {$imported} subscriber(s). Skipped {$skipped} row(s).");
    }

    public function subscribe(SubscribeNewsletterRequest $request): RedirectResponse|JsonResponse
    {
        $newsletter = $this->newsletters->upsertSubscriber(array_merge($request->validated(), [
            'ip_address' => $request->ip(),
            'source' => $request->input('source', 'website'),
        ]));

        EmailMarketingEvent::create([
            'newsletter_id' => $newsletter->id,
            'email' => $newsletter->email,
            'type' => 'subscribe',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Subscription successful.', 'subscriber' => $newsletter]);
        }

        return back()->with('success', 'Thank you for subscribing.');
    }

    public function unsubscribe(Request $request, Newsletter $newsletter, string $token): RedirectResponse|JsonResponse
    {
        abort_unless(hash_equals((string) $newsletter->unsubscribe_token, $token), 403);

        $newsletter->markUnsubscribed();
        EmailMarketingEvent::create([
            'newsletter_id' => $newsletter->id,
            'email' => $newsletter->email,
            'type' => 'unsubscribe',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'You have been unsubscribed.']);
        }

        return redirect('/')->with('success', 'You have been unsubscribed from marketing emails.');
    }
}
