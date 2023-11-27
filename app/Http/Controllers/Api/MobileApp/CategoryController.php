<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\CategoryOrSubCategoryOrCompanyResource;
use App\Models\Category;
use App\Models\SubCategory;
use App\Traits\Message;

class CategoryController extends Controller
{
    use Message;

    public $current_shop_area_id;
    public function __construct( )
    {
        $this->current_shop_area_id = current_shop_area_id();
    }

    public function categoryHome()
    {
        $categories = Category::where('status', 1)
        ->whereRelation('products',function ($q) {
            $q->where('status',1);
            $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        })
        ->inRandomOrder()->limit(6)->get();
        return $this->sendResponse(['categories' => CategoryOrSubCategoryOrCompanyResource::collection($categories)], trans('message.messageSuccessfully'));
    }

    /**
     * get Category
     */

    public function category()
    {
        $categories = Category::where('status', 1)
        ->whereRelation('products',function ($q) {
            $q->where('status',1);
            $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        })->get();
        return $this->sendResponse(['categories' => CategoryOrSubCategoryOrCompanyResource::collection($categories)], trans('message.messageSuccessfully'));
    }

    /**
     * get sub Category
     */

    public function subCategory($id)
    {

        $subCategories = SubCategory::where('category_id',$id)
        ->where('status', 1)
        ->whereRelation('products',function ($q) {
            $q->where('status',1);
            $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        })->get();
        return $this->sendResponse(['subCategories' => CategoryOrSubCategoryOrCompanyResource::collection($subCategories)], trans('message.messageSuccessfully'));
    }
}
