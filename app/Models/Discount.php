<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discounts';

    protected $fillable = [
        'name',
        'type',
        'code',
        'description',
        'percentage',
        'amount',
        'valid_from',
        'valid_until',
        'is_active',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_per_user',
        'used_count',
        'banner_image',
        'is_banner',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'is_banner' => 'boolean',
    ];
}
