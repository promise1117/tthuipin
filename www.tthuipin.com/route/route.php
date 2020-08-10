<?php
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

Route::get('redis','index/Index/redis');

Route::get('msg',function(){
//    $res = sendAliZjjAuthCode(8860973156737);
//    dump($res);
});


//Route::get('/', function () {
//    $data = [
//        'code'    => '0000',
//        'message' => '无效的访问'
//    ];
//    return $data;
//});

Route::get('hello/:name', 'index/hello');

//首页
Route::any('index', 'index/Index/index');  //首页
Route::any('indexApi', 'index/Index/indexApi');  //首页
Route::any('get_category_list_api','index/Index/getCategoryListApi'); // 瀑布流接口
Route::any('cattwo', 'index/Index/getTwoCateList');  //二级分类
Route::any('catlevel', 'index/Index/getParentImage');  //一级头像
Route::any('goodsinfo/[:goodsid]', 'index/Index/getGoodsInfo');  //产品详情


Route::any('hot','index/Index/hot');
Route::any('hotApi','index/Index/hotApi');
Route::any('new','index/Index/new');
Route::any('newApi','index/Index/newApi');
Route::any('special','index/Index/special');
Route::any('specialApi','index/Index/specialApi');

Route::any('activity','index/Index/activity');
Route::any('sub_activity','index/Index/sub_activity');

// 手機端搜索
Route::any('search','index/Index/search');
Route::any('searchApi','index/Index/searchApi');

// 手机端底部service
Route::rule('about_us','index/Service/aboutUs');
Route::rule('conditions','index/Service/conditions');
Route::rule('contact_us','index/Service/contactUs');
Route::rule('delivery','index/Service/delivery');
Route::rule('distribution','index/Service/distribution');
Route::rule('exchange','index/Service/exchange');
Route::rule('payment','index/Service/payment');
Route::rule('privacy','index/Service/privacy');
Route::rule('enquire','index/Service/enquire');

Route::any('goodslist', 'index/Index/getGoodsList');  //产品列表
Route::any('visitgoods', 'index/Index/getVisitGoodsList');  //最近浏览产品列表
Route::any('shortcut', 'index/Index/shortCut');  //shortcut
Route::any('desktop', 'index/Index/desktop');  //shortcut


//分类
Route::any('categorylist', 'index/Index/getCategoryList');  //分类列表

//购物车
Route::any('shopcar', 'index/Shopcar/getShopcarList');  //购物车列表

//订单
Route::any('sure/[:type]', 'index/Order/sureOrder');  //确定下单-确认资料
Route::post('del','index/Order/del');
Route::any('address','index/Order/sureAddress');
Route::any('money','index/Order/sureMoney');
Route::any('moneyApi','index/Order/sureMoneyApi');
Route::any('over', 'index/Order/overOrder');  //下单完成

//我的
Route::any('myself','index/Myself/index');
Route::any('my','index/Myself/my');
Route::any('my_order','index/Myself/myOrder');
Route::any('service','index/Myself/service');
Route::any('privacy','index/Myself/privacy');
Route::any('question','index/Myself/question');


//pc
Route::any('pc_index','pc/Index/index');
Route::any('pc_index_api','pc/Index/indexApi');
Route::any('pc_goodsinfo','pc/Index/pcGetGoodsInfo');
Route::any('pc_goodsinfo/[:goodsid]', 'pc/Index/getGoodsInfo');  //产品详情
Route::any('pc_categorylist','pc/Index/pcGetCategoryList');
Route::any('pc_hot','pc/Index/pcHot');
Route::any('pc_new','pc/Index/pcNew');
Route::any('pc_special','pc/Index/pcSpecial');
Route::any('pc_sure/[:type]','pc/Order/pcSureOrder');
Route::any('pc_del','pc/Order/pcDel');
Route::any('pc_address','pc/Order/pcSureAddress');
Route::any('pc_money','pc/Order/pcSureMoney');
Route::any('pc_over','pc/Order/pcOverOrder');

Route::any('pc_home','pc/Index/home');

Route::any('pc_home_page','pc/Index/home_page');




// pc搜索
Route::any('pc_search','pc/Index/pcSearch');

// pc底部service
Route::rule('pc_service','pc/Service/index');
Route::rule('pc_enquire','pc/Service/enquire');

// 紫砂壶

Route::rule('pot','pc/Pot/index');
Route::rule('clay_pot','pc/Pot/yixinClayPot');
Route::rule('tea_pot','pc/Pot/teaPot');
Route::any('about','pc/Pot/about_us');

// line号添加

Route::rule('line','pc/Line/index');
Route::rule('clay_line','pc/Line/yixinClayLine');
Route::rule('tea_line','pc/Line/teaLine');
Route::rule('line_add','pc/Line/add');


// 折价码接口
Route::any('get_discount_code','index/discount/getDiscountCode');
return [

];
