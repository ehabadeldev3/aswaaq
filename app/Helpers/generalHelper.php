<?php

use Illuminate\Support\Facades\File;

function current_shop_area_id()
{
    if (auth()->user()->auth_id == 2 && $client = auth()->user()->client){
        $selected_shop = $client->selected_shop;
        if ($selected_shop)
            return $selected_shop->area->id;
    }

    return 0 ;
}

function getPaginates($collection)
{
    return [
        'per_page' => $collection->perPage(),
        'path' => $collection->path(),
        'total' => $collection->total(),
        'current_page' => $collection->currentPage(),
        'next_page_url' => $collection->nextPageUrl(),
        'previous_page_url' => $collection->previousPageUrl(),
        'last_page' => $collection->lastPage(),
        'has_more_pages' => $collection->hasMorePages(),
        'from' => $collection->firstItem(),
        'to' => $collection->lastItem(),
    ];
}

function saveFile($file,$model,$folder,$action = 'store'){
    if($action == 'update'){
        deleteFile($model);
    }
    $file_size = $file->getSize();
    $file_type = $file->getMimeType();
    $image = time() . '.' . $file->getClientOriginalName();

    // picture move
    $file->storeAs($folder, $image, 'general');

    $model->media()->create([
        'file_name' => $folder.'/' . $image,
        'file_size' => $file_size,
        'file_type' => $file_type,
        'file_sort' => 1
    ]);
}

function deleteFile($model){
    if(File::exists('upload/'.$model->media->file_name)){
        unlink('upload/'. $model->media->file_name);
    }
    $model->media->delete();
}
