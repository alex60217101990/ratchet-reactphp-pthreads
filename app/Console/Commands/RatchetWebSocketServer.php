<?php

namespace App\Console\Commands;

use App\CustomClasses\ServerFactory;
use Illuminate\Console\Command;
use React\EventLoop\Factory;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class RatchetWebSocketServer extends Command
{
    /** @var \React\EventLoop\LoopInterface  */
    protected $loop;

    /** @var \SplObjectStorage $clients */
    protected $clients;

    /** @var array $subscriptions */
    private $subscriptions;

    /** @var array $users */
    private $users;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websockets:start 
    {--host= : Host for connection.} 
    {--port= : Port № for connection.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command start web socket server.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->loop = Factory::create();
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $processes = [];

        $process = (object)[
            'port' => $this->option('port'),
            'host' => $this->option('host'),
            'processed' => 0
        ];

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ServerFactory($process)
                )
            ),
            $process->port
        );

        $loop = $server->loop;
        for ($i = 1; $i < 10; $i ++) {
            $server->socket->pause();
            $processes[] = $this->fork(function () use ($server, $loop, $process) {
                $server->socket->resume();
                $loop->addSignal(SIGINT, function () use ($process, $loop) {
                    fwrite(STDERR, sprintf('%s finished running, Processed %d requests' . PHP_EOL, posix_getpid(),
                        $process->processed));
                    $loop->stop();
                });
                $loop->run();
            });
        }

        $loop->addSignal(SIGINT, function () use ($processes) {
            foreach ($processes as $pid) {
                posix_kill($pid, SIGINT);
                $status = 0;
                pcntl_waitpid($pid, $status);
            }
            $this->loop->stop();
        });

        $server->run();
       // $loop->run();
    }

    /**
     * @param callable $child
     * @return int
     */
    protected function fork(callable $child)
    {
        $pid = pcntl_fork();
        if ($pid === -1) {
            throw new \RuntimeException('Cant fork a process');
        } elseif ($pid > 0) {
            return $pid;
        } else {
            posix_setsid();
            $child();
            exit(0);
        }
    }
}
