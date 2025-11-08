<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreFormSubmit extends Model
{
    use HasFactory;
    
    public function users() {
        return $this->belongsTo('App\Models\Store', 'retailer_id', 'id');
    }
    
    public function distributors() {
        return $this->belongsTo('App\Models\User', 'distributor_id', 'id');
    }
    
}