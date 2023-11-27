<?php

namespace App\Repositories;

use App\Interfaces\AdsInterface;
use App\Models\Ad;
use Illuminate\Support\Facades\File;

class AdsRepository implements AdsInterface
{
    public function getAds($request)
    {
        return Ad::where('place',$request->place)->with(['media','product','sub_category'])->when($request->search,function ($q) use($request){
            return $q->whereRelation('product','name','like','%'.$request->search.'%');
        })->latest()->paginate(10);
    }

    public function getAdsById($id)
    {
        return Ad::with(['media','product','sub_category'])->findOrFail($id);
    }

    public function deleteAds($id)
    {
        $ads = Ad::findOrFail($id);
        if (File::exists('upload/' . $ads->media->file_name)) {
            unlink('upload/' . $ads->media->file_name);
        }
        $ads->media->delete();
        $ads->delete();
    }

    public function createAds(array $request_data)
    {
        $ads = Ad::create($request_data);

        saveFile($request_data['file'],$ads,'ads');
    }

    public function updateAds($id, array $newDetails)
    {
        $ads = Ad::findOrFail($id);
        $ads->update($newDetails);

        if ($newDetails['has_file'])
            saveFile($newDetails['file'],$ads,'ads','update');
    }
}
