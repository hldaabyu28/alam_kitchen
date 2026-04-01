<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stores';

    protected $fillable = [
        'name',
        'description',
        'about_us',
        'logo',
        'google_maps_url',
        'google_maps_embed',
        'latitude',
        'longitude',
        'whatsapp_number',
        'instagram',
        'facebook',
        'tiktok',
        'twitter',
        'address',
        'phone',
        'email',
        'opening_time',
        'closing_time',
        'is_active',
    ];

    protected $casts = [
        'opening_time' => 'datetime:H:i',
        'closing_time' => 'datetime:H:i',
        'is_active'    => 'boolean',
        'latitude'     => 'decimal:8',
        'longitude'    => 'decimal:8',
    ];

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }
}
