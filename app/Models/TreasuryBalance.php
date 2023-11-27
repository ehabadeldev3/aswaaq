<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreasuryBalance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function treasury (){
        return $this->belongsTo(Treasury::class,'treasury_id');
    }
}
