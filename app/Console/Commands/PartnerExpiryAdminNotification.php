<?php

namespace App\Console\Commands;

use App\Http\Controllers\AdminNotification\functionController;
use App\PartnerInfo;
use Illuminate\Console\Command;

class PartnerExpiryAdminNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PartnerExpiryAdminNotification';

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
        $today = date('Y-m-d');
        $date_after_5 = date('Y-m-d', strtotime('+5 day', strtotime($today)));
        //partners will be expired after 5 days
        $partners1 = PartnerInfo::where('expiry_date', $date_after_5)->get();
        foreach ($partners1 as $partner) {
            $msg = $partner->partner_name.' will be expiring in 5 days.';
            (new functionController())->partnerExpiryNotification($partner->partner_account_id, $msg);
        }
        //partners will be expired today
        $partners2 = PartnerInfo::where('expiry_date', $today)->get();
        foreach ($partners2 as $partner) {
            $msg = $partner->partner_name.' will be expiring today.';
            (new functionController())->partnerExpiryNotification($partner->partner_account_id, $msg);
        }
    }
}
