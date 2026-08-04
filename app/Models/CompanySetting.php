<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Singleton-style settings row for company/brand info (logo, name, short
 * detail, contact info, banner). Used to render the website nav logo,
 * footer branding, and (later) a homepage banner.
 *
 * Deliberately NOT cached: caching a raw Eloquent model instance via the
 * database cache driver in this environment intermittently comes back as
 * __PHP_Incomplete_Class on unserialize. This is a single indexed-row
 * lookup on a tiny table, so the query cost isn't worth that fragility.
 */
class CompanySetting extends Model
{
    protected $table = 'company_settings';

    protected $fillable = [
        'logo', 'favicon', 'name', 'short_detail', 'address', 'email', 'contact_number', 'banner_image',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], ['name' => 'Travel & Tour Management System']);
    }
}
