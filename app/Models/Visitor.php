<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    protected $fillable = [
        'destination_id',
        'ticket_no',
        'name',
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
        'status',
        'checked_in_at',
        'checked_out_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
