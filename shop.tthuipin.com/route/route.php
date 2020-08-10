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

Route::get('/', function () {
    $data = [
        'code'    => '0000',
        'message' => '无效的访问'
    ];

    exit(json_encode($data));
});

Route::get('hello/:name', 'index/hello');
Route::get('orderApi', function(){
    $order_list = Db::name('order')->limit(100)->select();
    dump($order_list);
});

//公共接口
Route::any('uploadimage', 'admin/UploadImage/uploadImage');  //图片上传
Route::any('deleteimage', 'admin/UploadImage/deleteImage');  //图片删除


//后台接口
//ecs_admin_user
Route::any('list', 'admin/User/getList');    //用户列表
Route::any('login', 'admin/User/login');    //用户登录
Route::any('addedit', 'admin/User/addEdit');    //用户添加修改
Route::any('allow', 'admin/User/allowLogin');    //用户禁止登录
Route::any('delete', 'admin/User/delete');    //用户删除
Route::any('myself', 'admin/User/getMyself');    //获取个人信息

//ecs_category
Route::any('categorylist', 'admin/Category/getListOne');    //获取一级分类
Route::any('categorylistn', 'admin/Category/getListMore');    //获取N级分类
Route::any('categoryaddedit', 'admin/Category/addEdit');    //分类添加修改
Route::any('categorydelete', 'admin/Category/deleteCategory');    //分类删除
Route::any('categoryshow', 'admin/Category/showCategory');    //分类恢复

//ecs_brand
Route::any('brandlist', 'admin/Brand/getList');    //品牌列表
Route::any('brandaddedit', 'admin/Brand/addEdit');    //品牌添加修改
Route::any('branddelete', 'admin/Brand/deleteBrand');    //品牌删除
Route::any('brandshow', 'admin/Brand/showBrand');    //品牌恢复

//ecs_goods
Route::any('goodslist', 'admin/Goods/getList');    //商品列表
Route::any('goodsaddedit', 'admin/Goods/addEdit');    //商品添加和修改
Route::any('goodsdelete', 'admin/Goods/deleteGoods');    //商品删除

//ecs_order
Route::any('orderlist', 'admin/Order/getList');    //订单列表
Route::any('orderaddedit', 'admin/Order/addEdit');    //订单添加和修改
Route::any('orderdelete', 'admin/Order/deleteOrder');    //订单删除

//ecs_comments
Route::any('commentslist', 'admin/Comments/getList');    //评论列表
Route::any('commentsaddedit', 'admin/Comments/addEdit');    //评论添加和修改
Route::any('commentsdelete', 'admin/Comments/deleteComments');    //评论状态修改

//ecs_banner
Route::any('bannerlist', 'admin/Banner/getList');    //banner列表
Route::any('banneraddedit', 'admin/Banner/addEdit');    //banner添加与修改
Route::any('bannerdelete', 'admin/Banner/deleteBanner');    //banner删除


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//前台接口
//ecs_banner
Route::any('shopbanner', 'index/Banner/getList');    //banner列表

//ecs_goods
Route::any('shopgoodsindex', 'index/Goods/getIndexList');    //首页列表
Route::any('shopgoods', 'index/Goods/getList');    //内页列表
Route::any('shopgoodsinfo', 'index/Goods/getInfo');    //商品详情
Route::any('shopgoodslook', 'index/Goods/addLook');    //商品浏览量

//ecs_comments
Route::any('shopcomments', 'index/Comments/getList');   //商品详情获取评论

//ecs_category
Route::any('shopcategory', 'index/Category/getList');    //获取一级分类
Route::any('shopcategorymore', 'index/Category/getListMore');    //获取N级分类

//ecs_order
Route::any('shoporder', 'index/Order/addOrder');    //提交订单


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//视图后台
Route::any('vupload', 'backend/User/upload');    //上传图片
Route::any('vuploads', 'backend/User/uploads');    //上传图片多
Route::any('vdeleteimg', 'backend/User/delImg');    //上传图片删除
Route::any('vimgsize', 'backend/User/getImgSise');    //计算图片尺寸

//首页
Route::any('vindex', 'backend/Index/index');    //后台首页
Route::any('vwelcome', 'backend/Index/welcome');    //后台首页欢迎
Route::any('vmyself', 'backend/Index/getmyself');    //获取个人信息
Route::any('vout', 'backend/User/outlogin');    //退出登录
Route::any('veditmyself', 'backend/Index/useredit');    //用户修改

//ecs_admin_user
Route::any('vuserlist', 'backend/User/getUserList');    //用户列表
Route::any('vlogin', 'backend/User/login');    //用户登录
Route::any('vuseraddedit', 'backend/User/userAddEdit');    //用户添加
Route::any('vuseredit', 'backend/User/userEdit');    //用户修改
Route::any('vallow', 'backend/User/allowLogin');    //用户禁止登录
Route::any('vdelete', 'backend/User/deleteUser');    //用户删除
Route::any('vuserloginlog', 'backend/User/getUserLoginLogList');    //用户登录

//ecs_admin_role
Route::any('vrolelist', 'backend/Role/getRoleList');    //角色列表
Route::any('vroleadd', 'backend/Role/roleAdd');    //角色添加
Route::any('vroleedit', 'backend/Role/roleEdit');    //角色修改
Route::any('vroledelete', 'backend/Role/deleteRole');    //角色删除

//ecs_admin_right
Route::any('vrightlist', 'backend/Right/getRightList');    //权限列表
Route::any('vrightadd', 'backend/Right/rightAdd');    //权限添加
Route::any('vrightedit', 'backend/Right/rightEdit');    //权限修改
Route::any('vrightdelete', 'backend/Right/deleteRight');    //权限删除

//ecs_category
Route::any('vcategorylist', 'backend/Category/getList');    //分类列表
Route::any('vcategoryedit', 'backend/Category/edit');    //分类修改
Route::any('vcategoryadd', 'backend/Category/add');    //分类添加
Route::any('vcategorydelete', 'backend/Category/deleteCategory');    //分类删除
Route::any('vcategoryshow', 'backend/Category/show');    //分类删除

//ecs_banner
Route::any('vbannerlist', 'backend/Banner/getList');    //banner列表
Route::any('vbanneradd', 'backend/Banner/add');    //banner添加
Route::any('vbanneredit', 'backend/Banner/edit');    //banner修改
Route::any('vbannershow', 'backend/Banner/show');    //banner显示
Route::any('vbannerdelete', 'backend/Banner/deleteBanner');    //banner删除

//ecs_goods
Route::any('vgoodslist', 'backend/Goods/getList');    //商品列表
Route::any('vgoodsadd', 'backend/Goods/add');    //商品添加
Route::any('vgoodsedit', 'backend/Goods/edit');    //商品修改
Route::any('vgoodscopy', 'backend/Goods/copy');    //商品复制
Route::any('vgoodsdelete', 'backend/Goods/deleteGoods');    //商品删除
Route::any('vgoodsdels', 'backend/Goods/delGoodsMany');    //商品多选删除
Route::any('vgoodsshow', 'backend/Goods/show');    //商品下架
Route::any('vgoodsrecycle', 'backend/Goods/goodsrecycle');    //商品回收站
Route::any('vgoodsdel', 'backend/Goods/goodsdel');    //商品删除列表
Route::any('vgoodsdelchange', 'backend/Goods/goodsdelchange');    //商品删除列表
Route::any('vdiscountcode', 'backend/Discount/getList');    //折价码
Route::any('vcodeadd', 'backend/Discount/add');    //折价码添加
Route::any('vcodedelete', 'backend/Discount/deleteCode');    //折价码添加

//推广单页面
Route::any('vgoodspage', 'backend/Goodspage/getList');    //推广单页面
Route::any('vgoodsMypage', 'backend/GoodsMypage/getList');    //推广个人单页面
Route::any('vgoodspageadd', 'backend/Goodspage/add');    //推广添加
Route::any('vgoodspageedit', 'backend/Goodspage/edit');    //推广修改
Route::any('vgoodspagedelete', 'backend/Goodspage/deleteGoods');    //推广删除
Route::any('vgoodspagedels', 'backend/Goodspage/delGoodsMany');    //推广多选删除
Route::any('vgoodspageshow', 'backend/Goodspage/show');    //推广下架
Route::any('vgoodspagerecycle', 'backend/Goodspage/goodsrecycle');    //推广回收站

//ecs_goodsinfo
Route::any('vgoodsilist', 'backend/Goodsinfo/getList');    //商品详情列表
Route::any('vgoodsiadd', 'backend/Goodsinfo/add');    //商品详情添加
Route::any('vgoodsiedit', 'backend/Goodsinfo/edit');    //商品详情修改
Route::any('vgoodsidel', 'backend/Goodsinfo/deletGoods');    //商品详情删除
Route::any('vgoodsidels', 'backend/Goodsinfo/delGoodsMany');    //商品详情多选删除
//Route::any('vgoodsishow', 'backend/Goodsinfo/show');    //商品详情下架
Route::any('vgoodsiaddt', 'backend/Goodsinfo/addtc');    //商品详情套餐添加
Route::any('vgoodsieditt', 'backend/Goodsinfo/edittc');    //商品详情套餐修改
Route::any('vgoodsitlist', 'backend/Goodsinfo/getListtc');    //商品详情套餐列表
Route::any('vgoodsiaddtdel', 'backend/Goodsinfo/gitcDelete');    //商品详情添加套餐删除

//ecs_comments
Route::any('vcommentslist', 'backend/Comments/getList');    //评论列表
Route::any('vcommentsadd', 'backend/Comments/add');    //评论添加
Route::any('vcommentsedit', 'backend/Comments/edit');    //评论修改
Route::any('vcommentsdelete', 'backend/Comments/deleteComments');    //评论删除
Route::any('vcomdelmany', 'backend/Comments/delComMany');    //评论多条删除

//ecs_order
Route::any('vorderlist', 'backend/Order/getList');    //订单列表
Route::any('vorderedit', 'backend/Order/edit');    //订单修改
Route::any('vordereditall', 'backend/Order/editAll');    //订单批量修改
Route::any('vorderdelete', 'backend/Order/deleteOrder');    //订单作废
Route::any('vorderinfo', 'backend/Order/getOrderInfo');    //订单详情
Route::any('vorderrecycle', 'backend/Order/orderRecycle');    //订单回收站

//统计类Count
Route::any('vgoodscount', 'backend/Count/getCount');    //商品上传统计
Route::any('vgoodsbuy', 'backend/Count/getBuyGoodsList');    //商品购买统计
Route::any('vgoodsvisit', 'backend/Count/getVisitGoodsList');    //商品浏览统计
Route::any('vgoodssales', 'backend/Count/daySales');    //日销量
Route::any('vgoodsamount', 'backend/Count/amount');    //日销量

//数据库备份
Route::any('vmysqllist', 'backend/Mysqlback/getMysqlList');    //数据库列表
Route::any('vmysqlbackup', 'backend/Mysqlback/backup');    //数据库备份全部
Route::any('vmysqlbackuponly', 'backend/Mysqlback/backuponly');    //数据库备份单个
Route::any('vmysqlrepair', 'backend/Mysqlback/repairTable');    //修复表
Route::any('vmysqlbacklist', 'backend/Mysqlback/backFilesList');    //备份文件列表
Route::any('vmysqldelback', 'backend/Mysqlback/delBackup');    //备份文件列表
Route::any('vmysqlreduction', 'backend/Mysqlback/reduction');    //恢复表




//定时任务 删除空套餐
Route::any('delnulltc', 'backend/Timingtask/deleteGoodsinfoNullTaocan');    //删除空套餐




// promise_route

Route::rule('pc_banner','backend/Banner/pcBanner');
Route::rule('pc_banner_list','backend/Banner/pcBannerList');

// 紫砂壺
Route::rule('pot','backend/Pot/pot');
Route::rule('pot_list','backend/Pot/potList');
Route::rule('pot_link','backend/Pot/potLink');


// line账号填写
Route::rule('vline','backend/Line/line');
Route::rule('line_list','backend/Line/lineList');
Route::rule('line_link','backend/Line/lineLink');



//ecs_pot_product
Route::any('pproductlist', 'backend/Product/getList');    //商品列表
Route::any('pproductadd', 'backend/Product/add');    //商品添加
Route::any('pproductedit', 'backend/Product/edit');    //商品修改
Route::any('pproductdelete', 'backend/Product/deleteGoods');    //商品删除
Route::any('pproductdels', 'backend/Product/delGoodsMany');    //商品多选删除
Route::any('pproductshow', 'backend/Product/show');    //商品下架


//ecs_pot_product
Route::any('porderlist', 'backend/Potorder/getList');    //紫砂壶订单列表
Route::any('porderadd', 'backend/Potorder/add');    //紫砂壶订单列表

Route::any('porderedit', 'backend/Potorder/edit');    //订单修改
Route::any('porderdelete', 'backend/Potorder/deleteOrder');    //订单作废
Route::any('porderinfo', 'backend/Potorder/getOrderInfo');    //订单详情


//ecs_goodsinfo
Route::any('pproductilist', 'backend/Productinfo/getList');    //商品详情列表
Route::any('pproductiadd', 'backend/Productinfo/add');    //商品详情添加
Route::any('pproductiedit', 'backend/Productinfo/edit');    //商品详情修改
Route::any('pproductidel', 'backend/Productinfo/deletGoods');    //商品详情删除
Route::any('pproductidels', 'backend/Productinfo/delGoodsMany');    //商品详情多选删除
//Route::any('vgoodsishow', 'backend/Goodsinfo/show');    //商品详情下架
Route::any('pproductiaddt', 'backend/Productinfo/addtc');    //商品详情套餐添加
Route::any('pproductieditt', 'backend/Productinfo/edittc');    //商品详情套餐修改
Route::any('pproductitlist', 'backend/Productinfo/getListtc');    //商品详情套餐列表
Route::any('pproductiaddtdel', 'backend/Productinfo/gitcDelete');    //商品详情添加套餐删除

//ecs_category
Route::any('pcategorylist', 'backend/Pcategory/getList');    //分类列表
Route::any('pcategoryedit', 'backend/Pcategory/edit');    //分类修改
Route::any('pcategoryadd', 'backend/Pcategory/add');    //分类添加
Route::any('pcategorydelete', 'backend/Pcategory/deleteCategory');    //分类删除
Route::any('pcategoryshow', 'backend/Pcategory/show');    //分类删除


//ecs_pot_artist
Route::any('partistlist', 'backend/Artist/getList');    //商品列表
Route::any('partistadd', 'backend/Artist/add');    //商品添加
Route::any('partistedit', 'backend/Artist/edit');    //商品修改
Route::any('partistdelete', 'backend/Artist/deleteGoods');    //商品删除
Route::any('partistdels', 'backend/Artist/delGoodsMany');    //商品多选删除
Route::any('partistshow', 'backend/Artist/show');    //商品下架
return [

];
