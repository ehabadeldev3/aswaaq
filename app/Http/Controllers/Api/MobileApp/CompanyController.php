<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\CategoryOrSubCategoryOrCompanyResource;
use App\Models\Company;
use App\Models\Store;
use App\Traits\Message;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use Message;
    public $current_shop_area_id;
    public function __construct( )
    {
        $this->current_shop_area_id = current_shop_area_id();
    }
    public function companyBySubCategoryId($id){
        $companies = Company::where('status', 1)
            ->whereHas('products',function ($q) use($id) {
            $q->where('sub_category_id',$id);
            $q->where('status',1);
            $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        })->paginate(10);

        return $this->sendResponse(['companies' => ['data' => $companies->makeVisible(['sub_category']),'next_url' => $companies->nextPageUrl()]], trans('message.messageSuccessfully'));
    }

    public function company(){
        $companies = Company::where('status', 1)
            ->whereRelation('products',function ($q) {
            $q->where('status',1);
            $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        })->latest()->paginate(15);
        $data = array_merge(['data' => CategoryOrSubCategoryOrCompanyResource::collection($companies->items())],getPaginates($companies));

        return $this->sendResponse(['companies' => $data], trans('message.messageSuccessfully'));
    }

    /**
     * get company Home
     */

    public function companyHome(){
        $companies = Company::where('status', 1)
            ->whereRelation('products',function ($q) {
                $q->where('status',1);
                $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
            })->inRandomOrder()->paginate(9);
        $data = array_merge(['data' => CategoryOrSubCategoryOrCompanyResource::collection($companies->items())],getPaginates($companies));
        return $this->sendResponse(['companies' => $data], trans('message.messageSuccessfully'));
    }

}
