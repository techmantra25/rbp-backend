<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class Product extends Model
{
    use HasFactory;
    public function category() {
        return $this->belongsTo('App\Models\Category', 'cat_id', 'id');
    }
    public function collection() {
        return $this->belongsTo('App\Models\Collection', 'collection_id', 'id');
    }
    public function colorSize() {
        return $this->hasMany('App\Models\ProductColorSize', 'product_id', 'id');
    }
    public function color() {
        return $this->hasMany('App\Models\ProductColorSize' ,'color_id','id');
    }
    public function size() {
        return $this->hasMany('App\Models\Size','size_id',  'id');
    }
    
    
    
     public static function insertData($data, $successCount) {
        $id='';
        $value = DB::table('products')->where('name', $data['name'])->where('collection_id',$data['collection_id'])->get();
        if($value->count() == 0) {
            $id = DB::table('products')->insertGetId($data);
           
           //DB::table('users')->insert($data);
            $successCount++;
        $resp = [
            "successCount" => $successCount,
            "id" => $id,
        ];
        
         return $resp;
        } else {
            $resp = [
            "successCount" => 0,
            "id" => $value[0]->id,
            ];
            
            return $resp;
        }

        // return $count;

       
        
    }
}
