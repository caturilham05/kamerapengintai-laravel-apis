<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BbcUser extends Model
{
    use HasFactory;
    protected $table = 'Bbc_user';
    protected $fillable = [
        'group_ids',
        'username',
        'password',
        'created',
    ];
    public $timestamps = false;

}
