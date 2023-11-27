<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AdsRequest;
use App\Interfaces\AdsInterface;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdsController extends Controller
{
    use Message;

    protected $adsRepository;

    public function __construct(AdsInterface $adsRepository)
    {
        $this->middleware('permission:SliderAds read', ['only' => ['index','show']]);
        $this->middleware('permission:SliderAds create', ['only' => ['store']]);
        $this->middleware('permission:SliderAds edit', ['only' => ['update', 'edit']]);
        $this->middleware('permission:SliderAds delete', ['only' => ['destroy']]);
        $this->adsRepository = $adsRepository;
    }

    public function index(Request $request)
    {
        return $this->sendResponse(['ads' => $this->adsRepository->getAds($request)], 'Data exited successfully');
    }

    public function store(AdsRequest $request)
    {
        $data = $request->only([$request->type =='product' ? 'product_id' : 'sub_category_id','place', 'file']);
        $this->adsRepository->createAds($data);
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function show($id)
    {
        return $this->sendResponse(['ad' => $this->adsRepository->getAdsById($id)], 'Data exited successfully');
    }

    public function edit($id)
    {
        return $this->sendResponse(['ad' => $this->adsRepository->getAdsById($id)], 'Data exited successfully');
    }

    public function update(AdsRequest $request, $id)
    {
        $data = $request->only([$request->type =='product' ? 'product_id' : 'sub_category_id','place', 'file']);
        $data ['product_id']= $request->type =='product' ? $request->product_id : null ;
        $data ['sub_category_id']= $request->type =='sub_category' ? $request->sub_category_id : null ;
        $data['has_file'] = $request->hasFile('file') ? 1 : 0;
        $this->adsRepository->updateAds($id, $data);

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function destroy($id)
    {
        $this->adsRepository->deleteAds($id);
        return $this->sendResponse([], 'Data exited successfully');
    }
}
