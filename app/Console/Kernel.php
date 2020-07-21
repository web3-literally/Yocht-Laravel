<?php

namespace App\Console;

use App\Console\Commands\Billing\BillingPeriodEndsReminder;
use App\Console\Commands\Billing\ProcessExtraCrewMemberOffer;
use App\Console\Commands\Billing\ProcessExtraTenderOffer;
use App\Console\Commands\Billing\ProcessExtraVesselOffer;
use App\Console\Commands\Events\CleanupEvents;
use App\Console\Commands\Index\BlogPosts;
use App\Console\Commands\Index\Events;
use App\Console\Commands\Index\FavoriteJobs;
use App\Console\Commands\Index\Jobs;
use App\Console\Commands\Index\Members;
use App\Console\Commands\Index\FavoriteMembers;
use App\Console\Commands\Index\Classifieds;
use App\Console\Commands\Index\Reviews;
use App\Console\Commands\Index\VesselAttachments;
use App\Console\Commands\Members\PaymentInfoEndsReminder;
use App\Console\Commands\Vessels\TransferProcess;
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
        Commands\SMTPTest::class,

        Commands\SyncPlans::class,

        Commands\Index::class,
        Members::class,
        Reviews::class,
        FavoriteMembers::class,
        BlogPosts::class,
        Events::class,
        Jobs::class,
        FavoriteJobs::class,
        Classifieds::class,
        VesselAttachments::class,

        ProcessExtraCrewMemberOffer::class,
        ProcessExtraVesselOffer::class,
        ProcessExtraTenderOffer::class,

        BillingPeriodEndsReminder::class,
        PaymentInfoEndsReminder::class,

        TransferProcess::class,

        CleanupEvents::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('news:fetch')->everyFifteenMinutes()->withoutOverlapping();
        $schedule->command('braintree:sync-plans')->daily()->withoutOverlapping();

        $schedule->command('extra-offer:crew-member:process')->everyMinute()->withoutOverlapping();
        //$schedule->command('extra-offer:vessel:process')->everyMinute()->withoutOverlapping();
        //$schedule->command('extra-offer:tender:process')->everyMinute()->withoutOverlapping();

        $schedule->command('сlassifieds:process')->daily()->withoutOverlapping();
        $schedule->command('reminder:classifieds:process')->daily()->withoutOverlapping();

        $schedule->command('reminder:billing:process')->daily()->withoutOverlapping();
        $schedule->command('reminder:payment-info:process')->daily()->withoutOverlapping();

        $schedule->command('vessel-transfer:process')->hourly()->withoutOverlapping();

        $schedule->command('events:cleanup')->everyFiveMinutes()->withoutOverlapping();
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
