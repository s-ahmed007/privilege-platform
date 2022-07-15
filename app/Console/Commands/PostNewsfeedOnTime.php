<?php

namespace App\Console\Commands;

use App\CustomerInfo;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PushNotificationType;
use App\Http\Controllers\jsonController;
use App\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PostNewsfeedOnTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postNewsfeedOnScheduledTime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Newsfeed post at scheduled time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time = date('Y-m-d H');
        $posts = Post::where('scheduled_at', 'LIKE', $time.'%')->where('moderate_status', 0)->get();

        foreach ($posts as $post) {
            $cur_post = Post::findOrFail($post->id);
            $cur_post->moderate_status = 1;
            $cur_post->push_status = 1;
            $cur_post->posted_on = $time.':00:00';
            $cur_post->save();

            $scroll_id = 0;

            $android = PlatformType::android;
            $ios = PlatformType::ios;
            $android_data = collect(DB::select("SELECT *
                                                FROM customer_login_sessions
                                                WHERE id IN (
                                                    SELECT MAX(id)
                                                    FROM customer_login_sessions
                                                    where platform = '$android'
                                                    GROUP BY customer_id)"))
                ->where('status', LoginStatus::logged_in)->pluck('physical_address');

            $ios_data = collect(DB::select("SELECT *
                                                FROM customer_login_sessions
                                                WHERE id IN (
                                                    SELECT MAX(id)
                                                    FROM customer_login_sessions
                                                    where platform = '$ios'
                                                    GROUP BY customer_id)"))
                ->where('status', LoginStatus::logged_in)->pluck('physical_address');

            $f_android_result = array_chunk($android_data->toArray(), Constants::notification_chunk);
            $f_ios_result = array_chunk($ios_data->toArray(), Constants::notification_chunk);
            foreach ($f_android_result as $customers) {
                (new jsonController)->sendFirebaseFeedNotification('Royalty', $cur_post->header, $customers, $scroll_id,
                    $cur_post->image_url, PushNotificationType::FROM_NEWSFEED);
            }
            foreach ($f_ios_result as $customers) {
                (new jsonController)->sendFirebaseIOSFeedNotification('Royalty', $cur_post->header, $customers, $scroll_id,
                    $cur_post->image_url, PushNotificationType::FROM_NEWSFEED);
            }
        }
    }
}
