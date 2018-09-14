<?php

namespace App\Console\Commands;

use App\CustomClasses\Interfaces\GlobalConstants;
use App\CustomClasses\ProtocClassesGenerator;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

use InvalidArgumentException;
use Proto\Data\UserRegisterRequest;
use Proto\Data\UserRequest;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\Socket\ConnectionInterface;
use React\Socket\LimitingServer;
use React\Socket\TcpServer;

class StartProtobufTCPServer extends Command implements GlobalConstants
{
    /** @var string $host */
    protected $host;

    /** @var string $port */
    protected $port;

    /** @var string|null $name */
    protected $name;

    /** @var \React\EventLoop\LoopInterface  */
    protected $loop;

    /** @var ProtocClassesGenerator $generator */
    protected $generator;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:start 
    {name? : name of server (option).} 
    {--host='.self::HOST.' : Host for connection.} 
    {--port='.self::PORT.' : Port № for connection.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Started TCP Server for protocol buffer translation.';

    /**
     * Create a new command instance.
     *
     * @param ProtocClassesGenerator $classesGenerator
     */
    public function __construct(ProtocClassesGenerator $classesGenerator)
    {
        parent::__construct();
        $this->loop = Factory::create();
        $this->generator = $classesGenerator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->name = empty($this->arguments())?'TCP server':$this->argument('name');
        $this->port = $this->option('port');
        $this->host = $this->option('host');
        Redis::set('server_name', $this->name);
        Redis::set('server_host', $this->host);
        Redis::set('server_port', $this->port);

        $connection_data = (object)[
            'port' => '7000',
            'server' => self::SCHEMA.'://0.0.0.0',
            'processed' => 0
        ];

        /*server logic*/
//        $server = new TcpServer($this->host === self::HOST ?
//            self::SCHEMA . '://' . '172.21.0.5:' . $this->port :
//            self::SCHEMA . '://' . '172.21.0.5'/*$_SERVER['SERVER_ADDR']$this->host*/ . ':7000', $this->loop, array(
//            'bindto' => '0:7000',
//            'so_reuseport' => true,
//            'backlog' => 200
//        ));

        $server = new TcpServer($connection_data->server.':'.$connection_data->port, $this->loop,
            array(
            'so_reuseport' => true,
            'backlog' => 200
        ));

        $server = new LimitingServer($server, null);

        $server->on('connection', function (ConnectionInterface $client) use ($server, $connection_data) {
            // whenever a new message comes in
            $client->on('data', function ($data) use ($client, $server) {
                // remove any non-word characters (just for the demo)
      //          $data = trim(preg_replace('/[^\w\d \.\,\-\!\?]/u', '', $data));
                // ignore empty messages
                if ($data === '') {
                    return;
                }
                /*----------------------------------------------------------------------------------------------------*/
//                if(substr($data, 0,3) === 'add'){
//                    $this->info('METHOD: ADD ' . PHP_EOL);
//                    $request = new UserRegisterRequest();
//                    $request->mergeFromString(substr($data, 3));
//                    $response = (new AuthController())->add($request);
//                    $client->write($response->serializeToString());
//                }
//                if(substr($data, 0,3) === 'get'){
//                    $this->info('METHOD: GET ' . PHP_EOL);
//                    $request = new UserRequest();
//                    $request->mergeFromString(substr($data, 3));
//                    $response = (new AuthController())->get($request);
//                    $client->write($response->serializeToString());
//                }
                $matches = [];
                $data = preg_match_all(
                    '/{"class":"(.*)","method":"(.*)","request":"(.*)"}/',
                    $data,
                    $matches
                );
                $merged_data = call_user_func_array('array_merge', $matches);
                $current_data = json_decode($data);
                $this->line(var_dump($merged_data));
                $class_name = $this->generator->getCorrectClassName($merged_data[1]);
                $method = $merged_data[2];
                $params = $merged_data[3];
                $class = new $class_name();

                $this->line($class);
//                $object = $this->generator->callNeededMethod(
//                    $class_name,
//                    $method/*'mergeFromString'*/,
//                    $params
//                );
//                $this->line('Test_1: '.$object->getName());
 //               $response = (new AuthController())->add($class);
                /*----------------------------------------------------------------------------------------------------*/

                $this->info('-> ' . $data .'    '. PHP_EOL);
                // prefix with client IP and broadcast to all connected clients
//                $data = trim(parse_url($client->getRemoteAddress(), PHP_URL_HOST), '[]') . ': ' . $data . PHP_EOL;
//                foreach ($server->getConnections() as $connection) {
//                    $connection->write($data);
//                }
            });

            $client->on('end', function () {

                $this->info('Ended event.'.PHP_EOL);
            });

            $client->on('error', function (Exception $e) {

                $this->info('Error event ('.$e->getMessage().')'.PHP_EOL);
            });

            $client->on('close', function () {

                $this->info('Close event.');
            });
            echo('new connection.');
            $connection_data->processed++;
        });
        $server->on('error', function (Exception $exception){
            $this->error('Error: '.$exception->getMessage().PHP_EOL);
        });
        $server->on('close', function (){
            $this->error('Server was stop.');
        });
        $this->info('Listening on ' . $server->getAddress() . PHP_EOL);


        $fork = function (callable $child) {
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
        };

        $processes = [];

        $loop = $this->loop;
        for ($i = 1; $i < 10; $i ++) {
            $server->pause();
            $processes[] = $fork(function () use ($server, $loop, $connection_data) {
                $server->resume();
                $loop->addSignal(SIGINT, function () use ($connection_data, $loop) {
                    fwrite(STDERR, sprintf('%s finished running, Processed %d requests' . PHP_EOL, posix_getpid(),
                        $connection_data->processed));
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

        $this->loop->run();
    }
}
