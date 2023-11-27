<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $guarded= ['id'];
    protected $appends=['area_name'];

    public function getAreaNameAttribute($value)
    {
        $area_with_trashed =$this->area()->withTrashed()->first();
        $provience_with_trashed =$area_with_trashed->province()->withTrashed()->first();
        return $area_with_trashed ? $area_with_trashed->name  . ( $provience_with_trashed ? ' / ' .$provience_with_trashed->name :''): '';
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function area(){
        return $this->belongsTo(Area::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class,'mediable');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y/m/d (H:i)');
    }
}

