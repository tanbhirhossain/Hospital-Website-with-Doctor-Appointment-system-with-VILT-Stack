<?php

namespace Modules\WEBSITE_EXTRA\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;
use Modules\WEBSITE_EXTRA\Services\ContactMessageService;

class ContactMessageController extends Controller
{
    public function __construct(private readonly ContactMessageService $messages) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'mail_status', 'department']);

        return Inertia::render('WEBSITE_EXTRA::ContactMessage/Index', [
            'messages' => $this->messages->paginate($filters)->through(fn (ContactMessage $message): array => $this->serialize($message)),
            'filters' => $filters,
            'stats' => $this->messages->stats(),
        ]);
    }

    public function markRead(int $id): RedirectResponse
    {
        $this->messages->markRead($id);

        return back()->with('success', 'Contact message marked as read.');
    }

    public function markResolved(int $id): RedirectResponse
    {
        $this->messages->markResolved($id);

        return back()->with('success', 'Contact message marked as resolved.');
    }

    public function archive(int $id): RedirectResponse
    {
        $this->messages->archive($id);

        return back()->with('success', 'Contact message archived.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->messages->destroy($id);

        return back()->with('success', 'Contact message deleted.');
    }

    /** @return array<string, mixed> */
    private function serialize(ContactMessage $message): array
    {
        return [
            'id' => $message->id,
            'name' => $message->name,
            'email' => $message->email,
            'phone' => $message->phone,
            'department' => $message->department,
            'subject' => $message->subject,
            'message' => $message->message,
            'source' => $message->source,
            'status' => $message->status,
            'mail_status' => $message->mail_status,
            'mail_error' => $message->mail_error,
            'ip_address' => $message->ip_address,
            'admin_notified_at' => $message->admin_notified_at?->toDayDateTimeString(),
            'customer_notified_at' => $message->customer_notified_at?->toDayDateTimeString(),
            'created_at' => $message->created_at?->toDayDateTimeString(),
            'created_at_human' => $message->created_at?->diffForHumans(),
        ];
    }
}
