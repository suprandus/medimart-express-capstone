<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesByPharmacy extends Model
{
    use HasFactory;
    protected $table = 'vw_salesbypharmacy'; // Link to the view
    public $timestamps = false; // Views do not have timestamps
}
