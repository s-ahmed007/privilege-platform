<?php

use App\Categories;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->truncate();
        $inputs = [
            ['type' => 'beauty_and_spa', 'name' => 'Beauty & Spa'],
            ['type' => 'entertainment', 'name' => 'Entertainment'],
            ['type' => 'food_and_drinks', 'name' => 'Food & Drinks'],
            ['type' => 'getaways', 'name' => 'Getaways'],
            ['type' => 'health_and_fitness', 'name' => 'Health & Fitness'],
            ['type' => 'lifestyle', 'name' => 'Lifestyle'],
        ];

        foreach ($inputs as $input) {
            Categories::create($input);
        }
    }
}
