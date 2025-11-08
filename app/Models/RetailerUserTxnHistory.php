<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerUserTxnHistory extends Model
{
    
     protected $fillable = [
        'user_id',
        'amount',
        'order_id',
        'type',
        'title',
        'description',
        'admin_status',
        'db_name',
        'db_code',
        'status',
        'created_at',
        'updated_at'
    ];
    public function orders(){
    	return $this->belongsTo('App\Models\RetailerOrder', 'order_id', 'id');
	}
	
	 public function user(){
    	return $this->belongsTo('App\Models\Store', 'user_id', 'id,unique_code');
	}
}
