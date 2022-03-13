<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSN extends Model
{
    use HasFactory;
    protected $table = 'warehouse_product_sn';
    public $timestamps = false;
}
