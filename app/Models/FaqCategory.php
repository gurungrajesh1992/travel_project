<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'sort_order'];

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order');
    }
}
