<?php
namespace app\backend\controller;
use app\backend\controller\Base;
class GoodsMypage extends Base
{
    public function __construct(){
        parent::__construct();
        $this->goods = Db('Goods');
//        $this->check = Validate('Category');
        $this->checkTokenSession = $this->getUserInfoSession();
    }


    /**
     * @author:xiaohao
     * @time:Times
     * @return mixed
     * @description:商品列表
     */
    public function getList(){
        $parameter = input();

        //搜索条件
        empty($parameter['starttime']) ? $parameter['starttime'] = '' :'';
        empty($parameter['endtime'])   ? $parameter['endtime'] = strtotime("+2hours") :'';
        empty($parameter['userid'])      ? $parameter['userid']      :$map[] = ['g.userid','eq',$parameter['userid']]; //位置不能动，做权限删除

        $map[] = ['g.create_time','between',[$parameter['starttime'],$parameter['endtime']]];//时间段查询
        if(empty($parameter['category_id'])){
            $parameter['category_id'];
        }else{
            $pcatList = Db('category')->where('cat_id','eq',$parameter['category_id'])->find();
            if($pcatList['parent_id'] == 0){
                $inWhere = array();
                $pcat = Db('category')->where('parent_id','eq',$parameter['category_id'])->select();
                foreach($pcat as $v){
                    array_push($inWhere,$v['cat_id']);
                }
                $map[] = ['g.categoryid','in',$inWhere];
            }else{
                $map[] = ['g.categoryid','eq',$parameter['category_id']];
            }

        }

        empty($parameter['goods_no'])    ? $parameter['goods_no']    :$map[] = ['g.goods_no','eq',$parameter['goods_no']];
        empty($parameter['name'])        ? $parameter['name']        :$map[] = ['g.name','like','%'.$parameter['name'].'%'];
        empty($parameter['is_del'])      ? $parameter['is_del']      :$map[] = ['g.is_del','eq',$parameter['is_del']];
        empty($parameter['special'])      ? $parameter['special']      :$map[] = ['g.special','eq',$parameter['special']];
        empty($parameter['hot'])      ? $parameter['hot']      :$map[] = ['g.hot','eq',$parameter['hot']];
        empty($parameter['index'])      ? $parameter['index']      :$map[] = ['g.index','eq',$parameter['index']];
        empty($parameter['listrow'])     ? $parameter['listrow'] = '20' : $parameter['listrow'];
        $map[] = ['g.is_del','neq','1'];
//        $map[] = ['g.is_del','neq','2'];
        $sort = ['g.promote_time'=>'desc'];
        $userInfo = $this->checkTokenSession;


        //主管权限执行
        if($userInfo['pid']==0){
            $userli[] = ['hidden','eq','0'];
            $userli[] = ['pid','eq','0'];
            $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
            $pUser = Db('AdminUser')
                ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
                ->alias('u')
                ->where($userli)
                ->where('u.role_id','eq','15')
                ->order($sorts)
                ->join('AdminRole r','u.role_id=r.id','left')
                ->select();

            $cUser = Db('AdminUser')
                ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
                ->alias('u')
                ->where('hidden','0')
                ->order($sorts)
                ->join('AdminRole s','u.role_id=s.id','left')
                ->select();


            $getlist = $this->goods
                ->alias('g')
                ->field('g.promote_time,g.completion,count(s.taocan) as taocan_num,g.custom_name,g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.special,g.hot,g.new,g.categoryid,g.promote,u.user_id,u.name uername,c.cat_id,c.cat_name')
                ->join('AdminUser u','g.userid=u.user_id','left')
                ->join('Category c','g.categoryid=c.cat_id','left')
                ->join('goodsinfo s','g.id=s.goods_id','left')
                ->where($map)
                ->where('g.promote','like','%'.$userInfo['user_id'].'%')
                ->where('s.taocan','neq',0)
                ->group('s.goods_id')
                ->order($sort)
                ->paginate($parameter['listrow'],false,['query'=>request()->param()]);

        }
//        dump($userInfo['user_id']);die;

        //组长权限执行
        if(!empty($userInfo['pid'])){

//            if($parameter['userid']){
//                $checkPid = Db('AdminUser')->field('user_id,pid')->where(['user_id'=>$parameter['userid']])->find();
//
//
//                if($checkPid['pid'] !== $userInfo['user_id']){
//                    $this->assign([
//                        'error' => '您暂无权限查看'.$checkPid['name'].'成员信息',
//                    ]);
//                    // return $this->fetch();
//                }
//            }

            $one = Db('AdminUser')->field('user_id,pid')->where(['user_id'=>$userInfo['pid']])->find();
            if(empty($one['pid'])){
//                $userli[] = ['hidden','eq','0'];
//                $userli[] = ['pid','eq',$one['user_id']];
//
//                $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
//                $pUser = Db('AdminUser')
//                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
//                    ->alias('u')
//                    ->where($userli)
//                    ->where('u.role_id','neq',19)
//                    ->order($sorts)
//                    ->join('AdminRole r','u.role_id=r.id','left')
//                    ->select();
////                dump($pUser);
//                $cUser = Db('AdminUser')
//                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
//                    ->alias('u')
//                    ->where('hidden','0')
//                    ->order($sorts)
//                    ->join('AdminRole s','u.role_id=s.id','left')
//                    ->select();
////                dump($cUser);
//                $two = Db('AdminUser')->field('user_id')->where(['pid'=>$userInfo['user_id']])->column('user_id');
//
//                array_push($two,$userInfo['user_id']);
////                dump($two);
//                $map[] = ['g.userid','in',$two];
//                $getlist = $this->goods
//                    ->alias('g')
//                    ->field('count(s.taocan) as taocan_num,g.custom_name,g.promote,g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.special,g.hot,g.new,g.categoryid,u.user_id,u.name uername,c.cat_id,c.cat_name')
//                    ->join('AdminUser u','g.userid=u.user_id','left')
//                    ->join('Category c','g.categoryid=c.cat_id','left')
//                    ->join('goodsinfo s','g.id=s.goods_id','left')
//                    ->where($map)
//                    ->where('g.promote','like','%'.$userInfo['user_id'].'%')
//                    ->where('s.taocan','neq',0)
//                    ->group('s.goods_id')
//                    ->order($sort)
//                    ->paginate($parameter['listrow'],false,['query'=>request()->param()]);

                $userli[] = ['hidden','eq','0'];
                $userli[] = ['pid','eq','0'];
                $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
                $pUser = Db('AdminUser')
                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where($userli)
                    ->where('u.role_id','eq','15')
                    ->order($sorts)
                    ->join('AdminRole r','u.role_id=r.id','left')
                    ->select();

                $cUser = Db('AdminUser')
                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where('hidden','0')
                    ->order($sorts)
                    ->join('AdminRole s','u.role_id=s.id','left')
                    ->select();


                $getlist = $this->goods
                    ->alias('g')
                    ->field('g.completion,count(s.taocan) as taocan_num,g.custom_name,g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.special,g.hot,g.new,g.categoryid,g.promote,u.user_id,u.name uername,c.cat_id,c.cat_name')
                    ->join('AdminUser u','g.userid=u.user_id','left')
                    ->join('Category c','g.categoryid=c.cat_id','left')
                    ->join('goodsinfo s','g.id=s.goods_id','left')
                    ->where($map)
                    ->where('g.promote','like','%'.$userInfo['user_id'].'%')
                    ->where('s.taocan','neq',0)
                    ->group('s.goods_id')
                    ->order($sort)
                    ->paginate($parameter['listrow'],false,['query'=>request()->param()]);


            }

//            dump($map);
            //组员使用
            if(!empty($one['pid'])){

//                $mapshift = array_shift($map);
//                $map[] = ['u.user_id','eq',$userInfo['user_id']];

//                $userli[] = ['hidden','eq','0'];
//                $userli[] = ['pid','eq',$one['user_id']];
//
//                $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
//                $pUser = Db('AdminUser')
//                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
//                    ->alias('u')
//                    ->where($userli)
//                    ->where('u.role_id','neq',19)
//                    ->order($sorts)
//                    ->join('AdminRole r','u.role_id=r.id','left')
//                    ->select();
////                dump($pUser);
//                $cUser = Db('AdminUser')
//                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
//                    ->alias('u')
//                    ->where('hidden','0')
//                    ->order($sorts)
//                    ->join('AdminRole s','u.role_id=s.id','left')
//                    ->select();
////                dump($cUser);
//                $two = Db('AdminUser')->field('user_id')->where(['pid'=>$userInfo['pid']])->column('user_id');
//
//                array_push($two,$userInfo['pid']);
////                dump($two);
//                $map[] = ['g.userid','in',$two];
//                $getlist = $this->goods
//                    ->alias('g')
//                    ->field('count(s.taocan) as taocan_num,g.custom_name,g.promote,g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.special,g.hot,g.new,g.categoryid,u.user_id,u.name uername,c.cat_id,c.cat_name')
//                    ->join('AdminUser u','g.userid=u.user_id','left')
//                    ->join('Category c','g.categoryid=c.cat_id','left')
//                    ->join('goodsinfo s','g.id=s.goods_id','left')
//                    ->where($map)
//                    ->where('g.promote','like','%'.$userInfo['user_id'].'%')
//                    ->where('s.taocan','neq',0)
//                    ->group('s.goods_id')
//                    ->order($sort)
//                    ->paginate($parameter['listrow'],false,['query'=>request()->param()]);

                $userli[] = ['hidden','eq','0'];
                $userli[] = ['pid','eq','0'];
                $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
                $pUser = Db('AdminUser')
                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where($userli)
                    ->where('u.role_id','neq',19)
                    ->order($sorts)
                    ->join('AdminRole r','u.role_id=r.id','left')
                    ->select();

                $cUser = Db('AdminUser')
                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where('hidden','0')
                    ->order($sorts)
                    ->join('AdminRole s','u.role_id=s.id','left')
                    ->select();


                $getlist = $this->goods
                    ->alias('g')
                    ->field('g.completion,count(s.taocan) as taocan_num,g.custom_name,g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.special,g.hot,g.new,g.categoryid,g.promote,u.user_id,u.name uername,c.cat_id,c.cat_name')
                    ->join('AdminUser u','g.userid=u.user_id','left')
                    ->join('Category c','g.categoryid=c.cat_id','left')
                    ->join('goodsinfo s','g.id=s.goods_id','left')
                    ->where($map)
                    ->where('g.promote','like','%'.$userInfo['user_id'].'%')
                    ->where('s.taocan','neq',0)
                    ->group('s.goods_id')
                    ->order($sort)
                    ->paginate($parameter['listrow'],false,['query'=>request()->param()]);
            }

        }
        //分类下拉
        $catli[] = ['c.is_show','eq','1'];
        $catli[] = ['c.parent_id','eq','0'];
        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'desc'];
        $pCategoryList = Db('Category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('Category pc','c.parent_id=pc.cat_id','left')
            ->where($catli)
            ->order($sort)
            ->select();

        $cCategoryList = Db('Category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('Category pc','c.parent_id=pc.cat_id','left')
            ->where('c.is_show','1')
            ->order($sort)
            ->select();
//        dump($getlist);

        $this->assign([
            'username' => $userInfo['name'],
            'user_id' => $userInfo['user_id'],
            'goodslsit' => $getlist,
            'pcatlsit' => $pCategoryList,
            'ccatlsit' => $cCategoryList,
            'puser' => $pUser,
            'cuser' => $cUser,
        ]);
        return $this->fetch();
    }

    public function goodsrecycle(){
        $parameter = input();

        //搜索条件
        empty($parameter['starttime']) ? $parameter['starttime'] = '' :'';
        empty($parameter['endtime'])   ? $parameter['endtime'] = strtotime("+2hours") :'';
        empty($parameter['userid'])      ? $parameter['userid']      :$map[] = ['g.userid','eq',$parameter['userid']]; //位置不能动，做权限删除

        $map[] = ['g.create_time','between',[$parameter['starttime'],$parameter['endtime']]];//时间段查询
        if(empty($parameter['category_id'])){
            $parameter['category_id'];
        }else{
            $pcatList = Db('category')->where('cat_id','eq',$parameter['category_id'])->find();
            if($pcatList['parent_id'] == 0){
                $inWhere = array();
                $pcat = Db('category')->where('parent_id','eq',$parameter['category_id'])->select();
                foreach($pcat as $v){
                    array_push($inWhere,$v['cat_id']);
                }
                $map[] = ['g.categoryid','in',$inWhere];
            }else{
                $map[] = ['g.categoryid','eq',$parameter['category_id']];
            }

        }

        empty($parameter['goods_no'])    ? $parameter['goods_no']    :$map[] = ['g.goods_no','eq',$parameter['goods_no']];
        empty($parameter['name'])        ? $parameter['name']        :$map[] = ['g.name','like','%'.$parameter['name'].'%'];
        empty($parameter['is_del'])      ? $parameter['is_del']      :$map[] = ['g.is_del','eq',$parameter['is_del']];
        empty($parameter['special'])      ? $parameter['special']      :$map[] = ['g.special','eq',$parameter['special']];
        empty($parameter['hot'])      ? $parameter['hot']      :$map[] = ['g.hot','eq',$parameter['hot']];
        empty($parameter['index'])      ? $parameter['index']      :$map[] = ['g.index','eq',$parameter['index']];
        empty($parameter['listrow'])     ? $parameter['listrow'] = '20' : $parameter['listrow'];
//        $map[] = ['g.is_del','neq','1'];
        $map[] = ['g.is_del','eq','2'];
        $sort = ['g.id'=>'desc'];
        $userInfo = $this->checkTokenSession;


        //主管权限执行
        if($userInfo['pid']==0){
            $userli[] = ['hidden','eq','0'];
            $userli[] = ['pid','eq','0'];
            $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
            $pUser = Db('AdminUser')
                ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
                ->alias('u')
                ->where($userli)
                ->where('u.role_id','neq',19)
                ->order($sorts)
                ->join('AdminRole r','u.role_id=r.id','left')
                ->select();

            $cUser = Db('AdminUser')
                ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
                ->alias('u')
                ->where('hidden','0')
                ->order($sorts)
                ->join('AdminRole s','u.role_id=s.id','left')
                ->select();


            $getlist = $this->goods
                ->alias('g')
                ->field('g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.special,g.hot,g.new,g.categoryid,u.user_id,u.name uername,c.cat_id,c.cat_name')
                ->join('AdminUser u','g.userid=u.user_id','left')
                ->join('Category c','g.categoryid=c.cat_id','left')
//                ->join('goodsinfo s','g.id=s.goods_id','left')
                ->where($map)
//                ->where([['s.pid','neq','0']])
                ->order($sort)
                ->paginate($parameter['listrow'],false,['query'=>request()->param()]);
//            dump($getlist);die;
        }

        //组长权限执行
        if(!empty($userInfo['pid'])){

//            if($parameter['userid']){
//                $checkPid = Db('AdminUser')->field('user_id,pid')->where(['user_id'=>$parameter['userid']])->find();
//
//
//                if($checkPid['pid'] !== $userInfo['user_id']){
//                    $this->assign([
//                        'error' => '您暂无权限查看'.$checkPid['name'].'成员信息',
//                    ]);
//                    // return $this->fetch();
//                }
//            }

            $one = Db('AdminUser')->field('user_id,pid')->where(['user_id'=>$userInfo['pid']])->find();//查当前用户的上级
            if(empty($one['pid'])){
                $userli[] = ['hidden','eq','0'];
                $userli[] = ['pid','eq',$one['user_id']];

                $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
                $pUser = Db('AdminUser')
                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where($userli)
                    ->where('u.role_id','neq',19)
                    ->order($sorts)
                    ->join('AdminRole r','u.role_id=r.id','left')
                    ->select();
//                dump($pUser);
                $cUser = Db('AdminUser')
                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where('hidden','0')
                    ->order($sorts)
                    ->join('AdminRole s','u.role_id=s.id','left')
                    ->select();
//                dump($cUser);
                $two = Db('AdminUser')->field('user_id')->where(['pid'=>$userInfo['user_id']])->column('user_id');

                array_push($two,$userInfo['user_id']);
//                dump($two);
                $map[] = ['g.userid','in',$two];
                $getlist = $this->goods
                    ->alias('g')
                    ->field('g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.special,g.hot,g.new,g.categoryid,u.user_id,u.name uername,c.cat_id,c.cat_name')
                    ->join('AdminUser u','g.userid=u.user_id','left')
                    ->join('Category c','g.categoryid=c.cat_id','left')
                    ->where($map)
                    ->order($sort)
                    ->paginate($parameter['listrow'],false,['query'=>request()->param()]);


            }

//            dump($map);
            //组员使用
            if(!empty($one['pid'])){

//                $mapshift = array_shift($map);
//                $map[] = ['u.user_id','eq',$userInfo['user_id']];

                $userli[] = ['hidden','eq','0'];
                $userli[] = ['pid','eq',$one['user_id']];

                $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
                $pUser = Db('AdminUser')
                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where($userli)
                    ->where('u.role_id','neq',19)
                    ->order($sorts)
                    ->join('AdminRole r','u.role_id=r.id','left')
                    ->select();
//                dump($pUser);
                $cUser = Db('AdminUser')
                    ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where('hidden','0')
                    ->order($sorts)
                    ->join('AdminRole s','u.role_id=s.id','left')
                    ->select();
//                dump($cUser);
                $two = Db('AdminUser')->field('user_id')->where(['pid'=>$userInfo['pid']])->column('user_id');

                array_push($two,$userInfo['pid']);
//                dump($two);
                $map[] = ['g.userid','in',$two];
                $getlist = $this->goods
                    ->alias('g')
                    ->field('g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.special,g.hot,g.new,g.categoryid,u.user_id,u.name uername,c.cat_id,c.cat_name')
                    ->join('AdminUser u','g.userid=u.user_id','left')
                    ->join('Category c','g.categoryid=c.cat_id','left')
                    ->where($map)
                    ->order($sort)
                    ->paginate($parameter['listrow'],false,['query'=>request()->param()]);
            }

        }
        //分类下拉
        $catli[] = ['c.is_show','eq','1'];
        $catli[] = ['c.parent_id','eq','0'];
        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'desc'];
        $pCategoryList = Db('Category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('Category pc','c.parent_id=pc.cat_id','left')
            ->where($catli)
            ->order($sort)
            ->select();

        $cCategoryList = Db('Category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('Category pc','c.parent_id=pc.cat_id','left')
            ->where('c.is_show','1')
            ->order($sort)
            ->select();
//        dump($cCategoryList);

        $this->assign([

            'goodslsit' => $getlist,
            'pcatlsit' => $pCategoryList,
            'ccatlsit' => $cCategoryList,
            'puser' => $pUser,
            'cuser' => $cUser,
        ]);
        return $this->fetch();
    }
    /**
     * @author:xiaohao
     * @time:2019/10/28 15:44
     * @return mixed
     * @description:添加商品
     */
    public function add(){
        $parameter = input();
        // dump($parameter);
        $number = setNumber();
        // $number = getRandomString(6);
        $number = strtoupper(substr(md5(rand()),1,6));


        $userInfo = $this->checkTokenSession;

//        print_r($img);
        if($parameter){
            $fa = request()->param('filesb/a');
            $fb = request()->param('files/a');
            $ad_img   = json_encode($fa);#banner图
            $img_info = json_encode($fb);#详情图

            $fc = request()->param('nooff/a');
            $twos = array_values($fc);
            $nooff    = json_encode($twos,true);#条件的开关
            $one = str_replace('"','',$nooff);
            $two = str_replace(']','',$one);
            $onoff = str_replace('[','',$two);
            $img = request()->param('filedan');
            $data = [
                'ad_img'          =>$ad_img,
                'iamge_info'      =>$img_info,
                'onoff'           =>$onoff,
                'img'             => $img,#原图
                'name'            => $parameter['name'],   #商品名称
                'like'            => $parameter['like'],   #猜你喜欢
                // 'title'           => $parameter['title'],   #标题
                'keywords'        => $parameter['keywords'],#SEO关键词
                'search_words'    => $parameter['search_words'],#产品搜索词库,逗号分隔
                'content'         => $parameter['content'],#商品描述
                'goods_no'        => $parameter['goods_no'],  #商品的货号
                'sell_price'      => $parameter['sell_price'],#销售价格
                'market_price'    => $parameter['market_price'],#市场价格
//                'cost_price'      => $parameter['cost_price'],#成本价格
                'up_time'         => strtotime($parameter['up_time']),#上架时间 时间戳
                'down_time'       => strtotime($parameter['down_time']),#下架时间 时间戳
                'create_time'     => time(), #创建时间
//            'description'     => $parameter['description'],#SEO描述
//            'weight'          => $parameter['weight'],#重量
//            'unit'            => $parameter['unit'],#计件单位。如:件,箱,个
//            'brand_id'        => $parameter['brand_id'],#品牌ID
//            'spec_array'      => $parameter['spec_array'],#商品信息json数据
//            'is_delivery_fee' => $parameter['is_delivery_fee'],#免运费 0收运费 1免运费
                'store_nums'      => $parameter['store_nums'],#库存
                'visit'           => $parameter['visit'],#浏览次数
                'sale'            => $parameter['sale'],#销量
                'sort'            => $parameter['sort'],#排序
                'index'           => $parameter['index'],#推首页
                'new'           => $parameter['new'],#新品
                'special'           => $parameter['special'],#特賣
                'hot'           => $parameter['hot'],#熱銷
                'is_del'          => $parameter['is_del'],#商品状态 0正常 1已删除 2下架 3申请上架
                'categoryid'      => $parameter['categoryid'],#所属分类
                'userid'          => $userInfo['user_id'],#用户的id
            ];
            $info = $this->goods->insert($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $map[] = ['c.parent_id','eq','0'];
        $map[] = ['c.is_show','eq','1'];
        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];
        $getlist = Db('Category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('Category pc','c.parent_id=pc.cat_id','left')
            ->where($map)
            ->order($sort)
            ->select();
        array_shift($map);
        $egetlist = Db('Category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('Category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where($map)
            ->select();
        $this->assign([
            'categorylsit' => $getlist,
            'elist' => $egetlist,
            'number' => $number,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 15:43
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:修改
     */
    public function edit(){
        $parameter = input();
        $userInfo = $this->checkTokenSession;

        if($parameter['id']){
            $fa = request()->param('filesb/a');
            $fb = request()->param('files/a');
            $fc = request()->param('nooff/a');
            $ad_img   = json_encode($fa);#banner图
            $img_info = json_encode($fb);#详情图
            $twos = array_values($fc);
            $nooff    = json_encode($twos,true);#条件的开关
            $one = str_replace('"','',$nooff);
            $two = str_replace(']','',$one);
            $onoff = str_replace('[','',$two);
            $img = request()->param('filedan');
            $data = [
                'ad_img'          => $ad_img,
                'iamge_info'      => $img_info,
                'onoff'           => $onoff,
                'img'             => $img,#原图
                'name'            => $parameter['name'],   #商品名称
                'like'            => $parameter['like'],   #猜你喜欢
                // 'title'           => $parameter['title'],   #标题
                'keywords'        => $parameter['keywords'], #标签关键词
                'search_words'    => $parameter['search_words'],#产品搜索词库,逗号分隔
                'content'         => $parameter['content'],#商品描述
                'sell_price'      => $parameter['sell_price'],#销售价格
                'market_price'    => $parameter['market_price'],#市场价格
//                'cost_price'      => $parameter['cost_price'],#成本价格
                'up_time'         => strtotime($parameter['up_time']),#上架时间 时间戳
                'down_time'       => strtotime($parameter['down_time']),#下架时间 时间戳
                'store_nums'      => $parameter['store_nums'],#库存
                'visit'           => $parameter['visit'],#浏览次数
                'sale'            => $parameter['sale'],#销量
                'sort'            => $parameter['sort'],#排序
//                'index'           => $parameter['index'],#推首页
                'new'           => $parameter['new'],#新品
//                'special'           => $parameter['special'],#特賣
//                'hot'           => $parameter['hot'],#熱銷
//                'is_del'          => $parameter['is_del'],#商品状态 0正常 1已删除 2下架 3申请上架
                'categoryid'      => $parameter['categoryid'],#所属分类
                'userid'          => $parameter['userid'],#用户的id
                'updatetime'      => time(),#修改时间
            ];
//            print_r($data);die;
            $info = $this->goods->where(['id'=>$parameter['id'],'goods_no'=>$parameter['goods_no']])->update($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $map[] = ['c.parent_id','eq','0'];
        $map[] = ['c.is_show','eq','1'];
        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];
        $getlist = Db('Category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('Category pc','c.parent_id=pc.cat_id','left')
            ->where($map)
            ->order($sort)
            ->select();
        array_shift($map);
        $egetlist = Db('Category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('Category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where($map)
            ->select();
        $info = $this->goods->where(['id'=>$parameter['eid']])->find();
        $info['bimg'] = json_decode($info['ad_img']);
        $info['iimg'] = json_decode($info['iamge_info']);

        // if($info['userid'] != $userInfo['user_id']){
        //     exit('您无权修改本条商品信息');
        // }
//        print_r($info);
        $this->assign([
            'categorylsit' => $getlist,
            'elist' => $egetlist,
            'info' => $info,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 15:52
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:显示隐藏
     */
    public function show(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        $account = Db('Goods')->field('is_del')->where('id',$id)->find();
        if($account['is_del'] == 0){
            $info = Db('Goods')->where('id',$id)->update(['is_del'=>2]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('Goods')->where('id',$id)->update(['is_del'=>0]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
    }

    public function changeSpecial(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        $account = Db('Goods')->field('special')->where('id',$id)->find();
        if($account['special'] == 0){
            $info = Db('Goods')->where('id',$id)->update(['special'=>1]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('Goods')->where('id',$id)->update(['special'=>0]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
    }

    public function changeHot(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        $account = Db('Goods')->field('hot')->where('id',$id)->find();
        if($account['hot'] == 0){
            $info = Db('Goods')->where('id',$id)->update(['hot'=>1]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('Goods')->where('id',$id)->update(['hot'=>0]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
    }

    public function changeIndex(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        $account = Db('Goods')->field('index')->where('id',$id)->find();
        if($account['index'] == 0){
            $info = Db('Goods')->where('id',$id)->update(['index'=>1]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('Goods')->where('id',$id)->update(['index'=>0]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 16:52
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:商品删除
     */
    public function deleteGoods(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        if($id){
            $info = Db('Goods')->where('id',$id)->update(['is_del'=>'1']);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
        returnResponse(100,'网路拥挤，稍后再试');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/27 16:23
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:多选删除
     */
    public function delGoodsMany(){
        $parameter = input();
        $ids = $parameter['ids'];
        foreach($ids as $v){
            $info = Db('Goods')->where('goods_no',$v)->update(['is_del'=>'1']);
        }
        if($info){
            returnResponse(200,'true',$info);
        }
        returnResponse(100,'false');

    }

    public function changePrice(){
        $parameter = input();
        $ids = $parameter['ids'];
        $sliderValue = $parameter['sliderValue'];
        $type = $parameter['type'];
        if($type == 0){
            foreach($ids as $v){
                $temp = Db('Goods')->where('goods_no',$v)->find();

                $goods_id = $temp['id'];

                $goodsinfo = Db('Goodsinfo')->where('goods_id',$goods_id)->where('pid','neq',0)->select();
//                return $goodsinfo;
                foreach ($goodsinfo as $item) {
                    $sell_price = intval($item['sell_price'])*(1+intval(substr($sliderValue,0,strpos($sliderValue,'%')))/100);
                    $sell_price = round($sell_price);
                    $info = Db('Goodsinfo')->where('goods_id',$goods_id)->where('id',$item['id'])->where('pid','neq',0)->update(['sell_price'=>$sell_price]);
                }

            }
        }else{
            foreach($ids as $v){
                $temp = Db('Goods')->where('goods_no',$v)->find();

                $goods_id = $temp['id'];

                $goodsinfo = Db('Goodsinfo')->where('goods_id',$goods_id)->where('pid','neq',0)->select();

                foreach ($goodsinfo as $item) {
                    $sell_price = intval($item['sell_price'])*(1-intval(substr($sliderValue,0,strpos($sliderValue,'%')))/100);
                    $sell_price = round($sell_price);
                    $info = Db('Goodsinfo')->where('goods_id',$goods_id)->where('id',$item['id'])->where('pid','neq',0)->update(['sell_price'=>$sell_price]);
                }

            }
        }

        if($info){
            returnResponse(200,'true',$info);
        }
        returnResponse(100,'false');

    }




    /**
     * @return array
     * @user promise_1117
     * @time 2020/1/14/13:20
     * @description// 模板列表接口
     */
    public function templeteGoodsTable(){
//        ->where([['user_id','eq',null],['is_templete','eq',1]])
        $data = Db('goodsinfo')->where([['user_id','eq',$this->checkTokenSession['user_id']],['is_templete','eq',1]])->select();
        $res = array();
        if($data){
            $res['code'] = 0;
            $res['msg'] = '请求成功';
            $res['count'] = count($data);
            $res['data'] = $data;
        }else{
            $res['code'] = 0;
            $res['msg'] = '暂无数据';
            $res['count'] = count($data);
            $res['data'] = $data;
        }
        return $res;
    }



    /**
     * @return array
     * @user promise_1117
     * @time 2020/1/14/13:21
     * @description// 添加模板接口
     */
    public function addTemplate(){
        $param = input('post.');
        $data = array();
        $values = array();
        if(!empty($param['size']) && !empty($param['color'])){
            $values['goods_id'] = 0;
            $values['image'] = $param['files'];
            $values['size'] = $param['size'];
            $values['name'] = $param['name'];
            $values['addtime'] = time();
            $values['sell_price'] = $param['sell_price'];
            $values['market_price'] = $param['market_price'];
            $values['cost_price'] = $param['cost_price'];
            $values['color'] = $param['color'];
            $values['order'] = $param['order'];
            $values['user_id'] = $this->checkTokenSession['user_id'];
            $values['is_templete'] = 1;

            $res = Db('goodsinfo')->insert($values);
            if($res){
                $data['code'] = 0;
                $data['msg'] = '添加成功';
                $data['res'] = $res;
            }else{
                $data['code'] = 1;
                $data['msg'] = '添加失败';
                $data['res'] = '';
            }
        }else{
            $data['code'] = 2;
            $data['msg'] = '添加失败';
            $data['res'] = '';
        }
        return $data;
    }



    /**
     * @return array
     * @user promise_1117
     * @time 2020/1/14/13:22
     * @description// 编辑模板接口
     */
    public function editTemplate(){

        $param = input('post.');
        $data = array();
        $values = array();
        if(!empty($param['size']) && !empty($param['color'])){
            $values['goods_id'] = $param['goods_id'];
            $values['image'] = $param['files'];
            $values['size'] = $param['size'];
            $values['name'] = $param['name'];
            $values['addtime'] = time();
            $values['sell_price'] = $param['sell_price'];
            $values['market_price'] = $param['market_price'];
            $values['cost_price'] = $param['cost_price'];
            $values['color'] = $param['color'];
            $values['order'] = $param['order'];
            $values['user_id'] = $this->checkTokenSession['user_id'];
            $values['is_templete'] = 1;

            $res = Db('goodsinfo')->where('id',$values['goods_id'])->update($values);
            if($res){
                $data['code'] = 0;
                $data['msg'] = '更新成功';
                $data['res'] = $res;
            }else{
                $data['code'] = 1;
                $data['msg'] = '更新失败';
                $data['res'] = '';
            }
        }else{
            $data['code'] = 2;
            $data['msg'] = '更新失败';
            $data['res'] = '';
        }
        return $data;
    }



    /**
     * @return array
     * @user promise_1117
     * @time 2020/1/14/13:22
     * @description// 删除模板接口
     */
    public function delTemplate(){
        $param = input('post.');
        $data = array();
        if($param){
            $res = Db('goodsinfo')->where('id',$param['goods_id'])->delete();
            if($res){
                $data['code'] = 0;
                $data['msg'] = '删除成功';
                $data['res'] = $res;
            }else{
                $data['code'] = 1;
                $data['msg'] = '删除失败';
                $data['res'] = '';
            }
            return $data;
        }

    }


    /**
     * @return array
     * @user promise_1117
     * @time 2020/1/14/13:22
     * @description// 图片上传接口
     */
    public function uploadImg()
    {
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move('uploads');
        $reubfo = array();
        if ($info) {
            $reubfo['code']= 0;
            $reubfo['savename'] = request()->domain()."/uploads/".$info->getSaveName();
        }else{
            // 上传失败获取错误信息
            $reubfo['code']= 1;
            $reubfo['err'] = $file->getError();
        }
        return $reubfo;

    }

    // 编辑单元格修改价格
    public function editMoney(){
        $id = input('id');
        $price = input('price');
        $type = input('type');
        $data = array();
        if($id && $price && $type){
            if($type == 1){
                $res = Db('goods')->where('id',$id)->update(['sell_price'=>$price]);
                if($res){
                    $data['code'] = 0;
                    $data['msg'] = '修改成功';
                    $data['res'] = $res;
                }else{
                    $data['code'] = 1;
                    $data['msg'] = '修改失败';
                    $data['res'] = '';
                }
                return $data;
            }else{
                $res = Db('goods')->where('id',$id)->update(['cost_price'=>$price]);
                if($res){
                    $data['code'] = 0;
                    $data['msg'] = '修改成功';
                    $data['res'] = $res;
                }else{
                    $data['code'] = 1;
                    $data['msg'] = '修改失败';
                    $data['res'] = '';
                }
                return $data;
            }

        }
    }

    /**
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     *
     * @ promise1117
     *
     * description 推广人员的添加接口
     */
    public function promoteAdd(){
        $user_id = input('user_id');
        $goods_id = input('goods_id');
        $goods = Db('goods')->where('id',$goods_id)->find();
        if(!$goods){
            return [
                'code'=>1,
                'msg'=>'暂无此商品',
                'data'=>array()
            ];
        }else{
            $promote = $goods['promote'];
            if($promote){
                $change_promote = $promote.','.$user_id;
            }else{
                $change_promote = $user_id;
            }
            $res = Db('goods')->where('id',$goods_id)->update(['promote'=>$change_promote]);

            if($res){
                return [
                    'code'=>0,
                    'msg'=>'添加成功',
                    'data'=>$res
                ];
            }else{
                return [
                    'code'=>1,
                    'msg'=>'添加失败',
                    'data'=>array()
                ];
            }

        }
    }



    public function promoteDecrease(){
        $user_id = input('user_id');
        $goods_id = input('goods_id');
        $goods = Db('goods')->where('id',$goods_id)->find();
        if(!$goods){
            return [
                'code'=>1,
                'msg'=>'暂无此商品',
                'data'=>array()
            ];
        }else{
            $promote = $goods['promote'];
            if($promote){
                $temp_arr = explode(',',$promote);
                $key = array_search($user_id,$temp_arr);
                unset($temp_arr[$key]);
                $change_promote = join(',',$temp_arr);

            }else{
                return [
                    'code'=>0,
                    'msg'=>'移除个鸡儿,都没有东西给你移除,傻屌',
                    'data'=>array()
                ];
            }
            $res = Db('goods')->where('id',$goods_id)->update(['promote'=>$change_promote]);

            if($res){
                return [
                    'code'=>0,
                    'msg'=>'移除成功',
                    'data'=>$res
                ];
            }else{
                return [
                    'code'=>1,
                    'msg'=>'移除失败',
                    'data'=>array()
                ];
            }

        }
    }
}
