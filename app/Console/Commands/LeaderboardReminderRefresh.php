<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class LeaderboardReminderRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboardReminderRefresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Leaderboard Reminders';

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
        DB::table('leaderboard_prizes')
            ->where('status', 1)
            ->update([
                'status' => 0,
            ]);
    }
}
