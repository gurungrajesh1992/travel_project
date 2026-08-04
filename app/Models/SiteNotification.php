<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Custom notifications table (see requirement §13: bell icon + notification
 * list). Deliberately not named `Notification` to avoid any confusion with
 * Illuminate\Notifications\Notification, and this app doesn't use Laravel's
 * built-in database notification channel/table.
 */
class SiteNotification extends Model
{
    public $timestamps = false;

    protected $table = 'notifications';

    protected $fillable = [
        'notifiable_type', 'notifiable_id', 'type', 'channel',
        'title', 'body', 'data', 'read_at', 'sent_at', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
