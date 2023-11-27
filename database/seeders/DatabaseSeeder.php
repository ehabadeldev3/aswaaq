<?php

namespace Database\Seeders;

use App\Models\Income;
use App\Models\Setting;
use App\Models\Terms;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);
        $this->call(CreateAdminSeeder::class);
        $this->call(CoponSeeder::class);
        $this->call(JobSeeder::class);
        $this->call(ProvinceSeeder::class);
        $this->call(AreaSeeder::class);
        $this->call(MainAccountTableSeeder::class);
        $this->call(SubAccountTableSeeder::class);
        $this->call(SuggestionSeeder::class);
        $this->call(TreasuryTableSeeder::class);
        $this->call(IncomeTableSeeder::class);
        $this->call(ExpenseTableSeeder::class);
        Terms::create();
        Setting::create();
    }
}
