<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public function areas() {
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }
    public function states() {
        return $this->belongsTo('App\Models\State', 'state_id', 'id');
    }
    public function distributors() {
        return $this->belongsTo('App\Models\User', 'distributor_id', 'id');
    }
    public function vp() {
        return $this->belongsTo('App\Models\User', 'vp_id', 'id');
    }
    public function rsm() {
        return $this->belongsTo('App\Models\User', 'rsm_id', 'id');
    }
    public function sm() {
        return $this->belongsTo('App\Models\User', 'sm_id', 'id');
    }
    public function asm() {
        return $this->belongsTo('App\Models\User', 'asm_id', 'id');
    }
    public function nsm() {
        return $this->belongsTo('App\Models\User', 'nsm_id', 'id');
    }
    public function zsm() {
        return $this->belongsTo('App\Models\User', 'zsm_id', 'id');
    }
    public function ase() {
        return $this->belongsTo('App\Models\User', 'ase_id', 'id');
    }
     public function store() {
        return $this->belongsTo('App\Models\Store', 'store_id', 'id');
    }
}
