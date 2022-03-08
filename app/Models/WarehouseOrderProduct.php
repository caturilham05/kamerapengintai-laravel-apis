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
}
