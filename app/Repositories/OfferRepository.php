<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Offer;

class OfferRepository
{
    public function store($offerInput)
    {
        $offer = Offer::create($offerInput);
        return $offer;
    }

    public function update($offerInput)
    {
        $offer = Offer::find($offerInput["id"]);
        $oldImage = $offer->image;
        $offer->description_ar = $offerInput["description_ar"];
        $offer->description_en = $offerInput["description_en"];
        $offer->url = $offerInput["url"] ?? null;
        $offer->product_id = $offerInput["product_id"] ?? null;
        $offer->external = $offerInput["external"] ?? null;
        $offer->image = isset($offerInput["image"]) ? $offerInput["image"] : $oldImage;
        $offer->save();
        return ["old_image" => $oldImage, "updated_offer" => $offer];
    }

    public function delete($id)
    {
        $offer = Offer::find($id);
        $oldImage = null;
        if ($offer) {
            $oldImage = $offer->image;
            $offer->delete();
        }
        return $oldImage;
    }
    public function getPage($pageSize, $text)
    {
        return Offer::where("description_ar", "like", "%$text%")->orWhere("description_en", "like", "%$text%")->with("product")
            ->paginate($pageSize);
    }

    public function getProducts()
    {
        return Product::where("status", 1)->get();
    }

}
