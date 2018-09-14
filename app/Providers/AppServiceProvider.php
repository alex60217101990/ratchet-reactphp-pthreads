<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Laravel\Horizon\Horizon;
use Proto\Data\User;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

class AppServiceProvider extends ServiceProvider
{
    /** @var LoopInterface $loop */
    protected $loop;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Horizon::auth(function ($request) {
            // Always show admin if local development
            if (env('APP_ENV') == 'local') {
                return true;
            }
        });

        $this->loop = Factory::create();
        $this->app->instance('eventLoop', $this->loop);
//        $this->app->instance('loop', function (): LoopInterface{
//            $this->loop = Factory::create();
//            return $this->loop;
//        });
//
//        $this->app->when('App\Listeners\ProtobufTCPSubscribe')
//            ->needs('getLoop')
//            ->give($this->loop);
//
//        $this->app->make('loop');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
