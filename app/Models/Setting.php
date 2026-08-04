<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = ['key_name', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("settings.{$key}", function () use ($key, $default) {
            return static::where('key_name', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key_name' => $key], ['value' => $value]);
        Cache::forget("settings.{$key}");
    }
}
