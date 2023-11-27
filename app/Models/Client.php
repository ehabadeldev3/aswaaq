<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'integer'
    ];
    protected $appends = ['selected_shop'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function getSelectedShopAttribute(){
        return Shop::find($this->selected_shop_id);
    }


    public function shops()
    {
        return $this->hasMany(Shop::class);
    }


}
