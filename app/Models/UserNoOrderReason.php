<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNoOrderReason extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'store_id',
        'no_order_reason_id',
        'comment',
        'description',
        'location',
        'lat',
        'lng',
        'date',
        'time'
    ];
    public function stores() {
        return $this->belongsTo('App\Models\Store', 'store_id', 'id');
    }
    public function users() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
