<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'type',
        'sent_at',  // Add this line
       'status'
    ];
}
