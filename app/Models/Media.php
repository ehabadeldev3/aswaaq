<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['url_file'];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function getUrlFileAttribute(){
        return asset('upload/' . $this->file_name);
    }
}
