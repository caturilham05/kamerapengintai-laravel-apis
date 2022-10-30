<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseOrder extends Model
{
    use HasFactory;
    protected $table = 'warehouse_order';
    protected $fillable = [
        'invoice',
        'recipient_id',
        'recipient',
        'recipient_phone',
        'recipient_address',
        'recipient_email',
        'discount_amount',
        'total_paid',
        'installment',
        'paid_leave',
        'date',
        'payment_unique_code',
        'is_payment_due_lock',
        'payment_due_date',
        'qty',
        'qty_inserted',
        'courier_id',
        'courier_name',
        'shipping_cost',
        'invoice_file',
        'sn_file',
        'is_free_shipping',
        'use_fp'
    ];
    public $timestamps = false;
}
