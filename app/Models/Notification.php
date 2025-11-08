<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public function senderDetails() {
        return $this->belongsTo('App\Models\User', 'sender_id', 'id');
    }

    public function receiverDetails() {
        return $this->belongsTo('App\Models\User', 'receiver_id', 'id');
    }
}
