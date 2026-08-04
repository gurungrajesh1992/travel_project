<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = [
        'business_name', 'owner_name', 'email', 'phone', 'logo',
        'commission_rate', 'status', 'approved_at',
    ];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime'];
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
}
