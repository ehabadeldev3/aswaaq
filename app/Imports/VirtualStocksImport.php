<?php

namespace App\Imports;

use App\Models\Price;
use App\Models\Product;
use App\Models\ProductLog;
use App\Models\ProductsSupplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class VirtualStocksImport implements ToModel,WithCalculatedFormulas
{
    public function __construct()
    {
        foreach(Price::where('supplier_id',request()->supplier_id)->get() as $price){
            $price->update(['quantity' => 0]);
            ProductLog::create([
                'diff_qty' => 0,
                'total_qty' => 0,
                'main_measurement_price' => 0,
                'sub_measurement_price' => 0,
                'main_measurement_price_after_sale' => 0,
                'sub_measurement_price_after_sale' => 0,
                'price_id' => $price->id,
            ]);
        }
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        set_time_limit(60);
        if ($row && isset($row[0]) && isset($row[4]) && isset($row[6])) {
            $product = Product::find($row[0]);
            if ($product && ProductsSupplier::where('supplier_id',request()->supplier_id)->where('product_id',$product->id)->first()) {
                if ($price = Price::where('product_id', $product->id)->where('supplier_id', request()->supplier_id)->first()) {
                    // just update in case there are diffrence between data
                    if (
                        intval($row[6]) != $price->quantity
                        || $row[4] != $price->main_measurement_price
                        || $row[5] != $price->sub_measurement_price
                    ) {
                        $old_qty = $price->quantity;
                        $price->update([
                            'quantity'        => $row[6],
                            'main_measurement_price'        => $row[4],
                            'sub_measurement_price'        => $row[5],
                            'main_measurement_price'        => $row[7],
                            'sub_measurement_price'        => $row[8],
                        ]);
                        $price->save();
                        $this->update_supplier_price_and_store_in_product_logs($price->quantity - $old_qty,$price->quantity,$price,$old_qty);
                    }
                } else {
                    $price = Price::create([
                        'product_id'      => $row[0],
                        'quantity'        => $row[6],
                        'main_measurement_price'        => $row[4],
                        'sub_measurement_price'        => $row[5],
                        'main_measurement_price_after_sale'        => $row[7],
                        'sub_measurement_price_after_sale'        => $row[8],
                        'supplier_id'     => request()->supplier_id,
                    ]);
                    $this->store_in_product_logs(0,$product['quantity'],$price);
                    return;
                }
            }
        }
    }

    public function store_in_product_logs($diff_qty,$total_qty,$price)
    {
        ProductLog::create([
            'diff_qty' => $diff_qty,
            'total_qty' => $total_qty,
            'main_measurement_price' => $price->main_measurement_price,
            'sub_measurement_price' => $price->sub_measurement_price,
            'main_measurement_price_after_sale' => $price->main_measurement_price_after_sale,
            'sub_measurement_price_after_sale' => $price->sub_measurement_price_after_sale,
            'price_id' => $price->id,
        ]);
    }

    public function update_supplier_price_and_store_in_product_logs($diff_qty, $total_qty, $price, $old_qty)
    {
        $latest_log = ProductLog::where('price_id', $price->id)->latest()->first();
        $latest_log->update(['sold_quantity' => $latest_log->total_qty - $old_qty]);
        $this->store_in_product_logs($diff_qty, $total_qty, $price);
    }
}
