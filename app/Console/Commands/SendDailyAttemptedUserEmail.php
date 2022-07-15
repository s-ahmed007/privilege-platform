<?php

namespace App\Console\Commands;

use App\Http\Controllers\adminController;
use App\Http\Controllers\functionController2;
use App\InfoAtBuyCard;
use DateTime;
use Illuminate\Console\Command;

class SendDailyAttemptedUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendDailyAttemptedUserEmail';

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
        $users = InfoAtBuyCard::with('info')->orderByDesc('id')->get()->unique('customer_id');
//        $users = $users->where('info.customer_type', 3);
        $emails = [];
        foreach ($users as $user) {
            if ($user->order_date) {
                try {
                    $createDate = new DateTime($user->order_date);
                    $strip = $createDate->format('Y-m-d');
                    if ((new functionController2())->daysRemaining($strip) == -1) {
                        array_push($emails, $user->customer_email);
                    }
                } catch (\Exception $e) {
                }
            }
        }
        //send email
        if (count($emails) > 0) {
            (new adminController())->sendAttemptedUserEmail($emails);
        }
    }
}
