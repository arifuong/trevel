<?php

namespace App\Models;

use App\Helpers\YouTubeHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'type',
        'is_profile_hero',
        'image_path',
        'video_url',
        'caption',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order'      => 'integer',
        'is_active'       => 'boolean',
        'is_profile_hero' => 'boolean',
    ];

    /**
     * Scope untuk item yang dijadikan hero profil
     */
    public function scopeProfileHero(Builder $query): Builder
    {
        return $query->where('is_profile_hero', true);
    }

    /**
     * Scope untuk item galeri yang aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk item foto
     */
    public function scopePhotos(Builder $query): Builder
    {
        return $query->where('type', 'photo');
    }

    /**
     * Scope untuk item video
     */
    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('type', 'video');
    }

    /**
     * Ekstraksi YouTube Video ID
     */
    public function getYoutubeIdAttribute(): ?string
    {
        return $this->video_url ? YouTubeHelper::extractVideoId($this->video_url) : null;
    }

    /**
     * URL Embed YouTube yang siap pakai untuk iframe
     */
    public function getEmbedUrlAttribute(): ?string
    {
        return $this->video_url ? YouTubeHelper::getEmbedUrl($this->video_url) : null;
    }

    /**
     * URL gambar / thumbnail (mendukung custom thumbnail, auto YouTube thumbnail, dan fallback)
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!empty($this->image_path)) {
            return \App\Helpers\ImageHelper::url($this->image_path, asset('storage/' . $this->image_path));
        }

        if ($this->type === 'video' && $this->youtube_id) {
            return 'https://img.youtube.com/vi/' . $this->youtube_id . '/hqdefault.jpg';
        }

        return 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fm=webp&fit=crop&w=600&q=75';
    }
}
