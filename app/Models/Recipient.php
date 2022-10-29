<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;
    protected $table = 'kp_recipient';
    protected $fillable = [
        'owner',
        'name',
        'email',
        'credit_limit',
        'remaining_credit_limit',
        'created'
    ];
    public $timestamps = false;
}
