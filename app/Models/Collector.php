<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collector extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'role',
        'created_by',
        'updated_by',
    ];
}
