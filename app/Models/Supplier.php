<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    protected $appends = [
        'order_numbers',
        'total_commission'
    ];

    protected $casts = [
        'status' => 'integer',
        'is_our_supplier' => 'integer',
    ];

    public function getOrderNumbersAttribute(): int
    {
        return Order::where('supplier_id', $this->id)->whereIn('order_status',['Completed','Partial Return'])->where('confirmed_received_amount',1)->count();
    }
    public function getTotalCommissionAttribute(): float
    {
        return Order::where('supplier_id', $this->id)->whereIn('order_status',['Completed','Partial Return'])->where('confirmed_received_amount',1)->sum('commission');
    }


    // public function purchases(){

    //     return $this->hasMany(Purchase::class);
    // }

    // public function purchaseReturns(){
    //     return $this->hasMany(PurchaseReturn::class);
    // }

    // public function supplierAccounts(){
    //     return $this->hasMany(SupplierAccount::class);
    // }

    // public function supplierExpense(): \Illuminate\Database\Eloquent\Relations\HasMany
    // {
    //     return $this->hasMany(SupplierExpense::class);
    // }

    // public function supplierIncome(): \Illuminate\Database\Eloquent\Relations\HasMany
    // {
    //     return $this->hasMany(SupplierIncome::class);
    // }

    public function  products()
    {
        return $this->belongsToMany(Product::class, 'products_suppliers', 'supplier_id', 'product_id');
    }

    public function workdays(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SupplierDay::class);
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class,'suppliers_areas','supplier_id','area_id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class,'supplier_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class,'supplier_id');
    }

    public function scopeGetSupplierIfDispatcherAuthenticated($q) {
        if($dispatcher = auth()->user()->dispatcher){
            $q->where(function($q) use($dispatcher){
                $suppliers_ids = $dispatcher->suppliers->pluck('id')->toArray();
                $q->whereIn('suppliers.id',$suppliers_ids);
            });
        }

    }

}
