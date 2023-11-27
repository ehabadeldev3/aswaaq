<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'integer',
        'value' => 'string',
    ];

    public function discount($total)
    {
        if (!$this->checkDate() || !$this->checkUsedTimes() || $this->UserUsedItBefore()){
            return 0;
        }
        return $this->discount_value($total);
    }

    public function discount_value($total)
    {
        return $this->checkGreaterThan($total) ? $this->doProcess($total) : 0;
    }

    protected function checkDate()
    {
        return $this->expire_date != '' ? (Carbon::now()->between($this->start_date, $this->expire_date, true)) ? true : false : true;
    }

    protected function checkUsedTimes()
    {
        return $this->use_times != '' ? ( $this->use_times > $this->used_times ) ? true : false : true;
    }
    public function orders()
    {
        return $this->hasMany(OrderDiscount::class,'discount_id');
    }
    public function UserUsedItBefore()
    {

        return auth()->user() && $this->orders()->whereRelation('order','user_id',auth()->user()->id)->first() ? true : false;
    }

    protected function checkGreaterThan($total)
    {
        return $this->greater_than != '' ? ($this->greater_than <= $total) ? true : false : true;
    }

    protected function doProcess($total)
    {
        switch ($this->type) {
            case 'fixed':
                return $this->value;
            case 'percentage':
                return ($this->value / 100) * $total;
            default:
                return 0;
        }
    }

}
