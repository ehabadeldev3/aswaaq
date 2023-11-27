<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CategoryRequest;
use App\Models\Category;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    use Message;

    public function __construct()
    {
        $this->middleware('permission:category read', ['only' => ['index','activeCategory']]);
        $this->middleware('permission:category create', ['only' => ['store']]);
        $this->middleware('permission:category edit', ['only' => ['update', 'edit','activationCategory']]);
        $this->middleware('permission:category delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::with('media:file_name,mediable_id')
            ->when($request->search, function ($q) use ($request) {
            return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
        })->latest()->paginate(10);

        return $this->sendResponse(['categories' => $categories], 'Data exited successfully');
    }

    /**
     * get active Category
     */
    public function activeCategory()
    {
        $categories = Category::where([
            ['status', 1],
        ])->get();
        return $this->sendResponse(['categories' => $categories], 'Data exited successfully');
    }

    public function activationCategory(Category $category)
    {
        $category->update([
            "status" => $category->status == 1 ? 0:1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }



    public function store(CategoryRequest $request)
    {
        $category = Category::create(['name_ar' => $request->name_ar, 'name_en' => $request->name_en]);

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
        $category = Category::with('media:file_name,mediable_id')->find($id);
        return $this->sendResponse(['category' => $category], 'Data exited successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update(['name_ar' => $request->name_ar,'name_en' => $request->name_en]);

        if($request->hasFile('file'))
            saveFile($request->file,$category,'category','update');


        return $this->sendResponse([],'Data exited successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if (count($category->products) == 0 && count($category->children) == 0){

            deleteFile($category);

            $category->delete();
            return $this->sendResponse([],'Deleted successfully');
        }
        return $this->sendError('Cant delete');

    }
}
