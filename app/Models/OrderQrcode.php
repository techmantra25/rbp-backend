<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderQrcode extends Model
{
    
    

    protected $table = 'order_qrcodes';
    
     
    
     public function qrcode() {
        return $this->belongsTo('App\Models\RetailerBarcode', 'qrcode_id', 'id');
    }
    
    
    
    public function order() {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }
    
}