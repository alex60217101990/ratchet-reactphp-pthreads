<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessagePusher implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var string $msg */
    public $msg;

    /** @var int */
    public $id;

    /**
     * Create a new event instance.
     *
     * @param int $id
     * @param string $message
     */
    public function __construct(int $id, string $message)
    {
        $this->id = $id;
        $this->msg = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('survey.'.$this->id);
       // return new PresenceChannel('survey.' . $this->survey->id);

    }
}
