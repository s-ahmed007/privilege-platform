<?php

use App\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(AdminSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(NotificationTypeSeeder::class);
        $this->call(UserTypeSeeder::class);
        $this->call(SubCat1Seeder::class);
        $this->call(SubCat2Seeder::class);
        $this->call(CategoryRelationSeeder::class);
    }
}
