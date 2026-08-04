<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug'];

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}
