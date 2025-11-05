<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'date',
        'reference',
        'annex',
        'company',
        'addressed_to',
        'subject',
        'status',
        'observation',
        'attachment'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ✅ Relationship to annexes
    public function annexes()
    {
        return $this->hasMany(LetterAnnex::class, 'incoming_letter_id');
    }
}
