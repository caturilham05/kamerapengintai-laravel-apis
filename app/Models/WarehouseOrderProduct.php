<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseOrderProduct extends Model
{
    use HasFactory;
    protected $table = 'warehouse_order_product';
    public $timestamps = false;
    protected  $primaryKey = 'list_id';
    protected $fillable = [
        'order_id',
        'invoice',
        'product_id',
        'product_name',
        'product_sku',
        'qty',
        'qty_inserted',
        'price',
        'sale',
        'type',
        'cat_id',
        'cat_ids',
        'cat_name',
        'position_name',
        'is_ppn',
        'use_fp',
        'status',
        'recipient_id'
    ];
}
