<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class Area extends Model
{
    use HasFactory;
    
    public function states() {
        return $this->belongsTo('App\Models\State', 'state_id', 'id');
    }
    
    public static function insertData($data, $successCount) {
        $value = DB::table('areas')->where('name', $data['name'])->get();
        if($value->count() == 0) {
           DB::table('areas')->insert($data);
            $successCount++;
        } else {
            false;
        }

        // return $count;

        $resp = [
            "successCount" => $successCount
            
        ];
        return $resp;
    }
}
