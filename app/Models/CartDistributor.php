<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Color;
use App\Models\Size;
use App\Models\Product;

class CartDistributor extends Model
{
    protected $fillable = ['id', 'user_id', 'device_id', 'ip', 'product_id', 'product_name','product_style_no' ,'product_image', 'product_slug', 'product_variation_id', 'price', 'offer_price', 'color', 'size', 'qty', 'coupon_code_id', 'status'];

    protected $table = 'carts_distributors';

   public function color() {
        return $this->belongsTo('App\Models\Color', 'color_id', 'id');
    }
    public function size() {
        return $this->belongsTo('App\Models\Size', 'size_id', 'id');
    }
    
    public function product() {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
}
