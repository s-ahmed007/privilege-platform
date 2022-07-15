<?php

namespace App\Console\Commands;

use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerInfo;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeactivateExpiredPartner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactivate:expired-partner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate partners when they are expired';

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
        $time = date('Y-m-d');

        $expired_partners = PartnerInfo::where('expiry_date', '<', $time)->get();

        foreach ($expired_partners as $partner) {
            PartnerAccount::where('partner_account_id', $partner->partner_account_id)
                ->update(['active' => 0]);
            PartnerBranch::where('partner_account_id', $partner->partner_account_id)
                ->update(['active' => 0]);
        }
    }
}
