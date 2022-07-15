<?php

namespace App\Console;

use App\Console\Commands\DeactivateExpiredPartner;
use App\Console\Commands\LeaderboardReminder;
use App\Console\Commands\LeaderboardReminderRefresh;
use App\Console\Commands\SendBirthdayWish;
use App\Console\Commands\SendExpiryUserNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DeactivateExpiredPartner::class,
        SendBirthdayWish::class,
        LeaderboardReminderRefresh::class,
        SendExpiryUserNotification::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('deactivate:expired-partner')->daily();
//        $schedule->command('SendUserExpiryNotification')->daily();
//        $schedule->command('SendDailyAttemptedUserEmail')->daily();
        $schedule->command('PartnerExpiryAdminNotification')->daily();
        //$schedule->command('SendBirthdayWish')->daily();
        //$schedule->command('leaderboardReminderRefresh')->monthly();
        $schedule->command('postNewsfeedOnScheduledTime')->hourly();
        $schedule->command('SendPushNotificationOnTime')->hourly();
        //$schedule->command('CustomerUsageSummary')->monthlyOn(7, '10:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
