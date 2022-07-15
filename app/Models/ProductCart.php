<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCart extends Model
{
    use HasFactory;
    protected $table = 'kp_product_cart';
    protected $fillable = [
        'user_id',
        'product_id',
        'qty',
        'stock',
    ];
    public $timestamps = false;
}
