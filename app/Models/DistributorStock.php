<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorStock extends Model
{
    
    

    protected $table = 'distributor_stocks';
    
     
    
     public function qrcode() {
        return $this->belongsTo('App\Models\RetailerBarcode', 'qrcode_id', 'id');
    }
    
    
    
    public function distributor() {
        return $this->belongsTo('App\Models\User', 'distributor_id', 'id');
    }
    
     public function order() {
        return $this->belongsTo('App\Models\Order', 'retailer_order_id', 'id');
    }
    
}