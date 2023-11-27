<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['user_name'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getUserNameAttribute(){
        return $this->user->name;
    }
    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
