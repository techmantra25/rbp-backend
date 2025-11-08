<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOld extends Model
{
    use HasFactory;
    protected $table = 'stores_old';
    public function states() {
        return $this->belongsTo('App\Models\State', 'state_id', 'id');
    }
    public function areas() {
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }
    public function users() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    
    
}
