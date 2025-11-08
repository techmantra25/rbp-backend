<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use DB;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    
      public static function insertData($data, $successCount) {
        $id='';
        $value = DB::table('users')->where('name', $data['name'])->where('employee_id',$data['employee_id'])->get();
        if($value->count() == 0) {
            $id = DB::table('users')->insertGetId($data);
           
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

    public function orderDetails() {
        return $this->hasMany('App\Models\Order', 'user_id', 'id');
    }
}
