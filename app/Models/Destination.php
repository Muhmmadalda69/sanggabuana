<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Destination extends Model
{
    protected $fillable = [
        'name', 'slug', 'short_description', 'description', 'image',
        'location', 'altitude', 'operational_days', 'operational_hours', 'price', 'daily_quota', 'kids_discount', 'duration',
        'latitude', 'longitude', 'is_featured', 'is_active', 'sort_order', 'contacts',
        'has_community', 'has_purpose', 'has_gender_details', 'has_member_details', 'has_online_registration',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'contacts' => 'array',
        'has_community' => 'boolean',
        'has_purpose' => 'boolean',
        'has_gender_details' => 'boolean',
        'has_member_details' => 'boolean',
        'has_online_registration' => 'boolean',
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

    public function purposes()
    {
        return $this->belongsToMany(Purpose::class, 'destination_purpose')
                    ->withPivot('has_custom_price', 'custom_price')
                    ->withTimestamps();
    }

    public function getActivePurposesAttribute()
    {
        $allGlobal = Purpose::where('is_all_destinations', true)->get();
        $explicit = $this->purposes;

        $merged = $allGlobal->map(function ($globalPurpose) use ($explicit) {
            $match = $explicit->firstWhere('id', $globalPurpose->id);
            return $match ?: $globalPurpose;
        });

        return $merged->concat($explicit->where('is_all_destinations', false))->unique('id')->values();
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
