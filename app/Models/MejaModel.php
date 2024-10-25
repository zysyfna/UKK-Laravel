<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MejaModel extends Model
{
    protected $table = 'meja';
    protected $primaryKey = 'id_meja';
    public $timestamps = false;
    public $fillable = [
        'nomor_meja'
    ];
}
