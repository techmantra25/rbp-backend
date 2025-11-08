<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branding extends Model
{
    use HasFactory;
    protected $table='brandings';
    public function stores() {
        return $this->belongsTo('App\Models\Store', 'store_id', 'id');
    }
    
    public function distributors() {
        return $this->belongsTo('App\Models\User', 'distributor_id', 'id');
    }
    
}
