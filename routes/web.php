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

//Route::get('/', function () {
//    return view('welcome');
//});

//Route::get('/info',function () {
//    phpinfo();
//});
//
//
//Route::get('/','Index\IndexController@index');            //微商城首页
//Route::get('/goods/detail','Goods\IndexController@detail');      //商品详情页
//
//
////微信开发
//Route::get('/wx','WeiXin\WxController@wx');
//Route::get('/wx/login','WeiXin\WxController@login');
//
//Route::post('/wx','WeiXin\WxController@receiv');
//Route::get('/wx/media','WeiXin\WxController@getMedia');
//Route::get('/wx/menu','WeiXin\WxController@createMenu');
//Route::get('/wx/access_token','WeiXin\WxController@access_token');
//Route::get('/wx/qrcode','WeiXin\WxQRController@qrcode');
//
////微信公众号
//Route::get('/vote','VoteController@index');     //微信投票
//
//Route::get('/wx/sendmsg','Crontab\WxController@sendMsg');     //微信群发

Route::get('/wx','WeiXin\WxController@wx');
Route::post('/wx','WeiXin\WxController@wxreceiv');
Route::get('/wx/menu','WeiXin\WxController@menu');
Route::get('/wx/guanli','WeiXin\WxController@guanli');
Route::get('/wx/access_token','WeiXin\WxController@access_token');





















