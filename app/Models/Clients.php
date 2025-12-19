<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'phone',
        'address',
        'area_id',
        'gender',
        'created_by',
    ];
}
