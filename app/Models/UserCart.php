<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
    protected $table = 'vw_usercart';
    protected $primaryKey = 'product_id';
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'vendor_id',
        'product_id',
        'product_name',
        'product_stock',
        'product_price',
        'cart_product_count',
        'cart_subtotal',
        'prescription_approved',
        'checked',
        'image_product',
        'brand_id',
        'category_id',
        'subcategory_id',
        'childcategory_id',
    ];
    
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
