<?php

use App\CardPromoType;
use Illuminate\Database\Seeder;

class CardPromoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('card_promo_type')->truncate();
        $inputs = [
            ['type' => 'flat_rate'],
            ['type' => 'percentage'],
        ];

        foreach ($inputs as $input) {
            CardPromoType::create($input);
        }
    }
}
