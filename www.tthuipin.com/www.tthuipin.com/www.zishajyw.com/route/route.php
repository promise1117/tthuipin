<?php

//use think\facade\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');


Route::any('mid','index/index/mid')->middleware('Check');

// 紫砂壶手机端路由
Route::any('index','index/Index/index');
Route::any('category/[:cate]/[:id]/[:type]','index/Index/category');
Route::any('goodsinfo','index/Index/product');
Route::any('collection','index/Index/collection');
Route::any('collectionApi','index/Index/collectionApi');
Route::any('classification','index/Index/classification');
Route::any('artist','index/Index/artist');
Route::any('artist_list','index/Index/artistList');
Route::any('artist_detail','index/Index/artistDetail');
Route::any('tealeaf','index/Index/tealeaf');
Route::any('iron_kettle','index/Index/ironKettle');
Route::any('silver_kettle','index/Index/silverKettle');
// 周邊
Route::any('ambitus','index/Index/ambitus');
Route::any('chinaware','index/Index/chinaware');
// 首页壶型
Route::any('pot_shape','index/Index/potShape');
Route::any('slurry','index/Index/slurry');

Route::any('search','index/Index/search');

return [

];
