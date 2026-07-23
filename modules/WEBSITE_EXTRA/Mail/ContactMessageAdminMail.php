<?php

namespace Modules\WEBSITE_EXTRA\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;

class ContactMessageAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function build(): self
    {
        $message = $this->contactMessage;

        return $this
            ->subject('New contact message: '.$message->subject)
            ->replyTo($message->email, $message->name)
            ->html($this->htmlBody());
    }

    private function htmlBody(): string
    {
        $message = $this->contactMessage;
        $rows = [
            'Name' => $message->name,
            'Email' => $message->email,
            'Phone' => $message->phone ?: 'Not provided',
            'Department' => $message->department ?: 'General',
            'Subject' => $message->subject,
            'Submitted' => $message->created_at?->toDayDateTimeString(),
            'IP Address' => $message->ip_address ?: 'Unknown',
        ];

        $details = collect($rows)->map(fn ($value, $label): string => '<tr><td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;color:#64748b;font-size:13px;width:150px;">'.e($label).'</td><td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;color:#0f172a;font-size:14px;font-weight:600;">'.e((string) $value).'</td></tr>')->implode('');

        return '<div style="margin:0;padding:28px;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a;">'
            .'<div style="max-width:680px;margin:auto;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 18px 50px rgba(15,23,42,.12);">'
            .'<div style="background:linear-gradient(135deg,#1e40af,#0ea5e9,#10b981);padding:28px;color:#ffffff;">'
            .'<p style="margin:0 0 8px;font-size:12px;letter-spacing:.16em;text-transform:uppercase;font-weight:800;">AMZ Contact Center</p>'
            .'<h1 style="margin:0;font-size:26px;line-height:1.2;">New contact message received</h1>'
            .'</div>'
            .'<div style="padding:26px;">'
            .'<table width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;border-collapse:separate;border-spacing:0;">'.$details.'</table>'
            .'<div style="margin-top:22px;padding:18px;border-radius:16px;background:#f8fafc;border:1px solid #e2e8f0;">'
            .'<p style="margin:0 0 8px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;font-weight:800;">Message</p>'
            .'<div style="font-size:15px;line-height:1.75;color:#334155;white-space:pre-line;">'.e($message->message).'</div>'
            .'</div>'
            .'</div></div></div>';
    }
}
