<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Purpose extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_all_destinations',
    ];

    protected $casts = [
        'is_all_destinations' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($purpose) {
            if (empty($purpose->slug)) {
                $purpose->slug = Str::slug($purpose->name);
            }
        });
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'destination_purpose')
                    ->withPivot('has_custom_price', 'custom_price')
                    ->withTimestamps();
    }
}
