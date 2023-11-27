<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\VirtualStockRequest;
use App\Imports\VirtualStocksImport;
use App\Models\Category;
use App\Models\Price;
use App\Models\ProductLog;
use App\Models\Setting;
use App\Models\Supplier;
use App\Traits\Message;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VirtualStockController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:virtualStock read', ['only' => ['index', 'categories_by_supplier', 'download_supplier_prices', 'getLimitSettings', 'get_prices_for_supplier']]);
        $this->middleware('permission:virtualStock create', ['only' => ['store_product_price']]);
        $this->middleware('permission:virtualStock edit', ['only' => ['saveExcelVirtualStock', 'markProductAsBestPrice', 'insertLimitSettings']]);
        $this->middleware('permission:virtualStock delete', ['only' => ['destroy']]);
    }

    public function categories_by_supplier(Request $request)
    {
        $supplier = Supplier::getSupplierIfDispatcherAuthenticated()->findOrFail($request->supplier_id);
        $categories = Category::whereHas('products', function ($q) use ($request) {
                $q->whereRelation('suppliers', function ($q) use ($request) {
                    $q->getSupplierIfDispatcherAuthenticated()->where('suppliers.id', $request->supplier_id);
                });
            })->get();

        return $this->sendResponse(['categories' => $categories, 'supplier' => $supplier], 'Data exited successfully');
    }

    public function index(Request $request)
    {
        $prices = Price::with(['product', 'supplier:id,name', 'product.flavor', 'product.size'])
            ->getPricesForSupplierIfDispatcherAuthenticated()
            ->where(function ($q) use ($request) {
                $q->when($request->search, function ($q) use ($request) {
                    return $q->where('main_measurement_price', 'like', '%' . $request->search . '%')
                        ->orWhere('quantity', 'like', '%' . $request->search . '%')
                        ->orWhere('sub_measurement_price', 'like', '%' . $request->search . '%')
                        ->orWhere('main_measurement_price_after_sale', 'like', '%' . $request->search . '%')
                        ->orWhere('sub_measurement_price_after_sale', 'like', '%' . $request->search . '%')
                        ->orWhereRelation('product', function ($q) use ($request) {
                            $q->where('name_ar', 'like', '%' . $request->search . '%')
                                ->orWhere('name_en', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereRelation('supplier', 'name', 'like', '%' . $request->search . '%');
                });
            })
            ->where(function ($q) use ($request) {
                $q->when($request->filter_best_offers == 'best_offers', function ($q) use ($request) {
                    return $q->where('best_offer', 1);
                });
            })
            ->latest()->paginate(20);
        return $this->sendResponse(['prices' => $prices], 'Data exited successfully');
    }

    public function store_product_price(VirtualStockRequest $request)
    {
        foreach ($request->product as $product) {
            if (!$price = Price::where('supplier_id', $product['supplier_id'])->where('product_id', $product['product_id'])->first()) {
                $price = Price::create([
                    'supplier_id'     => $product['supplier_id'],
                    'product_id'      => $product['product_id'],
                    'quantity'        => $product['quantity'],
                    'main_measurement_price'        => $product['main_measurement_price'],
                    'sub_measurement_price'        => $product['sub_measurement_price'],
                    'main_measurement_price_after_sale'        => $product['main_measurement_price_after_sale'] ?? 0,
                    'sub_measurement_price_after_sale'        => $product['sub_measurement_price_after_sale'] ?? 0,
                ]);
                $this->store_in_product_logs(0, $product['quantity'], $price);
            } else {
                $old_qty = $price->quantity;

                $price->update([
                    'quantity'        => $product['quantity'],
                    'main_measurement_price'        => $product['main_measurement_price'],
                    'sub_measurement_price'        => $product['sub_measurement_price'],
                    'main_measurement_price_after_sale'        => $product['main_measurement_price_after_sale'] ?? 0,
                    'sub_measurement_price_after_sale'        => $product['sub_measurement_price_after_sale'] ?? 0,
                ]);
                $price->save();
                $this->update_supplier_price_and_store_in_product_logs($price->quantity - $old_qty, $price->quantity, $price, $old_qty);
            }
        }
        return $this->sendResponse([], 'Data exited successfully');
    }


    public function download_supplier_prices($supplier_id)
    {
        $supplier = Supplier::getSupplierIfDispatcherAuthenticated()->findOrFail($supplier_id);
        $all_products_for_excel = $supplier->products()->where('status', 1)->get()->map(function ($item) use ($supplier) {
            $price = Price::where('supplier_id', $supplier->id)->where('product_id', $item->id)->first();
            return [
                "Product id" => $item->id,
                "Product Name" => $item->name,
                "Main Measurement Unit Price" => $price ? $price->main_measurement_price : 0,
                "Sub Measurement Unit Price" => $price ? $price->sub_measurement_price : 0,
                "Main Measurement Unit Price After Sale" => $price ? $price->main_measurement_price_after_sale : 0,
                "Sub Measurement Unit Price After Sale" => $price ? $price->sub_measurement_price_after_sale : 0,
                "Sub Measurement Unit Quantity" => $price ? $price->quantity : 0,
                "Flavor" => $item->flavor ? $item->flavor->name : '',
                "Size" => $item->size->name,
            ];
        });
        return $this->sendResponse(['products' => $all_products_for_excel], 'Data exited successfully');
    }

    public function saveExcelVirtualStock(Request $request)
    {
        $path = $request->file('select_virtualStocks_file');
        Excel::import(new VirtualStocksImport, $path);
    }

    public function markProductAsBestPrice(Request $request)
    {
        $product_price = Price::getPricesForSupplierIfDispatcherAuthenticated()->findOrFail($request->product_id);
        $best_offers_count = Price::getPricesForSupplierIfDispatcherAuthenticated()->where('best_offer', 1)->count();
        $best_prices_limits = Setting::first()->best_prices_limits;
        if ($product_price->best_offer == 1 || $best_prices_limits > $best_offers_count) {
            $product_price->update(['best_offer' => $product_price->best_offer == 0 ? 1 : 0]);
        } else {
            return response()->json(['limit' => $best_prices_limits], 404);
        }
    }

    public function get_prices_for_supplier(Request $request, $supplier_id)
    {
        $supplier = Supplier::getSupplierIfDispatcherAuthenticated()->findOrFail($supplier_id);
        $virtualStocks = Price::with('product', 'product.flavor', 'product.size')
            ->getPricesForSupplierIfDispatcherAuthenticated()
            ->where(function ($q) use ($request, $supplier) {
                $q->where('supplier_id', $supplier->id)
                    ->when($request->search, function ($q) use ($request) {
                        return $q
                            ->where('main_measurement_price', 'like', '%' . $request->search . '%')
                            ->orWhere('sub_measurement_price', 'like', '%' . $request->search . '%')
                            ->orWhere('main_measurement_price_after_sale', 'like', '%' . $request->search . '%')
                            ->orWhere('sub_measurement_price_after_sale', 'like', '%' . $request->search . '%')
                            ->orWhereRelation('product', 'name_ar', 'like', '%' . $request->search . '%')
                            ->orWhereRelation('supplier', 'name', 'like', '%' . $request->search . '%');
                    });
            })
            ->latest()->paginate(10);

        return $this->sendResponse(['virtualStocks' => $virtualStocks, 'supplier' => $supplier], 'Data exited successfully');
    }

    public function store_in_product_logs($diff_qty, $total_qty, $price)
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

    public function insertLimitSettings(Request $request)
    {
        $setting = Setting::first();
        $setting->update(['best_prices_limits' => $request->limit]);
        $setting->save();
    }

    public function getLimitSettings()
    {
        return response()->json(['limit' => Setting::first()->best_prices_limits], 200);
    }

    public function update_supplier_price_and_store_in_product_logs($diff_qty, $total_qty, $price, $old_qty)
    {
        $latest_log = ProductLog::where('price_id', $price->id)->latest()->first();
        $latest_log->update(['sold_quantity' => $latest_log->total_qty - $old_qty]);
        $this->store_in_product_logs($diff_qty, $total_qty, $price);
    }
}
