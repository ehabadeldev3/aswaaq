<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terms extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['terms'];
    public function getTermsAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->terms_ar : $this->terms_en;
    }
}
