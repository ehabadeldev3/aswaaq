<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\OfferRequest;
use App\Repositories\OfferRepository;
use Illuminate\Support\Facades\Storage;

class OfferController extends Controller
{
    private $offerRepository;
    public function __construct(OfferRepository $offerRepository)
    {
        $this->middleware('permission:offer read', ['only' => ['index', 'getProducts']]);
        $this->middleware('permission:offer create', ['only' => ['store']]);
        $this->middleware('permission:offer edit', ['only' => ['update']]);
        $this->middleware('permission:offer delete', ['only' => ['delete']]);
        $this->offerRepository = $offerRepository;
    }

    public function store(OfferRequest $request)
    {
        $image = $request->file("image")->store('offers', 'general');
        $request->merge(["image" => $image]);
        $offer = $this->offerRepository->store($request->input());
        return $offer;
    }

    public function update(OfferRequest $request)
    {
        $image = "";
        if ($request->file("image")) {
            $image = $request->file("image")->store('offers', 'general');
            $request->merge(["image" => $image]);
        }
        $updateResult = $this->offerRepository->update($request->input());
        if ($request->file("image")) {
            Storage::disk('general')->delete($updateResult["old_image"]);
        }
        return $updateResult["updated_offer"];
    }

    public function destroy($id)
    {
        $oldImage = $this->offerRepository->delete($id);
        if ($oldImage) {
            Storage::disk('general')->delete("$oldImage");
        }
    }

    public function index()
    {
        $text = isset(request()->text) ? request()->text : '';
        return $this->offerRepository->getPage(request()->page_size, $text);
    }

    public function getProducts()
    {
        return $this->offerRepository->getProducts();
    }
}
