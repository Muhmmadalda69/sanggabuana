<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PendingRegistration extends Model
{
    protected $fillable = [
        'visitor_account_id',
        'temp_token',
        'destination_id',
        'slug',
        'visit_date',
        'form_data',
        'payment_method',
        'snap_token',
        'transaction_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'visit_date' => 'date',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pending) {
            if (empty($pending->temp_token)) {
                $pending->temp_token = (string) Str::uuid();
            }
            if (empty($pending->expires_at)) {
                $pending->expires_at = now()->addHours(2);
            }
        });
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
