<?php

namespace app\index\controller;

use app\index\controller\Base;

use think\Db;

class Index extends Base

{

    public function __construct()

    {

     
        parent::__construct();


//        $this->category  = Db('Category'); //分类表

//        $this->goods     = Db('Goods'); //商品表

//        $this->comments  = Db('Comments'); //评论表

//        $this->goodsinfo = Db('Goodsinfo'); //商品详情表



        //获取header頂部分类名

        $this->catList = $this->getCategoryParentList();

        $this->hot = Db::name('category')->where('parent_id','neq',0)->where('hot','1')->where('is_show','1')->order('sort_order','desc')->select();
        $this->style = Db::name('category')->where('parent_id','neq',0)->where('style','1')->where('is_show','1')->order('sort_order','desc')->select();
        $this->fashion = Db::name('category')->where('parent_id','neq',0)->where('fashion','1')->where('is_show','1')->order('sort_order','desc')->select();

        foreach($this->catList as $k=>$v){

            $cat_two = Db::name('category')->where('parent_id',$v['cat_id'])->where('is_show','1')->order('sort_order','desc')->select();

            $this->catList[$k]['cat_two'] = $cat_two;



        }


        $this->assign('catlist',$this->catList);
        $this->assign('hot',$this->hot);
        $this->assign('style',$this->style);
        $this->assign('fashion',$this->fashion);



        // 頂部購物車



        $this->shop_car = $this->carSession();

        $this->assign('shop_car',$this->shop_car);



    }



    

    /**\

     * @return mixed

     * @user promise_1117

     * @time 2019/12/11/11:19

     * @description

     */

    public function index(){



        $catList = $this->getCategoryParentList();

        foreach($catList as $k=>$v){

            $cat_two = Db::name('category')->where('parent_id',$v['cat_id'])->where('is_show','1')->order('sort_order','desc')->select();

            $catList[$k]['cat_two'] = $cat_two;

        }

        //获取所有分类

        $catListt = $this->getCategoryParentListt();

        //获取热卖 分类

        $catHotList = $this->getCategoryHotList();

        //熱賣推薦商品

        $hotwhere[] = ['hot','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];

        $hotwhere[] = ['completion','eq','1'];

        $hotsort    = ['sale'=>'desc'];

//        $hotsort    = ['visit'=>'desc'];

        $hotGoods = Db('Goods')->where($hotwhere)->order($hotsort)->limit(0,24)->select();


        //榜單商品

        $indexwhere[] = ['hot','eq','1'];

        $indexwhere[] = ['is_del','eq','0'];

        $indexwhere[] = ['completion','eq','1'];

        $indexsort = ['sale'=>'desc'];

        $indexGoods = Db('Goods')->where($indexwhere)->order($indexsort)->limit(0,24)->select();


        //新品

//        $newwhere[] = ['new','eq','1'];

        $newwhere[] = ['is_del','eq','0'];

        $newwhere[] = ['completion','eq','1'];

        $newsort = ['create_time'=>'desc'];

        $newGoods = Db('Goods')->where($newwhere)->order($newsort)->limit(0,24)->select();


        // 活动总榜单页面

        $sub_activity = Db::name('goods')->where('is_del','0')->where('index','eq',1)->order('sale','desc')->limit(0,10)->select();



        // 彩妆特卖产品 彩妆分类父id = 49 属于顶级分类

        $c_two = Db('category')->where([['parent_id','eq','49'],['is_show','eq','1']])->select();
        $empty_arr = array();
        foreach($c_two as $v){
            array_push($empty_arr,$v['cat_id']);
        }
        $str = join(',',$empty_arr);

//        $specialwhere[] = ['index','eq','1'];

        $specialwhere[] = ['is_del','eq','0'];

        $specialwhere[] = ['completion','eq','1'];

        $specialwhere[] = ['special','eq','1'];

        $specialwhere[] = ['categoryid','in',$str];

        $specialsort = ['sale'=>'desc'];

        $specialGoods = Db('Goods')->where($specialwhere)->order($specialsort)->limit(0,24)->select();

//        dump($specialGoods);


        // 洋装特卖产品 id = 32 属于二级分类


//        $specialwhere1[] = ['index','eq','1'];

        $specialwhere1[] = ['is_del','eq','0'];

//        $specialwhere1[] = ['completion','eq','1'];

        $specialwhere1[] = ['special','eq','1'];

        $specialwhere1[] = ['categoryid','eq','32'];

        $specialsort1 = ['sale'=>'desc'];

        $specialGoods1 = Db('Goods')->where($specialwhere1)->order($specialsort1)->limit(0,24)->select();

//        dump($specialGoods);


        // 家居164 宠物98,187,188,189,190,191 户外173,174,175,176,177,178,179 这些栏目的特卖

        // 創意生活 55,56,57,95,117,167,168,169,170,171,172
        // 居家生活 52,53,54,94,119,153,154,155,156,166
        $specialwhereall[] = ['is_del','eq','0'];

        $specialwhereall[] = ['completion','eq','1'];

        $specialwhereall[] = ['special','eq','1'];

//        $specialwhereall[] = ['categoryid','in','55,56,57,95,117,167,168,169,170,171,172,52,53,54,94,119,153,154,155,156,166,164,98,187,188,189,190,191,173,174,175,176,177,178,179'];

        $specialsortall = ['sale'=>'desc'];


        $specialGoodsall = Db('Goods')->where($specialwhereall)->order($specialsortall)->limit(0,24)->select();







        // not in 上面的
        $specialwhereall1[] = ['is_del','eq','0'];

        $specialwhereall1[] = ['completion','eq','1'];

        $specialwhereall1[] = ['special','eq','1'];

        $specialwhereall1[] = ['categoryid','not in','55,56,57,95,117,167,168,169,170,171,172,52,53,54,94,119,153,154,155,156,166,164,98,187,188,189,190,191,173,174,175,176,177,178,179'];

        $specialsortall1 = ['create_time'=>'desc'];


        $specialGoodsall1 = Db('Goods')->where($specialwhereall1)->order($specialsortall1)->limit(0,24)->select();


        $this->assign([

//            'banner'    => $res, //banner

            'catlist'   => $catList, //一级分类

            'catlistt'  => $catListt, //所有分类

            'hotlist'   => $catHotList, //获取热卖

            'hotgoods'  => $hotGoods, //热销商品

            'newgoods'  => $newGoods, //新品商品

            'indexgoods'  => $indexGoods, //新品商品

            'specialgoods'  => $specialGoods, //新品商品

            'specialgoods1'  => $specialGoods1, //新品商品

            'specialgoodsall'  => $specialGoodsall, //新品商品
            'specialgoodsall1'  => $specialGoodsall1, //新品商品

            'sub_activity' => $sub_activity // 活动榜单页面

        ]);



        return $this->fetch();



    }


    public function indexApi($page=""){



        $page == ''?$page = 1:$page = $page;
        $num = 24;
        $start = ($page)*24;

        //热销商品

        $hotwhere[] = ['hot','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];

        $hotwhere[] = ['completion','eq','1'];

        $hotsort    = ['sort'=>'desc','id'=>'desc'];

//        $hotsort    = ['visit'=>'desc'];

        $hotGoods = Db('Goods')->where($hotwhere)->order($hotsort)->limit($start,$num)->select();

        $countHotGoods = count(Db('Goods')->where($hotwhere)->order($hotsort)->select());




   







        //新品

//        $newwhere[] = ['new','eq','1'];

        $newwhere[] = ['is_del','eq','0'];

        $newwhere[] = ['completion','eq','1'];

        $newsort = ['create_time'=>'desc'];

        $newGoods = Db('Goods')->where($newwhere)->order($newsort)->limit($start,$num)->select();

        $countNewGoods = count(Db('Goods')->where($newwhere)->order($newsort)->select());






//        dump($hotGoods);

//        dump($newGoods);


        return [



            'hotgoods'  => $hotGoods, //热销商品

            'newgoods'  => $newGoods, //新品商品

            'page' => $page,

            'countHotGoods' => $countHotGoods,

            'countNewGoods' => $countNewGoods

        ];



    }






    //商品详情

    public function getGoodsInfo(){

        $gid = request()->param('goodsid');


        //商品浏览量+1
        Db::name('goods')->where('id',$gid)->setInc('liulan');
        //商品内容
        $goodsInfo = Db::name('goods')
            ->where('id',$gid)
//            ->where('completion','1')
            ->find();

//        dump($goodsInfo);die;




        $cid = $goodsInfo['categoryid'];
        // 好物推薦
        $hotmap[] = ['categoryid','eq',$cid];
        $hotmap[] = ['is_del','eq','0'];
        $hotmap[] = ['completion','eq','1'];
        $hotsort = ['sale'=>'desc'];
        $hotGoods = Db('Goods')->where($hotmap)->limit(20)->order($hotsort)->select();


        //猜你喜欢
        //猜你喜欢改成同类目下的产品, 而不是后台填写的id
        $likeGoods = $this->another($cid);
//        $likeid = $goodsInfo['like'];
////        $likemap[] = ['id','in',$likeid];
//        $likemap[] = ['categoryid','eq',$cid];
//        $likemap[] = ['is_del','eq','0'];
//        $likesort  = ['sort'=>'desc','id'=>'desc'];
//        $likeGoods = Db('Goods')->where($likemap)->orderRand()->limit(9)->select();

        //特卖推荐
        $specialmap[] = ['categoryid','eq',$goodsInfo['categoryid']];
        $specialmap[] = ['is_del','eq','0'];
        $specialmap[] = ['completion','eq','1'];
        $specialmap[] = ['special','eq','1'];
        $specialsort  = ['sort'=>'desc','id'=>'desc'];
        $specialGoods = Db('Goods')->where($specialmap)->order($specialsort)->limit(0,12)->select();
        //评论内容
        $commap[] = ['goods_id','eq',$gid];
        $commap[] = ['username','neq',''];
        $comsort = ['sort'=>'desc','id'=>'desc'];
        $comments = Db::name('comments')->where($commap)->order($comsort)->select();
        //商品编号详情
        $infogsp[] = ['pid','eq','0'];
        $infogsp[] = ['taocan','neq','0'];
        $infogs[] = ['pid','neq','0'];
        $infogs[] = ['goods_id','eq',$gid];
        $infosort = ['order'=>'desc','id'=>'asc'];

//        echo $this->goodsinfo->getLastSql();
        $goodsinfo = Db('Goodsinfo')->where($infogs)->order($infosort)->select();

        array_shift($infogs);
        $goodstc = Db::name('Goodsinfo')->where($infogs)->where($infogsp)->order($infosort)->select();


//        dump($goodsinfo);die;
        // 绑定套餐
        foreach ($goodstc as $k => $v) {
            $join = $v['join'];
            if($join == null){
                $join = [];
            }else{
                $join = explode(',',$join);
            }
            array_push($join,$v['id']);


            for($i=0;$i<count($join);$i++){

                $package = Db('Goodsinfo')->where('pid',$join[$i])->group('package')->order($infosort)->select();
//                dump(Db('Goodsinfo')->where('pid',$join[$i])->group('package')->order($infosort)->select());
                for($p=0;$p<count($package);$p++){
                    $packageNum = $package[$p]['package'];
                    $goodsinfo_bind = Db('Goodsinfo')->where('pid',$join[$i])->where('package',$packageNum)->order($infosort)->select();

                    $goodstc[$k]['goodstc_bind'][$p] = $goodsinfo_bind;
                }
//                $goodstc[$k]['goodstc_bind'] = $goodsinfo_bind;


            }

        }

//        echo 111;die;

        $catList = $this->catList;

        $this->assign([
            'cid'=>$cid,
            'catlist'   => $catList, //一级分类
            'info'      => $goodsInfo, //商品详情
            'comments'  => $comments,  //评论
            'likegoods' => $likeGoods, //猜你喜欢
            'specialgoods'    => $specialGoods,   //套餐
            'goodsinfo' => $goodsinfo, //商品
            'taocan'    => $goodstc,   //套餐
            'hotgoods'    => $hotGoods,   //套餐

        ]);



        return $this->fetch();

    }


    public function another($cid){
        //猜你喜欢
        //猜你喜欢改成同类目下的产品, 而不是后台填写的id
//        $cid = $goodsInfo['categoryid'];
//        $likeid = $goodsInfo['like'];
//        $likemap[] = ['id','in',$likeid];
        $likemap[] = ['categoryid','eq',$cid];
        $likemap[] = ['is_del','eq','0'];
        $likemap[] = ['completion','eq','1'];
        $likeGoods = Db('Goods')->where($likemap)->orderRand()->limit(9)->select();
        return $likeGoods;
    }




    /**

     * @param $cat_id

     * @param $cat_name

     * @param $parent_id

     * @param string $page

     * @return mixed

     * @user promise_1117

     * @time 2019/12/8/14:34

     * @description 商品分類

     */

    public function getCategoryList($cat_id,$cat_name,$parent_id,$page="",$nav_id="0"){



        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;

//        //获取一级分类名

//        $catList = $this->getCategoryParentList();

//        //获取热卖 分类

//        $catHotList = $this->getCategoryHotList();

//

//        $this->assign([

//            'catlist' => $catList,

//            'hotlist' => $catHotList,

//        ]);

        $goodsAll = [];

        $bread = '';



        if($parent_id == 0){



            $data = Db::name('category')->find($cat_id);



            $data['cat_two'] = Db::name('category')->where('parent_id',$cat_id)->select();





            $count = 0;

            foreach ($data['cat_two'] as $k=>$v){

                $goods = Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->where('completion','1')->order('create_time','desc')->select();



                $count += count(Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->where('completion','1')->order('create_time','desc')->select());

                foreach ($goods as $kk=>$vv){

                    array_push($goodsAll,$vv);

                }



            }







            $res = array_splice($goodsAll,$start,$num);







        }else{

//            獲取分類麵包屑



            $bread = Db::name('category')->where('cat_id',$parent_id)->find();



            $res = Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('completion','1')->order('create_time','desc')->limit($start,$num)->select();



            $count = count(Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('completion','1')->order('create_time','desc')->select());









        }





        $catList = $this->catList;



        // 分頁





//        $page_size = 20;

//        $num = ceil($count/20);

//        echo $count.'<br>';

//        echo $num;

//        $res = array_slice($res,$page-1,$page_size);





        $page_all = ceil($count/20);





        // 对数组根据销量排序

        $hotGoods = $res;
        array_multisort(array_column($hotGoods,'sale'),SORT_DESC,$res);

        $this->assign(

            ['hotGoods'=>$hotGoods,'goods'=>$res,'cat_id'=>$cat_id,'parent_id'=>$parent_id,'cat_name'=>$cat_name,'catlist'   => $catList,'bread'=>$bread,'page'=>$page,'page_all'=>$page_all,'nav_id'=>$nav_id]

        );

        return $this->fetch();

    }

    public function getCategoryListApi($cat_id,$cat_name,$parent_id,$page="",$nav_id="0"){

        $page == ''?$page = 1:$page = $page;
        $num = 20;
        $start = $page*20;
//        //获取一级分类名
//        $catList = $this->getCategoryParentList();
//        //获取热卖 分类
//        $catHotList = $this->getCategoryHotList();
//
//        $this->assign([
//            'catlist' => $catList,
//            'hotlist' => $catHotList,
//        ]);
        $goodsAll = [];
        $bread = '';

        if($parent_id == 0){

            $data = Db::name('category')->find($cat_id);

            $data['cat_two'] = Db::name('category')->where('parent_id',$cat_id)->select();


            $count = 0;
            foreach ($data['cat_two'] as $k=>$v){
                $goods = Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->where('completion','1')->order('create_time','desc')->select();

                $count += count(Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->where('completion','1')->order('create_time','desc')->select());
                foreach ($goods as $kk=>$vv){
                    array_push($goodsAll,$vv);
                }

            }



            $res = array_splice($goodsAll,$start,$num);



        }else{
//            獲取分類麵包屑

            $bread = Db::name('category')->where('cat_id',$parent_id)->find();

            $res = Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('completion','1')->order('create_time','desc')->limit($start,$num)->select();

            $count = count(Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('completion','1')->order('create_time','desc')->select());




        }


        $catList = $this->catList;

        // 分頁


//        $page_size = 20;
//        $num = ceil($count/20);
//        echo $count.'<br>';
//        echo $num;
//        $res = array_slice($res,$page-1,$page_size);


        $page_all = ceil($count/20);


        return ['goods'=>$res,'cat_id'=>$cat_id,'parent_id'=>$parent_id,'cat_name'=>$cat_name,'catlist'   => $catList,'bread'=>$bread,'page'=>$page,'page_all'=>$page_all,'nav_id'=>$nav_id];
    }







    public function hot($page=""){





        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //热销商品

        $hotwhere[] = ['hot','eq','1'];
//        $hotwhere[] = ['index','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];

        $hotwhere[] = ['completion','eq','1'];

        $hotsort    = ['sort'=>'desc','id'=>'desc'];

//        $hotsort    = ['visit'=>'desc'];

        $hotGoods = Db('Goods')->where($hotwhere)->order($hotsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($hotwhere)->order($hotsort)->select());







        $page_all = ceil($count/20);





        $this->assign([



            'hotgoods'  => $hotGoods, //热销商品

            'page'=>$page,

            'page_all'=>$page_all

        ]);



        return $this->fetch();

    }
    public function hotApi($page=""){





        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //热销商品

//        $hotwhere[] = ['hot','eq','1'];
        $hotwhere[] = ['hot','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];
        $hotwhere[] = ['completion','eq','1'];
        $hotsort    = ['sort'=>'desc','id'=>'desc'];

//        $hotsort    = ['visit'=>'desc'];

        $hotGoods = Db('Goods')->where($hotwhere)->order($hotsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($hotwhere)->order($hotsort)->select());







        $page_all = ceil($count/20);




        return [



            'hotgoods'  => $hotGoods, //热销商品

            'page'=>$page,

            'page_all'=>$page_all

        ];

    }





    public function new($page=""){





        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //新品

//        $newwhere[] = ['index','eq','1'];

        $newwhere[] = ['is_del','eq','0'];
        $newwhere[] = ['new','eq','1'];
        $newwhere[] = ['completion','eq','1'];
        $newsort = ['create_time'=>'desc'];

        $newGoods = Db('Goods')->where($newwhere)->order($newsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($newwhere)->order($newsort)->select());





//        dump($hotGoods);

//        dump($newGoods);



        $page_all = ceil($count/20);



        $this->assign([



            'newgoods'  => $newGoods, //新品商品

            'page'=>$page,

            'page_all'=>$page_all

        ]);



        return $this->fetch();

    }
    public function newApi($page=""){





        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //新品

//        $newwhere[] = ['index','eq','1'];

        $newwhere[] = ['is_del','eq','0'];
        $newwhere[] = ['new','eq','1'];
        $newwhere[] = ['completion','eq','1'];
        $newsort = ['create_time'=>'desc'];

        $newGoods = Db('Goods')->where($newwhere)->order($newsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($newwhere)->order($newsort)->select());





//        dump($hotGoods);

//        dump($newGoods);



        $page_all = ceil($count/20);



        return [



            'newgoods'  => $newGoods, //新品商品

            'page'=>$page,

            'page_all'=>$page_all

        ];


    }





    public function special($page="",$cat_id=""){



        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //热销商品

        $hotwhere[] = ['special','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];
        $hotwhere[] = ['completion','eq','1'];
        $hotsort    = ['id'=>'desc'];

//        $hotsort    = ['visit'=>'desc'];

        if(!empty($cat_id)){

            $subList=  Db('category')->where('parent_id',$cat_id)->select();
            $subId = array();
            foreach ($subList as $v){
                array_push($subId,$v['cat_id']);
            }

            $hotwhere[] = ['categoryid','in',$subId];



    }


        $hotGoods = Db('Goods')->where($hotwhere)->order($hotsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($hotwhere)->order($hotsort)->select());







        $page_all = ceil($count/20);





        $this->assign([



            'hotgoods'  => $hotGoods, //热销商品

            'page'=>$page,

            'page_all'=>$page_all

        ]);



        return $this->fetch();

    }
    public function specialApi($page="",$cat_id=""){

       

        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //热销商品

        $hotwhere[] = ['special','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];
        $hotwhere[] = ['completion','eq','1'];
        $hotsort    = ['id'=>'desc'];


        if(!empty($cat_id)){

            $subList=  Db('category')->where('parent_id',$cat_id)->select();
            $subId = array();
            foreach ($subList as $v){
                array_push($subId,$v['cat_id']);
            }

            $hotwhere[] = ['categoryid','in',$subId];



        }

//        $hotsort    = ['visit'=>'desc'];

        $hotGoods = Db('Goods')->where($hotwhere)->order($hotsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($hotwhere)->order($hotsort)->select());







        $page_all = ceil($count/20);





        return [



            'hotgoods'  => $hotGoods, //热销商品

            'page'=>$page,

            'page_all'=>$page_all

        ];


    }



    /**

     * @author:xiaohao

     * @time:2019/11/06 14:17

     * @description:最近浏览的商品

     */



    public function search($page="",$param=""){



        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;





        $param = input('search')?input('search'):$param;









        $sort    = ['sort'=>'desc','id'=>'desc'];



        $data = Db::name('goods')->where([['goods_no','like',$param]])->where('is_del','=',0)->where('completion','1')->order($sort)->limit($start,$num)->select();

        if(empty($data)){
            $data = Db::name('goods')->where([['name','like','%'.$param.'%']])->where('is_del','=',0)->where('completion','1')->order($sort)->limit($start,$num)->select();
        }

        if(empty($data)){

            $cat_id = Db::name('category')->field('cat_id')->where([['cat_name','like','%'.$param.'%']])->where('is_show','=',1)->limit($start,$num)->select();
            foreach ($cat_id as $k=>$v){
                $data = $this->getCategoryListApi($v['cat_id'],$param,0,$page)['goods'];
            }
        }







        $count = count(Db::name('goods')->where('is_del',0)->where('completion','1')->where('goods_no','like',$param)->whereOr('name','like','%'.$param.'%')->select());









//        dump($data);

        $page_all = ceil($count/20);





        $this->assign([



            'goods'  => $data, //热销商品

            'page'=>$page,

            'page_all'=>$page_all,

            'search'=>$param

        ]);



        return $this->fetch();

    }


    public function searchApi($page="",$param=""){



        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = $page*20;





        $param = input('search')?input('search'):$param;









        $sort    = ['sort'=>'desc','id'=>'desc'];



        $data = Db::name('goods')->where([['goods_no','like',$param]])->where('is_del','=',0)->where('completion','1')->order($sort)->limit($start,$num)->select();
        $count = count(Db::name('goods')->where([['goods_no','like',$param]])->where('is_del','=',0)->where('completion','1')->order($sort)->select());

        if(empty($data)){
            $data = Db::name('goods')->where([['name','like','%'.$param.'%']])->where('is_del','=',0)->where('completion','1')->order($sort)->limit($start,$num)->select();
            $count = count(Db::name('goods')->where([['name','like','%'.$param.'%']])->where('is_del','=',0)->where('completion','1')->order($sort)->select());
        }

        if(empty($data)){

            $cat_id = Db::name('category')->field('cat_id')->where([['cat_name','like','%'.$param.'%']])->where('is_show','=',1)->limit($start,$num)->select();
            foreach ($cat_id as $k=>$v){
                $data = $this->getCategoryListApi($v['cat_id'],$param,0,$page)['goods'];
                $count = count($this->getCategoryListApi($v['cat_id'],$param,0,$page)['goods']);
            }
        }









//        dump($data);die;

        $page_all = ceil($count/20);


        return [



            'goods'  => $data, //热销商品

            'page'=>$page,

            'page_all'=>$page_all,

            'search'=>$param

        ];

    }





    public function getVisitGoodsList(){

        $parameter = $this->request->param('goodids');

        $visitwhere[] = ['id','in',$parameter];

        $visitsort = ['sort'=>'desc','id'=>'desc'];

        $visitList = Db('Goods')->field('id,img,name,sell_price,market_price')->where($visitwhere)->order($visitsort)->select();

        returnResponse(200,'请求成功',$visitList);

    }



    /**

     * @author:xiaohao

     * @time:2019/11/05 20:20

     * @param $catid    分类的id

     * @param string $page  分页数

     * @return mixed

     * @description:获取商品列表

     */

    public function getGoodsList(){

        $catid = $this->request->param('catid');

        $page  = $this->request->param('page');

        $pid = Db('Category')->where('cat_id',$catid)->find();

        if(empty($pid['parent_id'])){

            $catids = Db('Category')->where('parent_id',$catid)->where('is_show','1')->column('cat_id');

            $golimap[] = $golimap[] = ['categoryid','in',$catids];

        }else{

            $golimap[] = ['categoryid','eq',$catid];

        }

        $golimap[] = ['is_del','eq','0'];

        $golisort  = ['sort'=>'desc','id'=>'desc'];

        $goodslist = Db('Goods')->field('id,img,name,keywords,sell_price,market_price,sale')->where($golimap)->order($golisort)->limit($page,10)->select();

        foreach ($goodslist as $k=>$v){

            $word = explode(',',$v['keywords']);

            $v['keyword'] = $word[0];

            $goodslist[$k] = $v;

        }

//        if($goodslist){

        returnResponse(200,'请求成功',$goodslist);

//        }

//        returnResponse(100,'暫無商品,敬請期待！！！');

    }





    /**

     * @author:xiaohao

     * @time:2019/11/04 14:01

     * @description:获取二级分类

     */

    public function getTwoCateList(){

        //获取二级分类名

        $parameter = request()->param('catid');

        if($parameter){

            $catTwoList = $this->getCategoryTwoList($parameter);

        }

        if($catTwoList){

            returnResponse(200,'请求成功',$catTwoList);

        }

        returnResponse(100,'异常！！！');

    }



    /**

     * @author:xiaohao

     * @time:Times

     * @param $catid

     * @description:获取一级分类图片

     */

    public function getParentImage(){

        $catid = request()->param('catid');

        $catTowLists = $this->getParentImageLevel($catid);

        if($catTowLists){

            returnResponse(200,'请求成功',$catTowLists);

        }

        returnResponse(100,'异常！！！');

    }





    public function shortCut(){

        return $this->fetch();

    }


    /**
     * @user promise_1117
     * @time 2020/3/5/16:56
     * @description 开心乐购手机活动分类榜单页面
     */
    public function activity($cat_id,$cat_name,$parent_id,$page="",$nav_id="0"){


        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;

//        //获取一级分类名

//        $catList = $this->getCategoryParentList();

//        //获取热卖 分类

//        $catHotList = $this->getCategoryHotList();

//

//        $this->assign([

//            'catlist' => $catList,

//            'hotlist' => $catHotList,

//        ]);

        $goodsAll = [];
        $hotGoodsAll = [];

        $bread = '';



        if($parent_id == 0){



            $data = Db::name('category')->find($cat_id);



            $data['cat_two'] = Db::name('category')->where('parent_id',$cat_id)->select();





            $count = 0;

            foreach ($data['cat_two'] as $k=>$v){

                $goods = Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->where('index','eq',1)->order('sale','desc')->select();

                $hotGoods = Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->where('hot','eq',1)->order('sale','desc')->select();


                $count += count(Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->where('index','eq',1)->order('sale','desc')->select());

                foreach ($goods as $kk=>$vv){

                    array_push($goodsAll,$vv);

                }

                foreach ($hotGoods as $kk=>$vv){

                    array_push($hotGoodsAll,$vv);

                }



            }







            $res = array_splice($goodsAll,$start,$num);
            $hotres = array_splice($hotGoodsAll,$start,$num);



            foreach ($res as $kk=>$vv){



                $goodstc = Db('Goodsinfo')->where([['goods_id','eq',$vv['id']],['pid','eq','0'],['taocan','neq','0']])->order(['order'=>'desc','id'=>'asc'])->select();

                // 绑定套餐



                foreach ($goodstc as $k => $v) {

                    $join = $v['join'];

                    if($join == null){

                        $join = [];

                    }else{

                        $join = explode(',',$join);

                    }

                    array_push($join,$v['id']);







                    for($i=0;$i<count($join);$i++){

                        $goodsinfo_bind = Db('Goodsinfo')->where('pid',$join[$i])->order($infosort)->select();



                        $goodstc[$k]['goodstc_bind'] = $goodsinfo_bind;



                        $sell_price = 0;

                        $market_price = 0;



                        foreach ($goodsinfo_bind as $k_=>$v_){

                            if(is_null($goodstc[$k]['goodstc_bind'][$k_]['sell_price'])){

                                $res[$kk]['bind_goods'][$k]['goodstc_bind'][$k_]['sell_price'] = 0;

                            }

                            if(is_null($goodstc[$k]['goodstc_bind'][$k_]['market_price'])){

                                $goodstc[$k]['goodstc_bind'][$k_]['market_price'] = 0;

                            }

                            $sell_price += intval($goodstc[$k]['goodstc_bind'][$k_]['sell_price']);



                            $market_price += intval($goodstc[$k]['goodstc_bind'][$k_]['market_price']);



                        }

                        $goodstc[$k]['sell_price'] = $sell_price;



                        $goodstc[$k]['market_price'] = $market_price;

                        $goodstc[$k]['cost_price'] = $market_price - $sell_price;





                    }



                }



                $res[$kk]['bind_goods'] = $goodstc;





            }



        }else{

//            獲取分類麵包屑



            $bread = Db::name('category')->where('cat_id',$parent_id)->find();



            $res = Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('index','eq',1)->order('sale','desc')->limit($start,$num)->select();
            $hotres = Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('hot','eq',1)->order('sale','desc')->limit($start,$num)->select();



            $count = count(Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('index','eq',1)->order('sale','desc')->select());





            foreach ($res as $kk=>$vv){



                $goodstc = Db('Goodsinfo')->where([['goods_id','eq',$vv['id']],['pid','eq','0'],['taocan','neq','0']])->order(['order'=>'desc','id'=>'asc'])->select();

                // 绑定套餐



                foreach ($goodstc as $k => $v) {

                    $join = $v['join'];

                    if($join == null){

                        $join = [];

                    }else{

                        $join = explode(',',$join);

                    }

                    array_push($join,$v['id']);







                    for($i=0;$i<count($join);$i++){

                        $goodsinfo_bind = Db('Goodsinfo')->where('pid',$join[$i])->order($infosort)->select();



                        $goodstc[$k]['goodstc_bind'] = $goodsinfo_bind;



                        $sell_price = 0;

                        $market_price = 0;



                        foreach ($goodsinfo_bind as $k_=>$v_){

                            if(is_null($goodstc[$k]['goodstc_bind'][$k_]['sell_price'])){

                                $res[$kk]['bind_goods'][$k]['goodstc_bind'][$k_]['sell_price'] = 0;

                            }

                            if(is_null($goodstc[$k]['goodstc_bind'][$k_]['market_price'])){

                                $goodstc[$k]['goodstc_bind'][$k_]['market_price'] = 0;

                            }

                            $sell_price += intval($goodstc[$k]['goodstc_bind'][$k_]['sell_price']);



                            $market_price += intval($goodstc[$k]['goodstc_bind'][$k_]['market_price']);



                        }

                        $goodstc[$k]['sell_price'] = $sell_price;



                        $goodstc[$k]['market_price'] = $market_price;

                        $goodstc[$k]['cost_price'] = $market_price - $sell_price;





                    }



                }



                $res[$kk]['bind_goods'] = $goodstc;





            }



        }





        $catList = $this->catList;



        // 分頁





//        $page_size = 20;

//        $num = ceil($count/20);

//        echo $count.'<br>';

//        echo $num;

//        $res = array_slice($res,$page-1,$page_size);





        $page_all = ceil($count/20);



        // 对数组根据销量排序

        array_multisort(array_column($res,'sale'),SORT_DESC,$res);

        $this->assign(

            ['goods'=>$res,'hotgoods'=>$hotres,'cat_id'=>$cat_id,'parent_id'=>$parent_id,'cat_name'=>$cat_name,'catlist'   => $catList,'bread'=>$bread,'page'=>$page,'page_all'=>$page_all,'nav_id'=>$nav_id]

        );

        return $this->fetch();

    }


    public function sub_activity(){
        // 活动总榜单页面

        $sub_activity = Db::name('goods')->where('is_del','0')->where('index','eq',1)->order('sale','desc')->limit(0,20)->select();
        $hotGoods = Db::name('goods')->where('is_del','0')->where('hot','eq',1)->order('sale','desc')->limit(0,20)->select();

        $this->assign([
            'sub_activity' => $sub_activity,
            'hotgoods' => $hotGoods
        ]);

        return $this->fetch();
    }

    public function desktop(){
        $res = Db::name('count')->where('id', 1)->setInc('desktop');
        return $res;
    }


    public function redis(){
        config('app_debug',true);
        config('app_trace',true);
        //连接本地的 Redis 服务
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);

        if($redis->exists('data')){
            echo 111;
            $data = unserialize($redis->get('data'));
        }else{
            echo 222;
            $data = Db('goods')->select();
            $redis->setex('data','1',serialize($data));
        }
        dump($data);
    }

}
