<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerWalletTxn extends Model
{
     protected $fillable = [
        'user_id',
        'amount',
        'type',
        'final_amount',
        'created_at',
        'updated_at'
    ];

    public function users() {
        return $this->belongsTo('App\Models\Store', 'user_id', 'id');
    }
}
