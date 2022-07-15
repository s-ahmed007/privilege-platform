<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpgradeUserToPremium extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade-user-to-premium';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrading every user to premium with a long validity';

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
     * @return int
     */
    public function handle()
    {
        (new \App\Helpers\UpgradeUserToPremium())->upgradeUser();
    }
}
