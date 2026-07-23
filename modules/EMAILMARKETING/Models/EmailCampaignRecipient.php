<?php

namespace Modules\EMAILMARKETING\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EmailCampaignRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_campaign_id',
        'newsletter_id',
        'name',
        'email',
        'status',
        'tracking_token',
        'error_message',
        'sent_at',
        'opened_at',
        'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'opened_at' => 'datetime',
            'clicked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (EmailCampaignRecipient $recipient): void {
            $recipient->email = mb_strtolower(trim($recipient->email));
            $recipient->status ??= 'pending';
            $recipient->tracking_token ??= hash('sha256', Str::uuid()->toString().Str::random(40));
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'email_campaign_id');
    }

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(EmailMarketingEvent::class, 'email_campaign_recipient_id');
    }

    public function markSent(): void
    {
        $this->forceFill([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
        ])->save();
    }

    public function markSkipped(string $reason): void
    {
        $this->forceFill([
            'status' => 'skipped',
            'error_message' => $reason,
        ])->save();
    }

    public function markFailed(string $message): void
    {
        $this->forceFill([
            'status' => 'failed',
            'error_message' => mb_substr($message, 0, 2000),
        ])->save();
    }
}
