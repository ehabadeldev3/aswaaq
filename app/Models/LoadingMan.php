<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadingMan extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function areas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Area::class,'loading_men_areas','loading_man_id','area_id','id','id');
    }
}
