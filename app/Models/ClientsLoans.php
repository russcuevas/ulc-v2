<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientsLoans extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'pn_number',
        'loan_from',
        'loan_to',
        'loan_amount',
        'balance',
        'principal',
        'loan_terms',
        'status',
        'created_by'
    ];
}
