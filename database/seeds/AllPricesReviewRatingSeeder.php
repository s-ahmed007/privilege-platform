<?php

use Illuminate\Database\Seeder;

class AllPricesReviewRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('all_amounts')->insert([
            'type' => 'rating',
            'price' => 1,
            'month' => 0,
        ]);

        DB::table('all_amounts')->insert([
            'type' => 'review',
            'price' => 1,
            'month' => 0,
        ]);
    }
}
