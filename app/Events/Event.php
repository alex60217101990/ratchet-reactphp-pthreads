<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var string | null $event_data */
    private $event_data;

    /**
     * Create a new event instance.
     * @param string | null $some_data
     * @return void
     */
    public function __construct(?string $some_data)
    {
        $this->event_data = $some_data;
    }

    /**
     * @param void
     * @return null|string
     */
    public function getEventData(): ?string
    {
        return $this->event_data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
