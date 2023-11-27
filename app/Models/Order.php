<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends=['area_name'];
    protected $casts = [
        'total_amount' => 'float',
        'subtotal' => 'float',
        'hold' => 'integer',
        'confirmed_received_amount' => 'integer',
        'shipping_cost' => 'integer',
        'used_points' => 'integer',
        // 'representative_refund_part_note' => 'json',
    ];


    public function getRepresentativeRefundPartNoteAttribute($value){
        return $value ? json_decode($value) : '';
    }

    public function notes()
    {
        return $this->hasMany(Note::class,'order_id');
    }

    public function run_sheet()
    {
        return $this->hasOne(OrdersRunSheet::class,'order_id');
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function group(){
        return $this->belongsTo(OrdersGroup::class,'group_id');
    }

    public function area(){
        return $this->belongsTo(Area::class);
    }


    public function representative(){
        return $this->belongsTo(User::class,'representative_id');
    }
    public function car(){
        return $this->belongsTo(Car::class,'car_id');
    }

    public function products()
    {
        return $this->belongsToMany(Price::class,'orders_products','order_id','price_id')->withPivot(['measurement_id','measurement_type','quantity','price','sub_total','returned_quantity','refund_amount']);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y/m/d (H:i)');
    }

    public function getAreaNameAttribute($value)
    {
        $area_with_trashed =$this->area()->withTrashed()->first();
        $provience_with_trashed =$area_with_trashed->province()->withTrashed()->first();
        return $area_with_trashed ? $area_with_trashed->name  . ( $provience_with_trashed ? ' / ' .$provience_with_trashed->name :''): '';
    }

    public function scopeGetCompletedOrdersThroughMonthGroubByWeeks($q,$year,$month){
        return $q
        ->whereIn('order_status',['Completed','Partial Return'])
        ->where('confirmed_received_amount',1)
        ->whereYear('created_at',$year)->whereMonth('created_at',$month)
        ->orderBy('created_at')->get()
        ->groupBy(function($data) {
            //week
            $date = explode(" ",$data->created_at);// get the date from this format 'Y/m/d (H:i)'
            return $this->getNameOfWeekInMounth(Carbon::parse($date[0])->format('d')); //groub by week

        });
    }

    public function getNameOfWeekInMounth($week_number)
    {
        switch(true){
            case ($week_number <= 7):
                $week_name = 'First Week';
                break;
            case ($week_number <= 15):
                $week_name = 'Second Week';
                break;
            case ($week_number <= 22):
                $week_name = 'Third Week';
                break;
            case ($week_number <= 29):
                $week_name = 'Fourth Week';
                break;
            case ($week_number <= 31):
                $week_name = 'Fifth Week';
                break;
        }
        return $week_name;
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function scopeGetSupplierOrdersIfDispatcherAuthenticated($q) {
        if($dispatcher = auth()->user()->dispatcher){
            $q->where(function($q) use($dispatcher){
                $suppliers_ids = $dispatcher->suppliers->pluck('id')->toArray();
                $q->whereIn('supplier_id',$suppliers_ids);
            });
        }

    }

    public function wallet_target(){
        return $this->belongsTo(WalletTarget::class,'wallet_target_id');
    }

}
