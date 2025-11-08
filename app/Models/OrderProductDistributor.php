<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProductDistributor extends Model
{
    
    protected $fillable = ['order_id', 'product_id', 'product_name', 'product_image', 'product_slug', 'product_variation_id', 'price', 'offer_price', 'color', 'size', 'qty'];

    protected $table = 'order_products_distributors';

    public function product() {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function productVariationDetails() {
        return $this->belongsTo('App\Models\ProductColorSize', 'product_variation_id', 'id');
    }

    public function orders() {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function color() {
        return $this->belongsTo('App\Models\Color', 'color_id', 'id');
    }
    public function size() {
        return $this->belongsTo('App\Models\Size', 'size_id', 'id');
    }
}
