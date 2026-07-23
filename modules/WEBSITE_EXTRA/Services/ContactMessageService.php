<?php

namespace Modules\WEBSITE_EXTRA\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\WEBSITE_EXTRA\Interfaces\ContactMessageNotifierInterface;
use Modules\WEBSITE_EXTRA\Interfaces\ContactMessageRepositoryInterface;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;
use Throwable;

class ContactMessageService
{
    public function __construct(
        private readonly ContactMessageRepositoryInterface $messages,
        private readonly ContactMessageNotifierInterface $notifier,
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->messages->paginate($filters, $perPage);
    }

    /** @return array<string, int> */
    public function stats(): array
    {
        return $this->messages->countsByStatus();
    }

    public function findById(int $id): ContactMessage
    {
        return $this->messages->findById($id);
    }

    /**
     * Persist a public contact submission and send both admin/customer emails.
     * Mail delivery failure never loses the customer message; status is saved as failed.
     *
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $meta
     */
    public function submitPublicMessage(array $payload, array $meta = []): ContactMessage
    {
        $data = Arr::except($payload, ['privacy_consent']);

        $message = DB::transaction(fn (): ContactMessage => $this->messages->create(array_merge($data, [
            'source' => $meta['source'] ?? 'contact-page',
            'status' => ContactMessage::STATUS_NEW,
            'mail_status' => ContactMessage::MAIL_PENDING,
            'ip_address' => $meta['ip_address'] ?? null,
            'user_agent' => $meta['user_agent'] ?? null,
        ])));

        try {
            $this->notifier->notifyAdmin($message);
            $message = $this->messages->update($message, ['admin_notified_at' => now()]);

            $this->notifier->notifyCustomer($message);

            return $this->messages->update($message, [
                'customer_notified_at' => now(),
                'mail_status' => ContactMessage::MAIL_SENT,
                'mail_error' => null,
            ]);
        } catch (Throwable $exception) {
            Log::error('Contact message mail delivery failed.', [
                'contact_message_id' => $message->id,
                'error' => $exception->getMessage(),
            ]);

            return $this->messages->update($message, [
                'mail_status' => ContactMessage::MAIL_FAILED,
                'mail_error' => mb_substr($exception->getMessage(), 0, 2000),
            ]);
        }
    }

    public function markRead(int $id): ContactMessage
    {
        return $this->messages->update($this->messages->findById($id), [
            'status' => ContactMessage::STATUS_READ,
        ]);
    }

    public function markResolved(int $id): ContactMessage
    {
        return $this->messages->update($this->messages->findById($id), [
            'status' => ContactMessage::STATUS_RESOLVED,
        ]);
    }

    public function archive(int $id): ContactMessage
    {
        return $this->messages->update($this->messages->findById($id), [
            'status' => ContactMessage::STATUS_ARCHIVED,
        ]);
    }

    public function destroy(int $id): bool
    {
        return $this->messages->delete($this->messages->findById($id));
    }
}
