<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// API路由
$api = app('Dingo\Api\Routing\Router');
$api->version('v1',function($api){
    $api->get('dingo',function(){
        return 'hello! dingo!';
    });
    //$api->get('user',"App\Http\Controllers\UserController@index");//必须完整命名空间
});
