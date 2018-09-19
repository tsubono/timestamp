<?php

namespace App\Console;

use App\Console\Commands\DailyChargeCommand;
use App\Console\Commands\InsertTestDataForPaymentCommand;
use App\Console\Commands\migrate\CreateDatabaseCommand;
use App\Console\Commands\InsertTestDataCommand;
use App\Console\Commands\migrate\MigrateForCustomerCommand;
use App\Console\Commands\migrate\MigrateForCustomerAllCommand;
use App\Console\Commands\migrate\RollbackForCustomerCommand;
use App\Console\Commands\UpdateChargeDateForTestCommand;
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
        MigrateForCustomerCommand::class,
        MigrateForCustomerAllCommand::class,
        RollbackForCustomerCommand::class,
        CreateDatabaseCommand::class,
        DailyChargeCommand::class,
        InsertTestDataCommand::class,
        UpdateChargeDateForTestCommand::class,
        InsertTestDataForPaymentCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Daily決済処理
        $schedule->command('daily:charge')
            ->appendOutputTo(storage_path() . '/logs/charge_'.Carbon::now()->format('Ymd').'.log')
            ->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
