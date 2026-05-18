<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Destination extends Model
{
    protected $fillable = [
        'name', 'slug', 'short_description', 'description', 'image',
        'location', 'altitude', 'operational_days', 'operational_hours', 'price', 'duration',
        'latitude', 'longitude', 'is_featured', 'is_active', 'sort_order', 'contacts',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'contacts' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($destination) {
            if (empty($destination->slug)) {
                $destination->slug = Str::slug($destination->name);
            }
        });
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-mountain.jpg');
    }
}
