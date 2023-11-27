<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispatcher extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function suppliers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Supplier::class,'dispatchers_suppliers','dispatcher_id','supplier_id','id','id');
    }
}
