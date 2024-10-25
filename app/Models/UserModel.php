<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model 
{
    protected $table = 'user';
    protected $primarykey = 'id_user';
    public $timestamps = false;
    public $fillable = [
        'nama_user', 'role', 'username','password'
    ];
}
