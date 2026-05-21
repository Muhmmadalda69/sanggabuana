<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Visitor extends Model
{
    protected $fillable = [
        'visitor_account_id',
        'destination_id',
        'group_id',
        'visit_date',
        'ticket_no',
        'name',
        'email',
        'age',
        'address',
        'address_type',
        'city',
        'province',
        'community',
        'purpose',
        'camping_duration',
        'qty_male',
        'qty_female',
        'qty_kids',
        'qty_total',
        'avg_age',
        'price',
        'total_price',
        'payment_method',
        'payment_token',
        'transaction_id',
        'payment_status',
        'payment_details',
        'snap_token',
        'payment_settlement_at',
        'status',
        'checked_in_at',
        'checked_out_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'payment_settlement_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($visitor) {
            if (empty($visitor->payment_token)) {
                $visitor->payment_token = (string) Str::uuid();
            }
        });
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
