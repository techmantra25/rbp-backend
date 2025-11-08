<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDistributor extends Model
{
	
    

    protected $table = 'orders_distributors';

    public function orderProducts() {
        return $this->hasMany('App\Models\OrderProductDistributor', 'order_id', 'id');
    }

    public function users() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
