<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterAnnex extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_letter_id',
        'file_path',
        'file_name',
    ];

    public function letter()
    {
        return $this->belongsTo(IncomingLetter::class, 'incoming_letter_id');
    }
}
