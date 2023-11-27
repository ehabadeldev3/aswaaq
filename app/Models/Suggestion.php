<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = [
        'name',
    ];
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    // relations

    public function suggestionUser(){
        return $this->hasMany(SuggestionUser::class,'suggestion_id');
    }
}
