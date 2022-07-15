<?php

use App\CategoryRelation;
use Illuminate\Database\Seeder;

class CategoryRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_relation')->truncate();
        $inputs = [
            ['id' => 1, 'main_cat' => 3, 'sub_cat_1_id' => 1, 'sub_cat_2_id' => null],
            ['id' => 3, 'main_cat' => 3, 'sub_cat_1_id' => 2, 'sub_cat_2_id' => null],
            ['id' => 4, 'main_cat' => 3, 'sub_cat_1_id' => 3, 'sub_cat_2_id' => null],
            ['id' => 5, 'main_cat' => 3, 'sub_cat_1_id' => 4, 'sub_cat_2_id' => null],
            ['id' => 8, 'main_cat' => 3, 'sub_cat_1_id' => 5, 'sub_cat_2_id' => null],
            ['id' => 9, 'main_cat' => 3, 'sub_cat_1_id' => 6, 'sub_cat_2_id' => null],
            ['id' => 10, 'main_cat' => 3, 'sub_cat_1_id' => 7, 'sub_cat_2_id' => null],
            ['id' => 11, 'main_cat' => 3, 'sub_cat_1_id' => 8, 'sub_cat_2_id' => null],
            ['id' => 12, 'main_cat' => 3, 'sub_cat_1_id' => 9, 'sub_cat_2_id' => null],
            ['id' => 13, 'main_cat' => 3, 'sub_cat_1_id' => 16, 'sub_cat_2_id' => null],
            ['id' => 14, 'main_cat' => 3, 'sub_cat_1_id' => 15, 'sub_cat_2_id' => null],
            ['id' => 15, 'main_cat' => 3, 'sub_cat_1_id' => 17, 'sub_cat_2_id' => null],
            ['id' => 16, 'main_cat' => 3, 'sub_cat_1_id' => 18, 'sub_cat_2_id' => null],
            ['id' => 17, 'main_cat' => 3, 'sub_cat_1_id' => 11, 'sub_cat_2_id' => null],
            ['id' => 18, 'main_cat' => 3, 'sub_cat_1_id' => 19, 'sub_cat_2_id' => null],
            ['id' => 19, 'main_cat' => 3, 'sub_cat_1_id' => 20, 'sub_cat_2_id' => null],
            ['id' => 20, 'main_cat' => 3, 'sub_cat_1_id' => 12, 'sub_cat_2_id' => null],
            ['id' => 21, 'main_cat' => 3, 'sub_cat_1_id' => 13, 'sub_cat_2_id' => null],
            ['id' => 22, 'main_cat' => 3, 'sub_cat_1_id' => 23, 'sub_cat_2_id' => null],
            ['id' => 23, 'main_cat' => 3, 'sub_cat_1_id' => 21, 'sub_cat_2_id' => null],
            ['id' => 24, 'main_cat' => 3, 'sub_cat_1_id' => 25, 'sub_cat_2_id' => null],
            ['id' => 25, 'main_cat' => 3, 'sub_cat_1_id' => 22, 'sub_cat_2_id' => null],
            ['id' => 26, 'main_cat' => 3, 'sub_cat_1_id' => 10, 'sub_cat_2_id' => null],
            ['id' => 27, 'main_cat' => 3, 'sub_cat_1_id' => 14, 'sub_cat_2_id' => null],
            ['id' => 28, 'main_cat' => 3, 'sub_cat_1_id' => 24, 'sub_cat_2_id' => null],
            ['id' => 32, 'main_cat' => 6, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 1],
            ['id' => 33, 'main_cat' => 6, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 2],
            ['id' => 34, 'main_cat' => 6, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 3],
            ['id' => 35, 'main_cat' => 6, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 1],
            ['id' => 36, 'main_cat' => 6, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 2],
            ['id' => 37, 'main_cat' => 6, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 3],
            ['id' => 38, 'main_cat' => 6, 'sub_cat_1_id' => 28, 'sub_cat_2_id' => 1],
            ['id' => 39, 'main_cat' => 6, 'sub_cat_1_id' => 28, 'sub_cat_2_id' => 2],
            ['id' => 40, 'main_cat' => 2, 'sub_cat_1_id' => 29, 'sub_cat_2_id' => null],
            ['id' => 41, 'main_cat' => 2, 'sub_cat_1_id' => 30, 'sub_cat_2_id' => null],
            ['id' => 42, 'main_cat' => 2, 'sub_cat_1_id' => 31, 'sub_cat_2_id' => null],
            ['id' => 43, 'main_cat' => 2, 'sub_cat_1_id' => 32, 'sub_cat_2_id' => null],
            ['id' => 44, 'main_cat' => 2, 'sub_cat_1_id' => 33, 'sub_cat_2_id' => null],
            ['id' => 45, 'main_cat' => 2, 'sub_cat_1_id' => 34, 'sub_cat_2_id' => null],
            ['id' => 46, 'main_cat' => 1, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 4],
            ['id' => 47, 'main_cat' => 1, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 5],
            ['id' => 48, 'main_cat' => 1, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 6],
            ['id' => 49, 'main_cat' => 1, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 7],
            ['id' => 50, 'main_cat' => 1, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 8],
            ['id' => 51, 'main_cat' => 1, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 9],
            ['id' => 52, 'main_cat' => 1, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 4],
            ['id' => 53, 'main_cat' => 1, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 5],
            ['id' => 54, 'main_cat' => 1, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 6],
            ['id' => 55, 'main_cat' => 1, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 7],
            ['id' => 56, 'main_cat' => 1, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 8],
            ['id' => 57, 'main_cat' => 1, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 9],
            ['id' => 58, 'main_cat' => 5, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 13],
            ['id' => 59, 'main_cat' => 5, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 14],
            ['id' => 60, 'main_cat' => 5, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 15],
            ['id' => 61, 'main_cat' => 5, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 16],
            ['id' => 62, 'main_cat' => 5, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 17],
            ['id' => 63, 'main_cat' => 5, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 18],
            ['id' => 64, 'main_cat' => 5, 'sub_cat_1_id' => 26, 'sub_cat_2_id' => 19],
            ['id' => 65, 'main_cat' => 5, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 13],
            ['id' => 66, 'main_cat' => 5, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 14],
            ['id' => 67, 'main_cat' => 5, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 15],
            ['id' => 68, 'main_cat' => 5, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 16],
            ['id' => 69, 'main_cat' => 5, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 17],
            ['id' => 70, 'main_cat' => 5, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 18],
            ['id' => 71, 'main_cat' => 5, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 19],
            ['id' => 72, 'main_cat' => 4, 'sub_cat_1_id' => 35, 'sub_cat_2_id' => 20],
            ['id' => 73, 'main_cat' => 4, 'sub_cat_1_id' => 35, 'sub_cat_2_id' => 21],
            ['id' => 74, 'main_cat' => 4, 'sub_cat_1_id' => 35, 'sub_cat_2_id' => 22],
            ['id' => 75, 'main_cat' => 4, 'sub_cat_1_id' => 35, 'sub_cat_2_id' => 23],
            ['id' => 76, 'main_cat' => 4, 'sub_cat_1_id' => 35, 'sub_cat_2_id' => 24],
            ['id' => 77, 'main_cat' => 4, 'sub_cat_1_id' => 36, 'sub_cat_2_id' => 20],
            ['id' => 78, 'main_cat' => 4, 'sub_cat_1_id' => 36, 'sub_cat_2_id' => 21],
            ['id' => 79, 'main_cat' => 4, 'sub_cat_1_id' => 36, 'sub_cat_2_id' => 22],
            ['id' => 80, 'main_cat' => 4, 'sub_cat_1_id' => 36, 'sub_cat_2_id' => 23],
            ['id' => 81, 'main_cat' => 4, 'sub_cat_1_id' => 36, 'sub_cat_2_id' => 24],
            ['id' => 82, 'main_cat' => 1, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 10],
            ['id' => 83, 'main_cat' => 1, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 11],
            ['id' => 87, 'main_cat' => 3, 'sub_cat_1_id' => 37, 'sub_cat_2_id' => null],
            ['id' => 88, 'main_cat' => 3, 'sub_cat_1_id' => 38, 'sub_cat_2_id' => null],
            ['id' => 89, 'main_cat' => 3, 'sub_cat_1_id' => 39, 'sub_cat_2_id' => null],
            ['id' => 90, 'main_cat' => 6, 'sub_cat_1_id' => 27, 'sub_cat_2_id' => 25],
            ['id' => 91, 'main_cat' => 2, 'sub_cat_1_id' => 40, 'sub_cat_2_id' => null],
            ['id' => 92, 'main_cat' => 4, 'sub_cat_1_id' => 36, 'sub_cat_2_id' => 26],
        ];

        foreach ($inputs as $input) {
            CategoryRelation::create($input);
        }
    }
}
