<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTarget extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded=["id"];

    protected $table = "wallet_targets";

    public function users(){
        return $this->belongsToMany(User::class,'wallet_targets_clients','wallet_target_id','user_id')->withPivot(['points','achieved']);
    }
    public function orders(){
        return $this->hasMany(Order::class,'wallet_target_id');
    }

}

