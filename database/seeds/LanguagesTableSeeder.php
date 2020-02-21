<?php

use Illuminate\Database\Seeder;

/**
 * Class LanguagesTableSeeder
 */
class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [

            [
                'id' => 1,
                'short_name' => 'uz',
                'long_name' => 'Uzbek latin'
            ],
            [
                'id' => 2,
                'short_name' => 'ru',
                'long_name' => 'Русский'
            ],
            [
                'id' => 3,
                'short_name' => 'en',
                'long_name' => 'English'
            ],
            [
                'id' => 4,
                'short_name' => 'oz',
                'long_name' => 'Ўзбек кирил'
            ],
        ];

        DB::table('languages')->insert($languages);
    }
}
