<?php

namespace App\Console;

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
        \App\Console\Commands\Test::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
//        //每天执行
//        $schedule->command('AccountDay')->dailyAt('23:59');
//        //每月执行
//        $schedule->command('AccountMouth')->monthly();

        //待分享订单超时变成取消 退款 每小时执行一次
        $schedule->command('ShareOrder')->everyTenMinutes();

        //更新我的家族佣金记录，因为点击个人中心可以更新，古每天更新一次
        $schedule->command('RewardUpdate')->daily();

        //商品下架 超时状态订单自动变成下架状态
        $schedule->command('SoldOrder')->daily();

        //已发货 订单15天后，订单自动确认收货
        $schedule->command('DeliveryOrder')->daily();

        //测试执行
        //$schedule->command('Test')->everyMinute();
        $schedule->command('merchant:withdraw')
            ->withoutOverlapping()
            ->timezone('Asia/Shanghai')
            ->dailyAt('00:10');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
