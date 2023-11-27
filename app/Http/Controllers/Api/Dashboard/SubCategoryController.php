<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SubCategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:subCategory read', ['only' => ['index','getSubCategories']]);
        $this->middleware('permission:subCategory create', ['only' => ['store','create']]);
        $this->middleware('permission:subCategory edit', ['only' => ['update', 'edit']]);
        $this->middleware('permission:subCategory delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = SubCategory::
            with(['media:file_name,mediable_id','category'])
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
            })->latest()->paginate(10);

        return $this->sendResponse(['categories' => $categories], 'Data exited successfully');
    }

    public function create(){
        $main_categories = Category::where([
            ['status', 1],
        ])->get();
        return $this->sendResponse(['main_categories' => $main_categories], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubCategoryRequest $request)
    {
        $category = SubCategory::create(['name_ar' => $request->name_ar,'name_en' => $request->name_en,'category_id' => $request->category_id]);

        if ($request->hasFile('file'))
            saveFile($request->file,$category,'category');

        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = SubCategory::with('media:file_name,mediable_id')->find($id);
        $main_categories = Category::where([
            ['status', 1],
        ])->get();

        return $this->sendResponse(['category' => $category,'main_categories'=>$main_categories], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubCategoryRequest $request, SubCategory $subCategory)
    {
        $subCategory->update(['name_ar' => $request->name_ar,'name_en' => $request->name_en,'category_id' => $request->category_id]);

        if ($request->hasFile('file'))
            saveFile($request->file,$subCategory,'category','update');


         return $this->sendResponse([],'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubCategory $subCategory)
    {

        if(File::exists('upload/'.$subCategory->media->file_name)){
            unlink('upload/'. $subCategory->media->file_name);
        }
        $subCategory->media->delete();
        $subCategory->delete();
        return $this->sendResponse([],'Deleted successfully');

    }


    public function getSubCategories($id)
    {
        $subCategories = SubCategory::where('status', 1)->where('category_id',$id)->get();
        return $this->sendResponse(['subCategories' => $subCategories], 'Data exited successfully');
    }
}
