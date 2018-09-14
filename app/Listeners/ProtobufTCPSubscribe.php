<?php

namespace App\Listeners;

use App\CustomClasses\Interfaces\GlobalConstants;
use App\Events\ProtobufTCPEvent;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redis;
use Proto\Data\User;
use Proto\Data\UserRegisterRequest;
use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

class ProtobufTCPSubscribe implements GlobalConstants
{
    /** @var \React\EventLoop\LoopInterface  */
    protected $loop;

    /** @var Connector $connector */
    protected $connector;

    /** @var string $host */
    protected $host;

    /** @var string $port */
    protected $port;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->loop = Factory::create();//app('eventLoop');
    }

    /**
     * @param ProtobufTCPEvent $event
     */
    public function onMessage(ProtobufTCPEvent $event):void
    {
//        $req = (new UserRegisterRequest())->setUser((new User())
//            ->setName($event->getData()['name'])
//            ->setEmail($event->getData()['email'])
//            ->setPassword($event->getData()['password']));

    //    $req = /*'add' . */$req->serializeToString();
        $request = json_encode([
            'class' => UserRegisterRequest::class,
            'method' => 'setName',
            'request' => 'jggn'//$event->getData()['name']
        ]);
       // Redis::set(ProtobufTCPEvent::class, $event->getData());
        Redis::publish('message-channel', $request);
    }

    public function handle(){}

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\ProtobufTCPEvent',
            'App\Listeners\ProtobufTCPSubscribe@onMessage'
        );
    }

    public function __destruct()
    {
     //   $this->loop->stop();
    }
}
