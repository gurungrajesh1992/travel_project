<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Homepage hero slider slide — either an uploaded image or a YouTube video
 * link. `youtube_embed_url` converts any watch/share/short YouTube URL into
 * an embeddable autoplay/loop URL for the website slider.
 */
class Banner extends Model
{
    protected $fillable = [
        'title', 'media_type', 'file_path', 'video_url', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if ($this->media_type !== 'video' || ! $this->video_url) {
            return null;
        }

        preg_match(
            '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/',
            $this->video_url,
            $matches
        );

        $videoId = $matches[1] ?? null;

        if (! $videoId) {
            return null;
        }

        return "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&loop=1&controls=0&playlist={$videoId}&rel=0&modestbranding=1";
    }
}
