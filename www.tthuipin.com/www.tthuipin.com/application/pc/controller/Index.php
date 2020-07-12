<?php

namespace app\pc\controller;

use app\pc\controller\Base;

use think\Db;

class Index extends Base

{

    /**

     * Index constructor.

     * @throws \think\db\exception\DataNotFoundException

     * @throws \think\db\exception\ModelNotFoundException

     * @throws \think\exception\DbException

     */

    public function __construct()

    {

        parent::__construct();

        $this->banner    = Db('Banner'); //banner表

        $this->category  = Db('Category'); //分类表

        $this->goods     = Db('Goods'); //商品表

        $this->comments  = Db('Comments'); //评论表

        $this->goodsinfo = Db('Goodsinfo'); //商品详情表



        //获取header頂部分类名

        $this->catList = $this->getCategoryParentList();

        foreach($this->catList as $k=>$v){

            $cat_two = Db::name('category')->where('parent_id',$v['cat_id'])->where('is_show','1')->order('sort_order','desc')->select();

            $this->catList[$k]['cat_two'] = $cat_two;

        }

        $this->assign('catlist',$this->catList);



        // 頂部購物車



        $this->shop_car = $this->carSession();

        $this->assign('shop_car',$this->shop_car);

//        dump($this->shop_car);

    }




    public function index(){

        // 根据不同的域名跳转不同的首页
        $server_name = request()->server('SERVER_NAME');

        if(preg_match('/chaofengguwu\.shop|happyguwu\.shop|youmeishop\.shop/',$server_name)){
            return $this->fetch('homePage/home');
        }else if(preg_match('/chaolowprice\.shop|meipingowu\.shop|beautifulhall\.shop/',$server_name)){
            return $this->fetch('homePage/home_page');
        }else if(preg_match('/newgoods\.shop|hothotgoods\.shop|chaodijia\.shop/',$server_name)){
            return $this->fetch('homePage/shophome');
        }else if(preg_match('/xinpinshishang\.shop|xinxingou\.shop|promiseyou\.shop/',$server_name)){
            return $this->fetch('homePage/home_page2');
        }

        //banner获取

        $bmap[] = ['is_show','eq','0'];

        $bmap[] = ['show_in_nav','eq','0'];

        $bsort  = ['order'=>'desc','id'=>'desc'];

        $res = $this->banner->where($bmap)->order($bsort)->select();

        //获取一级分类名

        $catList = $this->getCategoryParentList();

        foreach($catList as $k=>$v){

            $cat_two = Db::name('category')->where('parent_id',$v['cat_id'])->where('is_show','1')->order('sort_order','desc')->select();

            $catList[$k]['cat_two'] = $cat_two;

        }



        //获取所有分类

        $catListt = $this->getCategoryParentListt();

        //获取热卖 分类

        $catHotList = $this->getCategoryHotList();

        //热销商品



        $hotwhere[] = ['index','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];

        $hotwhere[] = ['completion','eq','1'];

        $hotsort    = ['sort'=>'desc','id'=>'desc'];



        // ①获取所有产品

        $hotGoodsArr = Db('Goods')

            ->where($hotwhere)

            ->order($hotsort)

            ->limit(0,20)

            ->select();



        // ①获取所有产品的id数组集合

        $goods_id_list_arr = Db('Goods')

            ->field('id')

            ->where($hotwhere)

            ->order($hotsort)

            ->limit(0,20)

            ->select();



        // ①对商品数组所有产品id拼凑成字符串连接便于sql IN查询

        $goods_id_list = join(',',array_column($goods_id_list_arr,'id'));





        // ②得到所有套餐集合

        $goodstcArr = Db('Goodsinfo')

            ->where([['goods_id','in',$goods_id_list],['pid','eq','0'],['taocan','neq','0']])

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();





        // ②获取所有套餐的id数组集合

        $goodstc_id_list_arr = Db('Goodsinfo')

            ->field('id')

            ->where([['goods_id','in',$goods_id_list],['pid','eq','0'],['taocan','neq','0']])

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();



        // ②对商品数组所有套餐id拼凑成字符串连接便于sql IN查询

        $goodstc_id_list = join(',',array_column($goodstc_id_list_arr,'id'));







        // ③得到所有商品集合

        $packageArr = Db('Goodsinfo')

            ->where('pid','in',$goodstc_id_list)

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();







//        dump($hotGoodsArr);die;

//        dump($goodstcArr);die;

//        dump($packageArr);die;



        foreach($hotGoodsArr as $kk=>$vv){

            $goodstc = array();

            // 查找数组中的每个产品对应的套餐

            foreach ($goodstcArr as $kkk=>$vvv){

                if($vvv['goods_id']==$vv['id']){

                    array_push($goodstc,$vvv);

                }

            }

            foreach ($goodstc as $k => $v) {





                $package = array();

                // 查找数组中的每个产品对应的套餐

                foreach ($packageArr as $kkk=>$vvv){

                    if($vvv['pid']==$v['id']){

                        array_push($package,$vvv);

                    }

                }



                // 删除二维数组重复的package字段 , 统计多少个套餐

                $tmp_arr = array();



                foreach($package as $tmp_k => $tmp_v)

                {

                    if(in_array($tmp_v['package'], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true

                    {

                        unset($package[$tmp_k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值

                    }

                    else {

                        $tmp_arr[$tmp_k] = $tmp_v['package'];  //将不同的值放在该数组中保存

                    }

                }



//                $package = Db('Goodsinfo')->where('pid', $v['id'])->group('package')->order($infosort)->select();

//                dump(Db('Goodsinfo')->where('pid',$join[$i])->group('package')->order($infosort)->select());



                for ($p = 0; $p < count($package); $p++) {

                    $packageNum = $package[$p]['package'];





                    $goodsinfo_bind = array();

                    // 查找数组中的每个产品对应的套餐

                    foreach ($packageArr as $kkk=>$vvv){

                        if($vvv['pid']==$v['id'] && $vvv['package']==$packageNum){

                            array_push($goodsinfo_bind,$vvv);

                        }

                    }

//                    $goodsinfo_bind = Db('Goodsinfo')->where('pid', $join[$i])->where('package', $packageNum)->order($infosort)->select();



                    $goodstc[$k]['goodstc_bind'][$p] = $goodsinfo_bind;

                }

            }

            $hotGoodsArr[$kk]['bind_goods'] = $goodstc;

        }











        //新品

//        $newwhere[] = ['index','eq','1'];

        $newwhere[] = ['is_del','eq','0'];

        $newwhere[] = ['completion','eq','1'];

        $newsort = ['create_time'=>'desc'];

        // ①获取所有产品

        $newGoodsArr = Db('Goods')

            ->where($newwhere)

            ->order($newsort)

            ->limit(0,20)

            ->select();



        // ①获取所有产品的id数组集合

        $goods_id_list_arr = Db('Goods')

            ->field('id')

            ->where($newwhere)

            ->order($newsort)

            ->limit(0,20)

            ->select();



        // ①对商品数组所有产品id拼凑成字符串连接便于sql IN查询

        $goods_id_list = join(',',array_column($goods_id_list_arr,'id'));





        // ②得到所有套餐集合

        $goodstcArr = Db('Goodsinfo')

            ->where([['goods_id','in',$goods_id_list],['pid','eq','0'],['taocan','neq','0']])

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();





        // ②获取所有套餐的id数组集合

        $goodstc_id_list_arr = Db('Goodsinfo')

            ->field('id')

            ->where([['goods_id','in',$goods_id_list],['pid','eq','0'],['taocan','neq','0']])

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();



        // ②对商品数组所有套餐id拼凑成字符串连接便于sql IN查询

        $goodstc_id_list = join(',',array_column($goodstc_id_list_arr,'id'));







        // ③得到所有商品集合

        $packageArr = Db('Goodsinfo')

            ->where('pid','in',$goodstc_id_list)

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();







//        dump($hotGoodsArr);die;

//        dump($goodstcArr);die;

//        dump($packageArr);die;



        foreach($newGoodsArr as $kk=>$vv){

            $goodstc = array();

            // 查找数组中的每个产品对应的套餐

            foreach ($goodstcArr as $kkk=>$vvv){

                if($vvv['goods_id']==$vv['id']){

                    array_push($goodstc,$vvv);

                }

            }

            foreach ($goodstc as $k => $v) {





                $package = array();

                // 查找数组中的每个产品对应的套餐

                foreach ($packageArr as $kkk=>$vvv){

                    if($vvv['pid']==$v['id']){

                        array_push($package,$vvv);

                    }

                }



                // 删除二维数组重复的package字段 , 统计多少个套餐

                $tmp_arr = array();



                foreach($package as $tmp_k => $tmp_v)

                {

                    if(in_array($tmp_v['package'], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true

                    {

                        unset($package[$tmp_k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值

                    }

                    else {

                        $tmp_arr[$tmp_k] = $tmp_v['package'];  //将不同的值放在该数组中保存

                    }

                }



//                $package = Db('Goodsinfo')->where('pid', $v['id'])->group('package')->order($infosort)->select();

//                dump(Db('Goodsinfo')->where('pid',$join[$i])->group('package')->order($infosort)->select());



                for ($p = 0; $p < count($package); $p++) {

                    $packageNum = $package[$p]['package'];





                    $goodsinfo_bind = array();

                    // 查找数组中的每个产品对应的套餐

                    foreach ($packageArr as $kkk=>$vvv){

                        if($vvv['pid']==$v['id'] && $vvv['package']==$packageNum){

                            array_push($goodsinfo_bind,$vvv);

                        }

                    }

//                    $goodsinfo_bind = Db('Goodsinfo')->where('pid', $join[$i])->where('package', $packageNum)->order($infosort)->select();



                    $goodstc[$k]['goodstc_bind'][$p] = $goodsinfo_bind;

                }

            }

            $newGoodsArr[$kk]['bind_goods'] = $goodstc;

        }







        $this->assign([

            'banner'    => $res, //banner

            'catlist'   => $catList, //一级分类

            'catlistt'  => $catListt, //所有分类

            'hotlist'   => $catHotList, //获取热卖

            'hotgoods'  => $hotGoodsArr, //热销商品

            'newgoods'  => $newGoodsArr, //新品商品

        ]);



        return $this->fetch();



    }





    public function indexApi(){



        //banner获取

        $bmap[] = ['is_show','eq','0'];

        $bmap[] = ['show_in_nav','eq','0'];

        $bsort  = ['order'=>'desc','id'=>'desc'];

        $res = $this->banner->where($bmap)->order($bsort)->select();

        //获取一级分类名

        $catList = $this->getCategoryParentList();

        foreach($catList as $k=>$v){

            $cat_two = Db::name('category')->where('parent_id',$v['cat_id'])->where('is_show','1')->order('sort_order','desc')->select();

            $catList[$k]['cat_two'] = $cat_two;

        }



        //获取所有分类

        $catListt = $this->getCategoryParentListt();

        //获取热卖 分类

        $catHotList = $this->getCategoryHotList();

        //热销商品



        $hotwhere[] = ['index','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];

        $hotwhere[] = ['completion','eq','1'];

        $hotsort    = ['sort'=>'desc','id'=>'desc'];



        // ①获取所有产品

        $hotGoodsArr = Db('Goods')

            ->where($hotwhere)

            ->order($hotsort)

            ->limit(0,20)

            ->select();



        // ①获取所有产品的id数组集合

        $goods_id_list_arr = Db('Goods')

            ->field('id')

            ->where($hotwhere)

            ->order($hotsort)

            ->limit(0,20)

            ->select();



        // ①对商品数组所有产品id拼凑成字符串连接便于sql IN查询

        $goods_id_list = join(',',array_column($goods_id_list_arr,'id'));





        // ②得到所有套餐集合

        $goodstcArr = Db('Goodsinfo')

            ->where([['goods_id','in',$goods_id_list],['pid','eq','0'],['taocan','neq','0']])

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();





        // ②获取所有套餐的id数组集合

        $goodstc_id_list_arr = Db('Goodsinfo')

            ->field('id')

            ->where([['goods_id','in',$goods_id_list],['pid','eq','0'],['taocan','neq','0']])

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();



        // ②对商品数组所有套餐id拼凑成字符串连接便于sql IN查询

        $goodstc_id_list = join(',',array_column($goodstc_id_list_arr,'id'));







        // ③得到所有商品集合

        $packageArr = Db('Goodsinfo')

            ->where('pid','in',$goodstc_id_list)

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();







//        dump($hotGoodsArr);die;

//        dump($goodstcArr);die;

//        dump($packageArr);die;



        foreach($hotGoodsArr as $kk=>$vv){

            $goodstc = array();

            // 查找数组中的每个产品对应的套餐

            foreach ($goodstcArr as $kkk=>$vvv){

                if($vvv['goods_id']==$vv['id']){

                    array_push($goodstc,$vvv);

                }

            }

            foreach ($goodstc as $k => $v) {





                $package = array();

                // 查找数组中的每个产品对应的套餐

                foreach ($packageArr as $kkk=>$vvv){

                    if($vvv['pid']==$v['id']){

                        array_push($package,$vvv);

                    }

                }



                // 删除二维数组重复的package字段 , 统计多少个套餐

                $tmp_arr = array();



                foreach($package as $tmp_k => $tmp_v)

                {

                    if(in_array($tmp_v['package'], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true

                    {

                        unset($package[$tmp_k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值

                    }

                    else {

                        $tmp_arr[$tmp_k] = $tmp_v['package'];  //将不同的值放在该数组中保存

                    }

                }



//                $package = Db('Goodsinfo')->where('pid', $v['id'])->group('package')->order($infosort)->select();

//                dump(Db('Goodsinfo')->where('pid',$join[$i])->group('package')->order($infosort)->select());



                for ($p = 0; $p < count($package); $p++) {

                    $packageNum = $package[$p]['package'];





                    $goodsinfo_bind = array();

                    // 查找数组中的每个产品对应的套餐

                    foreach ($packageArr as $kkk=>$vvv){

                        if($vvv['pid']==$v['id'] && $vvv['package']==$packageNum){

                            array_push($goodsinfo_bind,$vvv);

                        }

                    }

//                    $goodsinfo_bind = Db('Goodsinfo')->where('pid', $join[$i])->where('package', $packageNum)->order($infosort)->select();



                    $goodstc[$k]['goodstc_bind'][$p] = $goodsinfo_bind;

                }

            }

            $hotGoodsArr[$kk]['bind_goods'] = $goodstc;

        }











        //新品

//        $newwhere[] = ['index','eq','1'];

        $newwhere[] = ['is_del','eq','0'];

        $newwhere[] = ['completion','eq','1'];

        $newsort = ['create_time'=>'desc'];

        // ①获取所有产品

        $newGoodsArr = Db('Goods')

            ->where($newwhere)

            ->order($newsort)

            ->limit(0,20)

            ->select();



        // ①获取所有产品的id数组集合

        $goods_id_list_arr = Db('Goods')

            ->field('id')

            ->where($newwhere)

            ->order($newsort)

            ->limit(0,20)

            ->select();



        // ①对商品数组所有产品id拼凑成字符串连接便于sql IN查询

        $goods_id_list = join(',',array_column($goods_id_list_arr,'id'));





        // ②得到所有套餐集合

        $goodstcArr = Db('Goodsinfo')

            ->where([['goods_id','in',$goods_id_list],['pid','eq','0'],['taocan','neq','0']])

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();





        // ②获取所有套餐的id数组集合

        $goodstc_id_list_arr = Db('Goodsinfo')

            ->field('id')

            ->where([['goods_id','in',$goods_id_list],['pid','eq','0'],['taocan','neq','0']])

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();



        // ②对商品数组所有套餐id拼凑成字符串连接便于sql IN查询

        $goodstc_id_list = join(',',array_column($goodstc_id_list_arr,'id'));







        // ③得到所有商品集合

        $packageArr = Db('Goodsinfo')

            ->where('pid','in',$goodstc_id_list)

            ->order(['order'=>'desc','id'=>'asc'])

            ->select();







//        dump($hotGoodsArr);die;

//        dump($goodstcArr);die;

//        dump($packageArr);die;



        foreach($newGoodsArr as $kk=>$vv){

            $goodstc = array();

            // 查找数组中的每个产品对应的套餐

            foreach ($goodstcArr as $kkk=>$vvv){

                if($vvv['goods_id']==$vv['id']){

                    array_push($goodstc,$vvv);

                }

            }

            foreach ($goodstc as $k => $v) {





                $package = array();

                // 查找数组中的每个产品对应的套餐

                foreach ($packageArr as $kkk=>$vvv){

                    if($vvv['pid']==$v['id']){

                        array_push($package,$vvv);

                    }

                }



                // 删除二维数组重复的package字段 , 统计多少个套餐

                $tmp_arr = array();



                foreach($package as $tmp_k => $tmp_v)

                {

                    if(in_array($tmp_v['package'], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true

                    {

                        unset($package[$tmp_k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值

                    }

                    else {

                        $tmp_arr[$tmp_k] = $tmp_v['package'];  //将不同的值放在该数组中保存

                    }

                }



//                $package = Db('Goodsinfo')->where('pid', $v['id'])->group('package')->order($infosort)->select();

//                dump(Db('Goodsinfo')->where('pid',$join[$i])->group('package')->order($infosort)->select());



                for ($p = 0; $p < count($package); $p++) {

                    $packageNum = $package[$p]['package'];





                    $goodsinfo_bind = array();

                    // 查找数组中的每个产品对应的套餐

                    foreach ($packageArr as $kkk=>$vvv){

                        if($vvv['pid']==$v['id'] && $vvv['package']==$packageNum){

                            array_push($goodsinfo_bind,$vvv);

                        }

                    }

//                    $goodsinfo_bind = Db('Goodsinfo')->where('pid', $join[$i])->where('package', $packageNum)->order($infosort)->select();



                    $goodstc[$k]['goodstc_bind'][$p] = $goodsinfo_bind;

                }

            }

            $newGoodsArr[$kk]['bind_goods'] = $goodstc;

        }







        return [

            'banner'    => $res, //banner

            'catlist'   => $catList, //一级分类

            'catlistt'  => $catListt, //所有分类

            'hotlist'   => $catHotList, //获取热卖

            'hotgoods'  => $hotGoodsArr, //热销商品

            'newgoods'  => $newGoodsArr, //新品商品

        ];

    }



    /**

     * @return mixed

     * @user promise_1117

     * @time 2019/12/8/14:34

     * @description 商品詳情

     */

    public function pcGetGoodsInfo(){



        $gid = request()->param('goodsid');

        //商品浏览量+1

        $this->goods->where('id',$gid)->setInc('liulan');

        //商品内容

        $goodsInfo = $this->goods

            ->where('id',$gid)

            ->find();

//        dump($goodsInfo);



        //猜你喜欢改成同类目下的产品, 而不是后台填写的id

        $cid = $goodsInfo['categoryid'];





        $likeid = $goodsInfo['like'];

        $likemap[] = ['categoryid','eq',$cid];

        $likemap[] = ['is_del','eq','0'];

        $likemap[] = ['completion','eq','1'];

        $likesort  = ['sort'=>'desc','id'=>'desc'];

//        $likeGoods = Db('Goods')->where($likemap)->order($likesort)->limit(0,4)->select();

//        dump($likeGoods);die;

        $likeGoods = Db('Goods')->where($likemap)->order($likesort)->limit(0,12)->select();



        // 排行榜

//        $rankmap[] = ['is_del','eq','0'];

//        $rankmap[] = ['index','eq',1];

//        $ranksort[] = ['sale'=>'desc'];

        $leaderboard  = Db::name('goods')->where('is_del','0')->where('index','eq',1)->order('sale','desc')->limit(0,20)->select();



        //评论内容

        $commap[] = ['goods_id','eq',$gid];

        $commap[] = ['username','neq',''];

        $comsort = ['sort'=>'desc','id'=>'desc'];

        $comments = $this->comments->where($commap)->order($comsort)->select();

        //商品编号详情

        $infogsp[] = ['pid','eq','0'];

        $infogsp[] = ['taocan','neq','0'];

        $infogs[] = ['pid','neq','0'];

        $infogs[] = ['goods_id','eq',$gid];

        $infosort = ['order'=>'desc','id'=>'asc'];



//        echo $this->goodsinfo->getLastSql();

        $goodsinfo = Db('Goodsinfo')->where($infogs)->order($infosort)->select();



        array_shift($infogs);

        $goodstc = $this->goodsinfo->where($infogs)->where($infogsp)->order($infosort)->select();





//        dump($goodstc);

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



//        dump($goodstc);



        $catList = $this->catList;



        $this->assign([

            'catlist'   => $catList, //一级分类

            'info'      => $goodsInfo, //商品详情

            'comments'  => $comments,  //评论

            'likegoods' => $likeGoods, //猜你喜欢

            'leaderboard'=>$leaderboard,//排行榜

            'goodsinfo' => $goodsinfo, //商品

            'taocan'    => $goodstc,   //套餐

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

    public function pcGetCategoryList($cat_id,$cat_name,$parent_id,$page=""){



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

                $goods = Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->where('completion','eq','1')->order('create_time','desc')->select();



                $count += count(Db::name('goods')->where('categoryid',$v['cat_id'])->where('is_del','0')->order('create_time','desc')->select());

                foreach ($goods as $kk=>$vv){

                    array_push($goodsAll,$vv);

                }



            }







            $res = array_splice($goodsAll,$start,$num);



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



                $res[$kk]['bind_goods'] = $goodstc;





            }



        }else{

//            獲取分類麵包屑



            $bread = Db::name('category')->where('cat_id',$parent_id)->find();



            $res = Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('completion','eq','1')->order('create_time','desc')->limit($start,$num)->select();



            $count = count(Db::name('goods')->where('categoryid',$cat_id)->where('is_del','0')->where('completion','eq','1')->order('create_time','desc')->select());





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





        $this->assign(

            ['goods'=>$res,'cat_id'=>$cat_id,'parent_id'=>$parent_id,'cat_name'=>$cat_name,'catlist'   => $catList,'bread'=>$bread,'page'=>$page,'page_all'=>$page_all]

        );

        return $this->fetch();

    }









    public function pcHot($page=""){



        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //热销商品

//        $hotwhere[] = ['index','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];

        $hotwhere[] = ['completion','eq','1'];

        $hotsort    = ['sort'=>'desc','id'=>'desc'];

//        $hotsort    = ['visit'=>'desc'];

        $hotGoods = Db('Goods')->where($hotwhere)->order($hotsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($hotwhere)->order($hotsort)->select());



        foreach ($hotGoods as $kk=>$vv){



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



            $hotGoods[$kk]['bind_goods'] = $goodstc;





        }



        $page_all = ceil($count/20);





        $this->assign([



            'hotgoods'  => $hotGoods, //热销商品

            'page'=>$page,

            'page_all'=>$page_all

        ]);



        return $this->fetch();

    }





    public function pcNew($page=""){





        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //新品

        $newwhere[] = ['index','eq','1'];

        $newwhere[] = ['is_del','eq','0'];

        $newwhere[] = ['completion','eq','1'];

        $newsort = ['create_time'=>'desc'];

        $newGoods = Db('Goods')->where($newwhere)->order($newsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($newwhere)->order($newsort)->select());

        foreach ($newGoods as $kk=>$vv){



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



            $newGoods[$kk]['bind_goods'] = $goodstc;





        }



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





    public function pcSpecial($page=""){



        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;



        //热销商品

//        $hotwhere[] = ['index','eq','1'];

        $hotwhere[] = ['is_del','eq','0'];

        $hotwhere[] = ['completion','eq','1'];

        $hotsort    = ['sort'=>'desc','id'=>'desc'];

//        $hotsort    = ['visit'=>'desc'];

        $hotGoods = Db('Goods')->where($hotwhere)->order($hotsort)->limit($start,$num)->select();



        $count = count(Db('Goods')->where($hotwhere)->order($hotsort)->select());



        foreach ($hotGoods as $kk=>$vv){



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



            $hotGoods[$kk]['bind_goods'] = $goodstc;





        }



        $page_all = ceil($count/20);





        $this->assign([



            'hotgoods'  => $hotGoods, //热销商品

            'page'=>$page,

            'page_all'=>$page_all

        ]);



        return $this->fetch();

    }







    public function pcSearch($page="",$param=""){



        $page == ''?$page = 1:$page = $page;

        $num = 20;

        $start = ($page-1)*20;





        $param = input('search')?input('search'):$param;









        $sort    = ['sort'=>'desc','id'=>'desc'];



        $data = Db::name('goods')->where('is_del',0)->where('completion','eq','1')->where('goods_no','like',$param)->order($sort)->limit($start,$num)->select();

        $count = count(Db::name('goods')->where('is_del',0)->where('completion','eq','1')->where('goods_no','like',$param)->select());



        if(empty($data)){

            $data = Db::name('goods')->where([['name','like','%'.$param.'%']])->where('is_del','=',0)->where('completion','1')->order($sort)->limit($start,$num)->select();

            $count = count(Db::name('goods')->where('is_del',0)->where('completion','eq','1')->where('name','like','%'.$param.'%')->select());

        }









        foreach ($data as $kk=>$vv){



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



            $data[$kk]['bind_goods'] = $goodstc;





        }





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

    /**

     * @author:xiaohao

     * @time:2019/11/06 14:17

     * @description:最近浏览的商品

     */

    public function getVisitGoodsList(){

        $parameter = $this->request->param('goodids');

        $visitwhere[] = ['id','in',$parameter];

        $visitsort = ['sort'=>'desc','id'=>'desc'];

        $visitList = Db('Goods')->where($visitwhere)->order($visitsort)->select();

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

        $goodslist = Db('Goods')->where($golimap)->order($golisort)->limit($page,10)->select();

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



	 public function home(){

       		return $this->fetch();

   	 }

 	public function homePage(){

       		return $this->fetch();

   	 }
         public function home_page2(){

       		return $this->fetch();

   	 }
	public function shophome(){

       		return $this->fetch();

   	 }








}

