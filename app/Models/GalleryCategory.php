<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryCategory extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'sort_order', 'status'];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)->orderBy('sort_order');
    }
}
