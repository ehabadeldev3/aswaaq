<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyOrdersLog extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeCollectOrdersPerDay($q,$date)
    {
        $q->join('products','products.id','daily_orders_logs.product_id')
        ->join('prices','prices.product_id','products.id')
        ->join('orders_products','orders_products.price_id','prices.id')
        ->join('orders','orders.id','orders_products.order_id')
        ->join('suppliers','suppliers.id','orders.supplier_id')
        ->join('flavors','flavors.id','products.flavor_id')
        ->join('sizes','sizes.id','products.size_id')
        ->join('measurement_units','measurement_units.id','daily_orders_logs.measurement_id')
        ->whereColumn('daily_orders_logs.product_id','prices.product_id')
        ->whereColumn('daily_orders_logs.measurement_id','orders_products.measurement_id')
        ->select([
            'products.name_ar as product_ar','products.name_en as product_en','products.image',
            'sizes.name_ar as size_ar','sizes.name_en as size_en',
            'flavors.name_ar as flavor_ar','flavors.name_en as flavor_en',
            'measurement_units.name_ar as measurement_ar','measurement_units.name_en as measurement_en',
            'orders_products.*','orders.order_status',
            'daily_orders_logs.id as log_id','daily_orders_logs.quantity as total_quantity','daily_orders_logs.product_id','daily_orders_logs.date','daily_orders_logs.deficit',
            'suppliers.id as supplier_id','suppliers.name as supplier_name','suppliers.is_our_supplier'
        ])
        ->whereIn('order_status',['Pending','Processing','Shipping','Completed','Partial Return'])
        ->whereDate('daily_orders_logs.date',$date)
        ->whereDate('orders.created_at',$date)
        ->latest('daily_orders_logs.created_at');
    }

}
