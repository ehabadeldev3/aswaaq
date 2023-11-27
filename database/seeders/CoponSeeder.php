<?php

namespace Database\Seeders;

use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CoponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Discount::create([
            'code'              => 'SAMI200',
            'type'              => 'fixed',
            'value'             => 200,
            'description'       => 'Discount 200 SAR on your sales on website',
            'use_times'         => 20,
            'start_date'        => Carbon::now(),
            'expire_date'       => Carbon::now()->addMonth(),
            'greater_than'      => 600,
            'status'            => 1,
        ]);

        Discount::create([
            'code'              => 'FiftyFifty',
            'type'              => 'percentage',
            'value'             => 50,
            'description'       => 'Discount 50% on your sales on website',
            'use_times'         => 5,
            'start_date'        => Carbon::now(),
            'expire_date'       => Carbon::now()->addWeek(),
            'greater_than'      => null,
            'status'            => 1,
        ]);
    }
}
