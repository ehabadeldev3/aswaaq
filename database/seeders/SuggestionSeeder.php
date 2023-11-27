<?php

namespace Database\Seeders;

use App\Models\Suggestion;
use Illuminate\Database\Seeder;

class SuggestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Suggestion::create([
            'name_ar'=>'اقتراح سعر منتج معين',
            'name_en'=>'Suggest a price for a specific product',
        ]);

        Suggestion::create([
            'name_ar'=>'نقص منتج معين',
            'name_en'=>'Deficiency of a particular product',
        ]);

        Suggestion::create([
            'name_ar'=>'تطبيق على مستوى التواصل',
            'name_en'=>'Communication level application',
        ]);

        Suggestion::create([
            'name_ar'=>'تعليق على البرنامج',
            'name_en'=>'Comment on the program',
        ]);

        Suggestion::create([
            'name_ar'=>'اقتراحات اخرى',
            'name_en'=>'Other suggestions',
        ]);
    }
}
