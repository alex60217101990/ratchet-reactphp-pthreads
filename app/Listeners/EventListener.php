<?php

namespace App\Listeners;

use App\Events\Event;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class EventListener //implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param Event $event
     * @return string | null
     */
    public function onEventHandler(Event $event)
    {
        return $event->getEventData();
    }

    /**
     * Handle a job failure.
     *
     * @param  \App\Events\Event  $event
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(Event $event, $exception)
    {
        Log::error('EventListener class error => '. $exception);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Event',
            'App\Listeners\EventListener@onEventHandler'
        );
    }
}
