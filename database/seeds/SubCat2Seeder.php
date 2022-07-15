<?php

use App\SubCat2;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCat2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sub_cat_2')->truncate();
        $inputs = [
            ['id' => 1, 'cat_name' => 'Clothing'],
            ['id' => 2, 'cat_name' => 'Footwear'],
            ['id' => 3, 'cat_name' => 'Jewelry, Gifts & Accessories'],
            ['id' => 4, 'cat_name' => 'Salons'],
            ['id' => 5, 'cat_name' => 'Face & Skin'],
            ['id' => 6, 'cat_name' => 'Hair'],
            ['id' => 7, 'cat_name' => 'Massages'],
            ['id' => 8, 'cat_name' => 'Nails'],
            ['id' => 9, 'cat_name' => 'Cosmetic Procedures'],
            ['id' => 10, 'cat_name' => 'Makeup'],
            ['id' => 11, 'cat_name' => 'Brows & Lashes'],
            ['id' => 13, 'cat_name' => 'Shower Facilities'],
            ['id' => 14, 'cat_name' => 'Swimming'],
            ['id' => 15, 'cat_name' => 'Jacuzzi'],
            ['id' => 16, 'cat_name' => 'Steam'],
            ['id' => 17, 'cat_name' => 'Sauna'],
            ['id' => 18, 'cat_name' => 'Gym'],
            ['id' => 19, 'cat_name' => 'Yoga & Therapy'],
            ['id' => 20, 'cat_name' => 'Restaurants'],
            ['id' => 21, 'cat_name' => 'Bars'],
            ['id' => 22, 'cat_name' => 'Swimming Pool'],
            ['id' => 23, 'cat_name' => 'Fitness Centre'],
            ['id' => 24, 'cat_name' => 'Leisure Activities'],
            ['id' => 25, 'cat_name' => 'Beauty & Cosmetics'],
            ['id' => 26, 'cat_name' => 'Kids Play Zone'],
        ];

        foreach ($inputs as $input) {
            SubCat2::create($input);
        }
    }
}
