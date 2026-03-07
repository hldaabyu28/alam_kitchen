<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'image',
        'is_available',
        'is_special',
        'order',
        'stock',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_special' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }
}
