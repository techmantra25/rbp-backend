<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadQuater extends Model
{
    use HasFactory;
    public function states() {
        return $this->belongsTo('App\Models\State', 'state_id', 'id');
    }
}
