<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    
    

    protected $table = 'warehouse_stocks';
    
     public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
        
        
    }
    
     public function qrcode() {
        return $this->belongsTo('App\Models\RetailerBarcode', 'qrcode_id', 'id');
    }
    
     public function order() {
        return $this->belongsTo('App\Models\OrderDistributor', 'order_id', 'id');
    }
    
    public function distributor() {
        return $this->belongsTo('App\Models\User', 'distributor_id', 'id');
    }
    
}