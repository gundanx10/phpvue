<?php

namespace App\Listeners;

use App\Events\UserNotice;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserNotice  $event
     * @return void
     */
    public function handle(UserNotice $event)
    {
        //
    }
}
