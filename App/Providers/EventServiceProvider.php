<?php

namespace App\Providers;

use Core\Events\EventDispatcher;
use App\Events\UserRegistered;
use App\Listeners\SendWelcomeEmail;

class EventServiceProvider
{
    /**
     * Register the listeners for the application.
     *
     * @param  \Core\Events\EventDispatcher  $dispatcher
     * @return void
     */
    public static function register(EventDispatcher $dispatcher): void
    {
        //$dispatcher->listen(UserRegistered::class, [new SendWelcomeEmail(), 'handle']); //Sample listener registration
    }
}