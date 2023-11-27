<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunSheet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y/m/d (H:i)');
    }

    public function orders(){
        return $this->belongsToMany(Order::class,'orders_run_sheets','run_sheet_id','order_id');
    }
    public function products(){
        return $this->hasMany(RunSheetProducts::class,'run_sheet_id');
    }
}
