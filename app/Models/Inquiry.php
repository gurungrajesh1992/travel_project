<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tour_id', 'name', 'email', 'phone', 'subject', 'message', 'response_message',
        'status', 'responded_by', 'responded_at', 'created_at',
    ];

    protected function casts(): array
    {
        return ['responded_at' => 'datetime', 'created_at' => 'datetime'];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
