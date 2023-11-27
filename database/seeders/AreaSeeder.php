<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Area::create([
            "name_ar"=>"المرج",
            "name_en"=>"Elmarg",
            "province_id"=>1,
            'shipping_price' => 0
        ]);

        Area::create([
            "name_ar"=>"عين شمس",
            "name_en"=>"Ain shams",
            "province_id"=>1,
            'shipping_price' => 0
        ]);
        Area::create([
            "name_ar"=>"مدينة نصر",
            "name_en"=>"Nasr city",
            "province_id"=>1,
            'shipping_price' => 0
        ]);

        Area::create([
            "name_ar"=>"الدقى",
            "name_en"=>"EL dokki",
            "province_id"=>2,
            'shipping_price' => 0
        ]);
        Area::create([
            "name_ar"=>"الهرم",
            "name_en"=>"Al haram",
            "province_id"=>2,
            'shipping_price' => 0
        ]);
        Area::create([
            "name_ar"=>"فيصل",
            "name_en"=>"Fisal",
            "province_id"=>2,
            'shipping_price' => 0
        ]);
    }
}
