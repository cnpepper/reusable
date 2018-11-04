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
// Auth::routers方法不是用来验证的是用来批量添加路由的
Auth::routes(); 

// 自定义的路由
Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware'=>['auth']],function(){
    Route::get('/home', 'HomeController@index')->name('home');
});
