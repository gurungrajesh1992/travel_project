<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id', 'amount', 'payment_method', 'receipt_path',
        'transaction_ref', 'gateway_response', 'status', 'paid_at', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
