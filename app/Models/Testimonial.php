<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name', 'role', 'message', 'avatar', 'rating', 'is_active', 'sort_order', 'is_read',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1b4332&color=fff';
    }
}
