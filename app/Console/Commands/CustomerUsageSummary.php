<?php

namespace App\Console\Commands;

use App\AdminActivityNotification;
use App\CustomerPoint;
use App\Http\Controllers\adminController;
use App\Http\Controllers\Enum\AdminNotificationType;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\functionController2;
use App\PartnerBranch;
use App\Review;
use App\TransactionTable;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CustomerUsageSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CustomerUsageSummary';

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
        $prevmonth = date('Y-m', strtotime('last month'));
        // $branch_count = PartnerBranch::where('created_at', 'like', $prevmonth.'%')->count();

        $branch_count = \App\AdminActivityNotification::where('type', \App\Http\Controllers\Enum\AdminNotificationType::new_branch_added)->where('created_at', 'like', $prevmonth.'%')->count();

        $tran_data = TransactionTable::where('posted_on', 'like', $prevmonth.'%')->with('customer')->get()->groupBy('customer_id');
        $review_data = Review::where('posted_on', 'like', $prevmonth.'%')->with('customer')->get()->groupBy('customer_id');
        $point_data = CustomerPoint::where('created_at', 'like', $prevmonth.'%')->with('customer')->get()->groupBy('customer_id');

        $user_info = [];
        $i = 0;
        //transaction
        foreach ($tran_data as $key => $value) {
            $user_info[$i]['customer_id'] = $key;
            $user_info[$i]['email'] = $value[0]->customer->customer_email;
            $user_info[$i]['total_scan'] = count($value);
            $user_info[$i]['outlet_visited'] = $value->groupBy('branch_id')->count('branch_id');
            $user_info[$i]['earned_point'] = $value->sum('transaction_point');
            $user_info[$i]['total_review'] = 0;
            $i++;
        }
        //review
        foreach ($review_data as $key => $value) {
            if (in_array($key, array_column($user_info, 'customer_id'))) { // search id array
                $index = array_search($key, array_column($user_info, 'customer_id'));
                $user_info[$index]['total_review'] = count($value);
            } else {
                $user_info[$i]['customer_id'] = $key;
                $user_info[$i]['email'] = $value[0]->customer->customer_email;
                $user_info[$i]['total_scan'] = 0;
                $user_info[$i]['outlet_visited'] = 0;
                $user_info[$i]['earned_point'] = 0;
                $user_info[$i]['total_review'] = count($value);
                $i++;
            }
        }
        //point
        foreach ($point_data as $key => $value) {
            if (in_array($key, array_column($user_info, 'customer_id'))) { // search id array
                $index = array_search($key, array_column($user_info, 'customer_id'));
                $user_info[$index]['earned_point'] += $value->sum('point');
            } else {
                $user_info[$i]['customer_id'] = $key;
                $user_info[$i]['email'] = $value[0]->customer->customer_email;
                $user_info[$i]['total_scan'] = 0;
                $user_info[$i]['outlet_visited'] = 0;
                $user_info[$i]['earned_point'] = $value->sum('point');
                $user_info[$i]['total_review'] = 0;
                $i++;
            }
        }

        $list = array_chunk($user_info, \App\Http\Controllers\Enum\Constants::notification_chunk);
        foreach ($list as $customers) {
            $emails = Arr::pluck($customers, 'email');
            try {
                $mg = (new functionController2())->getMailGun();
                $mg->messages()->send('mail.royaltybd.com', [
                    'from' => 'Royalty no-reply@royaltybd.com',
                    'to' => $emails,
                    'recipient-variables' => (new adminController())->getRecipientJson($customers, true),
                    'subject' => date('F', strtotime('last month')).' in review!',
                    'html' => view('emails.usage_summary', ['branch_count' => $branch_count])->render(),
                ]);
            } catch (\Exception $exception) {
                //
            }
        }
    }
}
