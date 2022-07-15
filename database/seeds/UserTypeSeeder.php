<?php

use App\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_type')->truncate();
        $inputs = [
            ['type' => 'Gold'],
            ['type' => 'Platinum'],
            ['type' => 'Guest'],

        ];

        foreach ($inputs as $input) {
            UserType::create($input);
        }
    }
}
