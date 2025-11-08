<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardCart extends Model
{
    protected $table='reward_carts';
	
	public function product(){
    	return $this->belongsTo('App\Models\RetailerProduct', 'product_id', 'id');
	}
	
	public function store(){
    	return $this->belongsTo('App\Models\Store', 'store_id', 'id');
	}
}
