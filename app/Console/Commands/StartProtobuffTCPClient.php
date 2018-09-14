<?php

namespace App\Console\Commands;

use App\CustomClasses\Interfaces\GlobalConstants;
use App\Events\ProtobufTCPEvent;
use Clue\React\Redis\Client;
use Exception;
use Illuminate\Console\Command;
use React\EventLoop\Factory;
use Clue\React\Redis\Factory as RedisFactory;
use React\Socket\ConnectionInterface;
use React\Socket\TcpConnector;
use React\Socket\Connector;
use Illuminate\Support\Facades\Redis;

class StartProtobuffTCPClient extends Command implements GlobalConstants
{
    /** @var string $host */
    protected $host;

    /** @var string $port */
    protected $port;

    /** @var string|null $name */
    protected $name;

    /** @var \React\EventLoop\LoopInterface  */
    protected $loop;

    /** @var string $text */
    protected $text;

    /** @var Connector $connector */
    protected $connector;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:start 
    {name? : Name of TCP Client (option).}
     {--host='.self::HOST.' : Host for connection.} 
    {--port='.self::PORT.' : Port № for connection.}
    {--text= : Text for message.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Started TCP Client for protocol buffer translation.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        parent::__construct();
//        $this->loop = Factory::create();
//    }
//
//    /**
//     * Execute the console command.
//     *
//     * @return mixed
//     */
//    public function handle()
//    {
//        $this->name = empty($this->arguments())?'TCP client':$this->argument('name');
//        $this->port = $this->option('port');
//        $this->host = $this->option('host');
//        $this->text = $this->option('text');
//        $text = $this->text;
//            $this->connector = new Connector($this->loop, array(
////            'dns' => false,
////            'timeout' => false,
//                'tcp' => array(
//                    'bindto' => '127.0.0.1:7001'
//                )
//            ));
//            $host = $this->host === self::HOST ? '127.0.0.1:'.$this->port :
//                self::SCHEMA . '://' . $this->host.':'.$this->port;
//            $this->line($host);
//            $this->connector->connect($host)->then(function (ConnectionInterface $connection) use ($host, $text) {
//                $connection->on('data', function ($data) {
//                    $this->line($data . PHP_EOL);
//                });
//                $connection->on('close', function () {
//                    $this->error('[CLOSED]' . PHP_EOL);
//                });
//                $connection->write($text . PHP_EOL);
//            });
//            $this->loop->run();
//    }


    protected $factory;
    /**
     * StartProtobuffTCPClient constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->loop = Factory::create();
    }

//    /**
//     * Execute the console command.
//     *
//     * @return mixed
//     */
//    public function handle()
//    {
//        $this->name = empty($this->arguments())?'TCP client':$this->argument('name');
//        $this->port = $this->option('port');
//        $this->host = $this->option('host');
//        $this->text = $this->option('text');
//        $text = $this->text;
//
//        $this->connector = new Connector($this->loop, array(
////            'dns' => false,
////            'timeout' => false,
//            'tcp' => array(
//                'bindto' => '127.0.0.1:7001'
//            )
//        ));
//        $loop = $this->loop;
//        $this->factory = new RedisFactory($this->loop);
//
//        $host = $this->host === self::HOST ? '127.0.0.1:'.$this->port :
//            self::SCHEMA . '://' . $this->host.':'.$this->port;
//        $this->line($host);
//
//        $this->factory->createClient('redis://redis:6379')->then(function (Client $client) use ($loop, $host) {
////            $client->set('greeting', 'Hello world');
////            $client->append('greeting', base64_encode(123));
//
//            $client->get('greeting')->then(function ($greeting) use ($host) {
////                // Hello world!
////                echo $message . PHP_EOL;
//
//                $this->connector->connect($host)->then(function (ConnectionInterface $connection) use ($host, $greeting) {
//                    $connection->on('close', function () {
//                        $this->error('[CLOSED]' . PHP_EOL);
//                    });
//                    $connection->write($greeting . PHP_EOL);
//                    $connection->end();
//                });
//
//                $this->info($greeting);
//
//            });
//
//            $client->incr('invocation')->then(function ($n) {
//                echo 'This is invocation #' . $n . PHP_EOL;
//            });
//
//            // end connection once all pending requests have been resolved
//            //$client->end();
//        }, function (Exception $e) {
//            $this->line($e->getMessage() . '');
//        });
//
//        $this->loop->run();
//    }







//  "alex60217101990/protobuf": "@dev",




    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->name = empty($this->arguments())?'TCP client':$this->argument('name');
        $this->port = $this->option('port');
        $this->host = $this->option('host');
        $this->text = $this->option('text');
        $text = $this->text;
            $this->connector = new Connector($this->loop, array(
            'dns' => false,
            'timeout' => false,
//                'tcp' => array(
//                    'bindto' => '127.0.0.1:7001'
//                )
            ));
        $host = $this->host === self::HOST ? /*'127.0.0.1: 172.21.0.5'*/'0.0.0.0:'.$this->port :
            self::SCHEMA . '://' . $this->host.':'.$this->port;
            $this->line($host);
            $this->connector->connect($host)->then(function (ConnectionInterface $connection) use ($host, $text) {
                $connection->on('data', function ($data) {
                    $this->line($data . PHP_EOL);
                });
                $connection->on('close', function () {
                    $this->error('[CLOSED]' . PHP_EOL);
                });
           //     $connection->write($text . PHP_EOL);

                Redis::psubscribe(['*'], function ($message, $channel) use ($connection) {
                    if($channel=='message-channel'){
                        $connection->write($message . PHP_EOL);
                        $this->line($message. PHP_EOL);
                    }
                });
            });

            $this->loop->run();
    }










}
