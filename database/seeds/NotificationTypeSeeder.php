<?php

use App\NotificationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notification_type')->truncate();
        $inputs = [
            ['type' => 'like_review'],
            ['type' => 'post_review'],
            ['type' => 'discount'],
            ['type' => 'partner_following'],
            ['type' => 'birthday'],
            ['type' => 'reply_review'],
            ['type' => 'like_post'],
            ['type' => 'customer_following'],
            ['type' => 'accept_follow_request'],
            ['type' => 'refer'],
            ['type' => '250tk_coupon'],
        ];

        foreach ($inputs as $input) {
            NotificationType::create($input);
        }
    }
}
