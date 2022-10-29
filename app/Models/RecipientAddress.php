<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipientAddress extends Model
{
    use HasFactory;
    protected $table = 'kp_recipient_address';
    protected $filable = ['id', 'user_id', 'name', 'phone', 'location_id', 'location_name', 'description', 'address', 'address_primary', 'zipcode', 'store_type', 'created'];
    public $timestamps = false;
}
