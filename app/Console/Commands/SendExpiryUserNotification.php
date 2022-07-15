<?php

namespace App\Console\Commands;

use App\CustomerInfo;
use App\Helpers\UserExpiryNotification;
use App\Http\Controllers\adminController;
use App\Http\Controllers\Enum\Constants;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SendExpiryUserNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendUserExpiryNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To send all users expiry notification every 5 days till 0 days.';

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
        (new UserExpiryNotification())->sendNotification();
    }
}
