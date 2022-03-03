<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    use HasFactory;
    protected $table = 'kp_marketplace';
    protected $fillable = [
        'id',
        'marketplace',
        'name',
        'store_address',
        'store_number_phone',
        'discount',
        'extra_ongkir',
        'extra_cashback',
        'payment_fee',
        'admin_fee',
        'discount_maximum',
        'created',
        'updated',
    ];
    public $timestamps = false;
}
