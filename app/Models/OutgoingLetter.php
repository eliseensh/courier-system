<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingLetter extends Model
{
    use HasFactory;

    protected $table = 'outgoing_letters';

protected $fillable = [
    'number',
    'date',
    'reference', // ✅ must be here
    'recipient',
    'subject',
    'status',
    'observation',
    'attachment',
];


    // Conversion automatique en Carbon
    protected $casts = [
        'date' => 'datetime',
    ];
}
