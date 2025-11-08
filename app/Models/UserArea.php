<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserArea extends Model
{
    use HasFactory;

    public function areas() {
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }

    public function users() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    
      public static function insertData($data, $successCount) {
        $id='';
        $value = DB::table('user_areas')->where('area_id', $data['area_id'])->where('user_id', $data['user_id'])->get();
        if($value->count() == 0) {
            $id = DB::table('user_areas')->insertGetId($data);
           
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
            "id" => $id,
            ];
            
            return $resp;
        }

        // return $count;

       
        
    }
}
