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
        'created'
    ];
    public $timestamps = false;
}
