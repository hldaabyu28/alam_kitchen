<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'payment_gateway',
        'gateway_order_id',
        'gateway_transaction_id',
        'snap_token',
        'redirect_url',
        'amount',
        'payment_method',
        'payment_type',
        'va_number',
        'payment_code',
        'status',
        'expires_at',
        'paid_at',
        'pdf_url',
        'raw_response',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'raw_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
