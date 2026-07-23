<?php

namespace Modules\EMAILMARKETING\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'ip_address',
        'source',
        'isActive',
        'status',
        'audience_type',
        'tags',
        'country',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
    ];

    protected function casts(): array
    {
        return [
            'isActive' => 'boolean',
            'tags' => 'array',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Newsletter $newsletter): void {
            $newsletter->email = mb_strtolower(trim($newsletter->email));
            $newsletter->status ??= 'subscribed';
            $newsletter->isActive ??= true;
            $newsletter->subscribed_at ??= now();
            $newsletter->unsubscribe_token ??= static::makeToken();
        });

        static::updating(function (Newsletter $newsletter): void {
            if ($newsletter->isDirty('email')) {
                $newsletter->email = mb_strtolower(trim($newsletter->email));
            }
        });
    }

    public static function makeToken(): string
    {
        return hash('sha256', Str::uuid()->toString().Str::random(40));
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(EmailMarketingEvent::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, fn (Builder $builder, string $term) => $builder
            ->where(fn (Builder $inner) => $inner
                ->where('email', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('source', 'like', "%{$term}%")
                ->orWhere('audience_type', 'like', "%{$term}%")
                ->orWhere('country', 'like', "%{$term}%")));
    }

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('isActive', true)->where('status', 'subscribed');
    }

    public function scopeAudience(Builder $query, ?string $audienceType): Builder
    {
        return $query->when($audienceType, fn (Builder $builder, string $type) => $builder->where('audience_type', $type));
    }

    public function markSubscribed(): void
    {
        $this->forceFill([
            'isActive' => true,
            'status' => 'subscribed',
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'unsubscribe_token' => $this->unsubscribe_token ?: static::makeToken(),
        ])->save();
    }

    public function markUnsubscribed(): void
    {
        $this->forceFill([
            'isActive' => false,
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ])->save();
    }
}
