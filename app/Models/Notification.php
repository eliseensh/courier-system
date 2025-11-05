<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Allow mass assignment
    protected $fillable = ['type', 'action', 'message'];

    // Enable timestamps so created_at / updated_at are automatically set
    public $timestamps = true;
}
