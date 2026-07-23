<?php

namespace Modules\WEBSITE_EXTRA\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_READ = 'read';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_ARCHIVED = 'archived';

    public const MAIL_PENDING = 'pending';
    public const MAIL_SENT = 'sent';
    public const MAIL_FAILED = 'failed';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'department',
        'subject',
        'message',
        'source',
        'status',
        'mail_status',
        'mail_error',
        'ip_address',
        'user_agent',
        'admin_notified_at',
        'customer_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'admin_notified_at' => 'datetime',
            'customer_notified_at' => 'datetime',
        ];
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, fn (Builder $builder, string $term) => $builder
            ->where(fn (Builder $inner) => $inner
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('subject', 'like', "%{$term}%")
                ->orWhere('message', 'like', "%{$term}%")));
    }
}
