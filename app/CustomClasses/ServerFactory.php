<?php
/**
 * Created by PhpStorm.
 * User: alex602
 * Date: 14.09.18
 * Time: 16:17
 */

namespace App\CustomClasses;

use phpDocumentor\Reflection\Types\Mixed_;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ServerFactory implements MessageComponentInterface
{
    /**
     * @var \SplObjectStorage $clients
     */
    protected $clients;

    /**
     * @var array $subscriptions
     */
    private $subscriptions;

    /**
     * @var array $users
     */
    private $users;

    /**
     * @var object $processes
     */
    private $processes;

    /**
     * ServerFactory constructor.
     * @param object $process
     */
    public function __construct(&$process)
    {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
        $this->processes = $process;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->processes++;
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        $data = json_decode($msg);
        switch ($data->command) {
            case "subscribe":
                $this->subscriptions[$conn->resourceId] = $data->channel;
                break;
            case "message":
                if (isset($this->subscriptions[$conn->resourceId])) {
                    $target = $this->subscriptions[$conn->resourceId];
                    foreach ($this->subscriptions as $id=>$channel) {
                        if ($channel == $target && $id != $conn->resourceId) {
                            $this->users[$id]->send($data->message);
                        }
                    }
                }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}