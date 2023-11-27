<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Province::create([
            "name_ar"=>"القاهرة",
            "name_en"=>"Cairo"
        ]);

        Province::create([
            "name_ar"=>"الجيزة",
            "name_en"=>"Giza"
        ]);
    }
}
