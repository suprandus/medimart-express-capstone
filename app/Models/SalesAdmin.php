<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesAdmin extends Model
{
    use HasFactory;

    // Specify the table name if it does not follow the Laravel naming convention
    protected $table = 'sales_admin';

    // Specify the primary key if it's not "id"
    protected $primaryKey = 'sales_admin_id';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'vendor_id',
        'product_id',
        'product_name',
        'product_brand_id',
        'product_category_id',
        'product_sub_category_id',
        'product_child_category_id',
        'product_price',
        'product_order_quantity',
        'order_cost',
        'sales',
        'created_at',
    ];

    // Add any relationships if needed
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
