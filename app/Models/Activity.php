<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'areas',
        'role',
        'type',
        'description',
        'color',
        'is_read_secretary',
        'is_read_admin',
    ];
}
