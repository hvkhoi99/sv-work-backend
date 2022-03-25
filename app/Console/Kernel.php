<?php

namespace App\Console;

use App\Console\Commands\EventCommand;
use App\Console\Commands\RecruitmentCommand;
use Carbon\Carbon;
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
        //
        // 'App\Console\Commands\EventCommand',
        // 'App\Console\Commands\RecruitmentCommand'
        EventCommand::class,
        RecruitmentCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->call(function () {
        //     DB::table('events')->where('end_date', '<', Carbon::now())->update(['is_closed' => true]);
        // });
        $schedule->command('event:update')->everyMinute();
        $schedule->command('recruitment:update')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
