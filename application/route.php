<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
//微信公众平台

Route::get('wx/:version/verification', 'wx/:version.Verification/checkSignature');
Route::get('wx/:version/message', 'wx/:version.Message/receiveMsg');
//微信小程序
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

Route::get('api/:version/theme','api/:version.Theme/getSimpleList'); //如果没写变量 则用？ids= 这种方式来传入参数 去config中开启完全匹配
Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');

//Route::get('api/:version/product/recent','api/:version.Product/getRecent');
//Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategory');
//第四个参数为变量规则
//Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);

Route::group('api/:version/product',function(){
    Route::get('/by_category','api/:version.Product/getAllInCategory');
    Route::get('/recent','api/:version.Product/getRecent');
    Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
});
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');
Route::post('api/:version/token/user','api/:version.Token/getToken');