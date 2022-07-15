<?php

use App\SubCat1;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCat1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sub_cat_1')->truncate();
        $inputs = [
            ['id' => 1, 'cat_name' => 'American'],
            ['id' => 2, 'cat_name' => 'Bakery'],
            ['id' => 3, 'cat_name' => 'BBQ'],
            ['id' => 4, 'cat_name' => 'Bengali'],
            ['id' => 5, 'cat_name' => 'Cafe'],
            ['id' => 6, 'cat_name' => 'Chinese'],
            ['id' => 7, 'cat_name' => 'Continental'],
            ['id' => 8, 'cat_name' => 'Drinks and Juice'],
            ['id' => 9, 'cat_name' => 'English'],
            ['id' => 10, 'cat_name' => 'Sushi'],
            ['id' => 11, 'cat_name' => 'Indian'],
            ['id' => 12, 'cat_name' => 'Mediterranean'],
            ['id' => 13, 'cat_name' => 'Mexican'],
            ['id' => 14, 'cat_name' => 'Thai'],
            ['id' => 15, 'cat_name' => 'Fine Dining'],
            ['id' => 16, 'cat_name' => 'Fast Food'],
            ['id' => 17, 'cat_name' => 'French'],
            ['id' => 18, 'cat_name' => 'Fusion'],
            ['id' => 19, 'cat_name' => 'Japanese'],
            ['id' => 20, 'cat_name' => 'Korean'],
            ['id' => 21, 'cat_name' => 'Portuguese'],
            ['id' => 22, 'cat_name' => 'Steak House'],
            ['id' => 23, 'cat_name' => 'Pizza'],
            ['id' => 24, 'cat_name' => 'Turkish'],
            ['id' => 25, 'cat_name' => 'Shisha Lounge'],
            ['id' => 26, 'cat_name' => 'Men'],
            ['id' => 27, 'cat_name' => 'Women'],
            ['id' => 28, 'cat_name' => 'Kids'],
            ['id' => 29, 'cat_name' => 'Fun Activity'],
            ['id' => 30, 'cat_name' => 'Billiards & Pool'],
            ['id' => 31, 'cat_name' => 'Bowling'],
            ['id' => 32, 'cat_name' => 'Theme Parks'],
            ['id' => 33, 'cat_name' => 'Movie Theatres'],
            ['id' => 34, 'cat_name' => 'Arcade Gaming'],
            ['id' => 35, 'cat_name' => 'Hotels'],
            ['id' => 36, 'cat_name' => 'Resorts'],
            ['id' => 37, 'cat_name' => 'Italian'],
            ['id' => 38, 'cat_name' => 'Sea Food'],
            ['id' => 39, 'cat_name' => 'Ice Cream Parlor'],
            ['id' => 40, 'cat_name' => 'Kids Activity'],
        ];

        foreach ($inputs as $input) {
            SubCat1::create($input);
        }
    }
}
