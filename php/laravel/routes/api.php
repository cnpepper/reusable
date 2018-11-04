<?php

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

$api = app('Dingo\Api\Routing\Router');

// dingo可以控制版本 但是发现好像加不了前缀
$api->version('v1', function ($api) {
    /**
     * 不需要验证的api
     */
    $api->group(['middleware' => ['cors']], function ($api) {
        /**
         * User 用户接口
         */
        $api->group(['prefix'=>'user'],function($api){
            //$api->post('user_register', 'App\Http\Controllers\Api\LoginController@register'); 暂时没实现
            $api->post('login', 'App\Http\Controllers\Api\User\LoginController@index'); 
        });
    });

    /**
     * 需要验证的api
     */
    $api->group(['middleware' => ['auth:api', 'cors']], function ($api) {
        /**
         * test 测试接口
         */
        $api->group(['prefix'=>'test'],function($api){
            $api->post('query','App\Http\Controllers\Api\Test\QueryController@index');
        });
    });
});


