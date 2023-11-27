<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function Shift(){
        return $this->belongsTo(Shift::class,'shift_id');
    }


}
