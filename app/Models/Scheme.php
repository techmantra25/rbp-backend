<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    use HasFactory;
     public function states() {
        return $this->belongsTo('App\Models\State', 'state_id', 'id');
    }
}
