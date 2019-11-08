<?php

namespace App\Console;

use App\Console\Commands\Import\ImportBadges;
use App\Console\Commands\Import\ImportUsers;
use App\Console\Commands\Import\ImportCML;
use App\Console\Commands\Import\ImportCMLBackfill;
use App\Console\Commands\Import\Sluggify;
use App\Console\Commands\Import\CopyCourse;
use App\Console\Commands\Import\FixPermissionsForCop;
use App\Console\Commands\Infusionsoft\GetRenewedSubscriptions;
use App\Console\Commands\Infusionsoft\RefreshTokenInfusionsoft;
use App\Console\Commands\Tasks\SendAbandonedCartReminderEmails;
use App\Console\Commands\Tasks\SendSubscriptionCancellationNotifications;
use App\Console\Commands\Tasks\SendSubscriptionPaymentsReminderEmails;
use App\Console\Commands\Tasks\UpdateUserActivities;
use App\Console\Commands\Workflows\WorkflowsEnroll;
use App\Console\Commands\Workflows\WorkflowsNodeHandler;
use App\Console\Commands\Tasks\UpdateStudentCounts;
use App\Console\Commands\Workflows\Email\GetStatistics;
use App\Console\Commands\Workflows\Email\CompileStats;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('refresh:tokens')->everyFiveMinutes();
        $schedule->command('subscriptions:renew')->twiceDaily(6, 18);
        $schedule->command('course:refunds')->everyMinute();

        $schedule->command('notify:subscription-cancellations')->dailyAt('09:00');
        $schedule->command('send:payment-reminders')->twiceDaily(9, 21);
        $schedule->command('send:cart-reminders')->hourly();

        $schedule->command('task:count_students')->everyFiveMinutes();
        $schedule->command('task:update_activities')->everyMinute();

        $schedule->command('workflows:enroll')->everyMinute();
        $schedule->command('workflows:node_handler')->everyMinute();
        $schedule->command('workflows:get-email-stats')->hourly()->after(function () {
            $this->artisan->call('workflows:compile-stats');
        });
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // Autoloads the available Commands.
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
