<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DifficultyLevel extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'sort_order'];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'difficulty_id');
    }
}
