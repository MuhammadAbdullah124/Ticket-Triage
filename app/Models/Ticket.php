<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $keyType = 'string';       
    public $incrementing = false;       
    protected $fillable = [
        'id',
        'subject',
        'body',
        'status',
        'category',
        'note',
        'explanation',
        'confidence',
    ];
}
