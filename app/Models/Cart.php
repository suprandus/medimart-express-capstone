<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'product_id';
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
        'subtotal',
        'product_image',
        'prescription_approved',
        'checked',
    ];
    
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
