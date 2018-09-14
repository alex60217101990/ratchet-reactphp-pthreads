<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\CustomClasses\ProtocClassesGenerator;
use App\Notifications\InvoicePaid;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use \App\Events\ProtobufTCPEvent;

Route::get('/', function () {
    Redis::set('test','some text');
    return view('welcome');
});

Route::middleware(['cors', 'doNotCacheResponse'])->group(function () {

    Route::post('/test/notification', function(Request $request){
        if ($request->has(['text', 'email'])) {
            Notification::route('mail', 'taylor@example.com')
                ->notify(new InvoicePaid($request->only(['text'])));
          event(new ProtobufTCPEvent(
              [
                  $request->get('name'),
                  $request->get('email'),
                  $request->get('password')
              ]
          ));


            $class_name = (new ProtocClassesGenerator())->getCorrectClassName('Proto\\Data\\UserRegisterRequest');
            $class = new $class_name();
            new Proto\Data\User;
            $class_name_param = (new ProtocClassesGenerator())->callNeededMethod(
                'Proto\Data\User',
                'setName',
                ['Petya']
            );

            $object = (new ProtocClassesGenerator())->callNeededMethod(
                    $class_name,
                    'setUser',
                    [$class_name_param]
                );

            $user = $object->getUser();

//            $name = (new ProtocClassesGenerator())->callNeededMethod(
//                (new ProtocClassesGenerator())->getCorrectClassName($user),
//                'getName'
//            );

        }
        return response()->json(['success'=>Redis::get('test'), 'name' => $user->getName()]);
    });

    Route::post('/test/event', function(Request $request){
        $data = event(new \App\Events\Event($request->only(['data'])['data']) ?? null);

        event(new App\Events\MessagePusher($request->get('id'), $request->get('msg')));

        return response()->json([
            'data' => $data,
            'test'=> $request->only(['data'])['data'] ?? 'absent'
        ]);
    });

});

// add this to web routes
Route::get('/websocket/open', 'WebSocketController@onOpen');
Route::get('/websocket/message', 'WebSocketController@onMessage');
Route::get('/websocket/close', 'WebSocketController@onClose');
Route::get('/websocket/error', 'WebSocketController@onError');
