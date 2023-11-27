<?php

namespace App\Imports;

use App\Models\Price;
use App\Models\Product;
use App\Models\ProductLog;
use App\Models\ProductPricing;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class AllPricesImport implements ToModel,WithCalculatedFormulas
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        set_time_limit(60);
        if ($row && isset($row[0]) && isset($row[1]) && isset($row[2]) && isset($row[3]) && isset($row[4])) {
            $price = ProductPricing::find($row[0]);
            if($price){
                $price->update([
                    'price' => $row[2],
                    'max_quantity' => $row[5],
                    'less_quantity' => $row[4]
                ]);
            }

        }
    }

    // public function store_in_product_logs($diff_qty, $total_qty, $pharmacyPrice, $publicPrice, $clientDiscount, $kayanDiscount, $kayanProfit, $price_id)
    // {
    //     ProductLog::create([
    //         'diff_qty' => $diff_qty,
    //         'total_qty' => $total_qty,
    //         'pharmacyPrice' => $pharmacyPrice,
    //         'publicPrice' => $publicPrice,
    //         'clientDiscount' => $clientDiscount,
    //         'kayanDiscount' => $kayanDiscount,
    //         'kayanProfit' => $kayanProfit,
    //         'price_id' => $price_id,
    //     ]);
    // }

    // public function update_supplier_price_and_store_in_product_logs($diff_qty, $total_qty, $pharmacyPrice, $publicPrice, $clientDiscount, $kayanDiscount, $kayanProfit, $price_id, $old_qty)
    // {
    //     $latest_log = ProductLog::where('price_id', $price_id)->latest()->first();
    //     $latest_log->update(['sold_quantity' => $latest_log->total_qty - $old_qty]);
    //     ProductLog::create([
    //         'diff_qty' => $diff_qty,
    //         'total_qty' => $total_qty,
    //         'pharmacyPrice' => $pharmacyPrice,
    //         'publicPrice' => $publicPrice,
    //         'clientDiscount' => $clientDiscount,
    //         'kayanDiscount' => $kayanDiscount,
    //         'kayanProfit' => $kayanProfit,
    //         'price_id' => $price_id,
    //     ]);
    // }
}
