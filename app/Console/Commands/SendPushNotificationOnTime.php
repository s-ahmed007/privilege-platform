<?php

namespace App\Console\Commands;

use App\Http\Controllers\adminController;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\SentMessageType;
use App\Http\Controllers\jsonController;
use App\SentMessageHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendPushNotificationOnTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendPushNotificationOnTime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $messages = SentMessageHistory::where('scheduled_at', 'LIKE', $time.'%')
            ->where('type', SentMessageType::push_notification)
            ->where('sent', 0)->get();
        foreach ($messages as $message) {
            $temp_message = SentMessageHistory::findOrFail($message->id);
            $temp_message->sent = 1;
            $temp_message->save();

            $f_result = (new adminController())->getFTokensWithType($temp_message->to);

            foreach ($f_result as $customers) {
                (new adminController())->sendCustomerWisePushNotification($temp_message->title, $temp_message->body, $customers,
                    $temp_message->image_url);
            }
        }
    }
}
