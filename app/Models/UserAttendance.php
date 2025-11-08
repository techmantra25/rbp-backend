<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAttendance extends Model
{
    use HasFactory;
    public function users() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function otheractivity() {
        return $this->belongsTo('App\Models\OtherActivity', 'other_activities_id', 'id');
    }
}
