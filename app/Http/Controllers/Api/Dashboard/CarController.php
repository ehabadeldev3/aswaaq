<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CarRequest;
use App\Models\Car;
use App\Models\Representative;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:car read', ['only' => ['index','cars']]);
        $this->middleware('permission:car create', ['only' => ['store']]);
        $this->middleware('permission:car edit', ['only' => ['update', 'edit']]);
        $this->middleware('permission:car delete', ['only' => ['destroy']]);
    }

    /**
     * get active Representative
     */
    public function cars(Request $request)
    {
        $cars = $this->search($request);
        if($request->paginate)
            $cars = $cars->take(10)->get();
        else
            $cars = $cars->get();

        return $this->sendResponse(['cars' => $cars], 'Data exited successfully');
    }

    public function index(Request $request)
    {
        $cars = $this->search($request)->paginate(10);

        return $this->sendResponse(['cars' => $cars], 'Data exited successfully');
    }

    protected function search($request){
        return Car::withCount('orders')
        ->when($request->search,function ($q) use($request){
            $q->where('plate_number','like','%'.$request->search.'%')
                ->orWhere('name_ar','like','%'.$request->search.'%')
                ->orWhere('name_en','like','%'.$request->search.'%')
                ->orWhere('description_ar','like','%'.$request->search.'%')
                ->orWhere('description_en','like','%'.$request->search.'%');
        })->latest();
    }



    public function store(CarRequest $request)
    {
        $data = $request->validated();
        if($request->image){
            $this->store_image($data,$request->image,'image');
        }

        if($request->license){
            $this->store_image($data,$request->license,'license');
        }

        Car::create(collect($request->validated())->filter()->toArray());

        return $this->sendResponse([], 'Data exited successfully');

    }

    public function edit(Car $car)
    {
        return $this->sendResponse(['car' => $car], 'Data exited successfully');
    }



    public function update(CarRequest $request, Car $car)
    {
        $data = $request->validated();
        if ($request->hasFile('image') ) {
            $this->delete_image($car->image);
            $this->store_image($data,$request->image,'image');
        }else
        $data['image'] = null;
        if ($request->hasFile('license') ) {
            $this->delete_image($car->license);
            $this->store_image($data,$request->license,'license');
        }else
            $data['license'] = null;

        $car->update(collect($data)->filter()->toArray());
        return $this->sendResponse([], 'Data exited successfully');

    }

    protected function store_image(&$data,$file,$attribute)
    {
        $image = time() . '.' .$file->getClientOriginalName();
        // picture move
        $file->storeAs('car', $image, 'general');
        $data[$attribute] = 'car/'.$image;
    }

    protected function delete_image($image_path)
    {
        if (File::exists('upload/' . $image_path) && $image_path) {
            unlink('upload/' . $image_path);
        }
    }

    public function destroy(Car $car)
    {
        $this->delete_image($car->license);
        $this->delete_image($car->image);

        $car->delete();
        return $this->sendResponse([],'Deleted successfully');

    }

}
