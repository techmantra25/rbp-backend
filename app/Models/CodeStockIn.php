<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeStockIn extends Model
{
    use HasFactory;
    protected $table='code_stock_in';
     public function codeDetails() {
        return $this->belongsTo('App\Models\RetailerBarcode', 'qrcode_id', 'id');
    }
}