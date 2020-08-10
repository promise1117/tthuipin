<?php
namespace app\backend\controller;
use app\backend\controller\Base;
class Product extends Base
{
    public function __construct(){
        parent::__construct();
        $this->goods = Db('Goods');
        $this->pot_goods = Db('pot_goods');
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

        empty($parameter['shape_id']) ? $parameter['shape_id'] :$map[] = ['g.shapeid','eq',$parameter['shape_id']];
        empty($parameter['capacityid']) ? $parameter['capacityid'] :$map[] = ['g.capacityid','eq',$parameter['capacityid']];
        empty($parameter['pid']) ? $parameter['pid'] :$map[] = ['g.pid','eq',$parameter['pid']];
        empty($parameter['goods_no'])    ? $parameter['goods_no']    :$map[] = ['g.goods_no','eq',$parameter['goods_no']];
        empty($parameter['name'])        ? $parameter['name']        :$map[] = ['g.name','like','%'.$parameter['name'].'%'];
        empty($parameter['is_del'])      ? $parameter['is_del']      :$map[] = ['g.is_del','eq',$parameter['is_del']];

        if(empty($parameter['price'])){
            $parameter['price'];
        }elseif($parameter['price']==1){
            $map[] = [['g.sell_price','>=',0],['g.sell_price','<',500]];
        }elseif($parameter['price']==2){
            $map[] = [['g.sell_price','>=',500],['g.sell_price','<',1000]];
        }elseif($parameter['price']==3){
            $map[] = [['g.sell_price','>=',1000],['g.sell_price','<',2000]];
        }elseif($parameter['price']==4){
            $map[] = [['g.sell_price','>=',2000],['g.sell_price','<',3000]];
        }elseif($parameter['price']==5){
            $map[] = [['g.sell_price','>=',3000],['g.sell_price','<',5000]];
        }elseif($parameter['price']==6){
            $map[] = [['g.sell_price','>=',5000],['g.sell_price','<',8000]];
        }elseif($parameter['price']==7){
            $map[] = [['g.sell_price','>=',8000],['g.sell_price','<',10000]];
        }elseif($parameter['price']==8){
            $map[] = [['g.sell_price','>=',10000],['g.sell_price','<',20000]];
        }elseif($parameter['price']==9){
            $map[] = ['g.sell_price','>=',20000];
        }
        empty($parameter['listrow'])     ? $parameter['listrow'] = '20' : $parameter['listrow'];
        $map[] = ['g.is_del','neq','1'];
        $sort = ['g.id'=>'desc'];
        $userInfo = $this->checkTokenSession;

//        dump($userInfo['role_id']);
        /**
         * 缓解下...
         *
         * 忽闻水上琵琶声
         * 主人忘归客不发
         * 寻声暗问弹者谁
         * 琵琶声停欲语迟
         * 移船相近邀相见
         * 添酒回灯重开宴
         * 千呼万唤始出来
         * 犹抱琵琶半遮面
         * 转轴拨弦三两声
         * 未成曲调先有情
         * 弦弦掩抑声声思
         * 似诉平生不得志
         * 低眉信手续续弹
         * 说尽心中无限事
         * 轻拢慢捻抹复挑
         * 初为霓裳后六幺
         *
         * 大弦嘈嘈如急雨
         * 小弦切切如私语
         * 嘈嘈切切错杂弹
         * 大珠小珠落玉盘
         *
         * 间关莺语花底滑
         * 幽咽泉流冰下难
         * 冰泉冷涩弦凝绝
         * 凝绝不通声暂歇
         * 别有幽愁暗恨生
         * 此时无声胜有声
         * 银瓶乍破水浆迸
         * 铁骑突出刀枪鸣
         * 曲终收拨当心画
         * 四弦一声如裂帛
         * 东船西舫悄无言
         * 唯见江心秋月白
         */
        //主管权限执行
        if($userInfo['pid']==0){
            $userli[] = ['hidden','eq','0'];
            $userli[] = ['pid','eq','0'];
            $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
            $pUser = Db('AdminUser')
                ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
                ->alias('u')
                ->where($userli)
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


            $getlist = $this->pot_goods
                ->alias('g')
                ->field('g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.categoryid,u.user_id,u.name uername,c.cat_id,c.cat_name,c_capacity.cat_name capacity_name,c_people.cat_name people_name')
                ->join('AdminUser u','g.userid=u.user_id','left')
                ->join('pot_category c','g.categoryid=c.cat_id','left')
                ->join('pot_category c_capacity','g.capacityid=c_capacity.cat_id','left')
                ->join('pot_category c_people','g.pid=c_people.cat_id','left')
                ->where($map)
                ->order($sort)
                ->paginate($parameter['listrow'],false,['query'=>request()->param()]);
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
                $getlist = $this->pot_goods
                    ->alias('g')
                    ->field('g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.categoryid,u.user_id,u.name uername,c.cat_id,c.cat_name,c_capacity.cat_name capacity_name,c_people.cat_name people_name')
                    ->join('AdminUser u','g.userid=u.user_id','left')
                    ->join('pot_category c','g.categoryid=c.cat_id','left')
                    ->join('pot_category c_capacity','g.capacityid=c_capacity.cat_id','left')
                    ->join('pot_category c_people','g.pid=c_people.cat_id','left')
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
                $getlist = $this->pot_goods
                    ->alias('g')
                    ->field('g.id,g.buynumber,g.name,g.goods_no,g.sell_price,g.market_price,g.cost_price,g.create_time,g.img,g.is_del,g.visit,g.store_nums,g.sort,g.sale,g.url,g.index,g.categoryid,u.user_id,u.name uername,c.cat_id,c.cat_name,c_capacity.cat_name capacity_name,c_people.cat_name people_name')
                    ->join('AdminUser u','g.userid=u.user_id','left')
                    ->join('pot_category c','g.categoryid=c.cat_id','left')
                    ->join('pot_category c_capacity','g.capacityid=c_capacity.cat_id','left')
                    ->join('pot_category c_people','g.pid=c_people.cat_id','left')
                    ->where($map)
                    ->order($sort)
                    ->paginate($parameter['listrow'],false,['query'=>request()->param()]);
            }

        }
        //分类下拉
        $catli[] = ['c.is_show','eq','1'];
        $catli[] = ['c.parent_id','eq','0'];
        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'desc'];
        $pCategoryList = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where($catli)
            ->order($sort)
            ->select();

        $cCategoryList = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where('c.is_show','1')
            ->order($sort)
            ->select();



        // 是否属于大师

        $getlist_people = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_people','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_people = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_people','eq','1']])
            ->select();


        // 是否属于容量

        $getlist_capacity = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_capacity','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_capacity = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_capacity','eq','1']])
            ->select();


        // 是否属于容量

        $getlist_shape = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_shape','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_shape = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_shape','eq','1']])
            ->select();
//        dump($cCategoryList);
//        dump($getlist);


        $map1[] = ['c.is_show','eq','1'];

        $map1[] = ['c.parent_id','eq','0'];

        $sort1 = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];

        $getlist1 = Db('pot_category')

            ->alias('c')

            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')

            ->join('pot_category pc','c.parent_id=pc.cat_id','left')

            ->where($map1)

            ->order($sort1)

            ->paginate($parameter['listrow']);

        $egetlist1 = Db('pot_category')

            ->alias('c')

            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')

            ->join('pot_category pc','c.parent_id=pc.cat_id','left')

            ->where('c.is_show','1')

            ->order($sort1)

            ->select();

//        dump($getlist);
        $this->assign([

            // 大师分类
            'categorylsit_people' => $getlist_people,
            'elist_people' => $egetlist_people,
            // 容量分类
            'categorylsit_capacity' => $getlist_capacity,
            'elist_capacity' => $egetlist_capacity,
            // 器型分类
            'categorylsit_shape' => $getlist_shape,
            'elist_shape' => $egetlist_shape,
            'goodslsit' => $getlist,
            'pcatlsit' => $pCategoryList,
            'categorylsit' => $getlist1,
            'elist' => $egetlist1,
            'ccatlsit' => $cCategoryList,
            'puser' => $pUser,
            'cuser' => $cUser,
            'userInfo'=>$userInfo
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

                'categoryid'      => $parameter['shapeid'],#所属分类
                'pid'      => $parameter['pid'],#所属分类
                'shapeid'      => $parameter['shapeid'],#所属分类
                'capacityid'      => $parameter['capacityid'],#所属分类
                'themeid'      => $parameter['themeid'],#所属分类
                'mudid'      => $parameter['mudid'],#所属分类
                'tealeafid'      => $parameter['tealeafid'],#所属分类
                'ambitusid'      => $parameter['ambitusid'],#所属分类
                'iron_kettleid'      => $parameter['iron_kettleid'],#所属分类
                'silver_kettleid'      => $parameter['silver_kettleid'],#所属分类
                'chinawareid'      => $parameter['chinawareid'],#所属分类
                'userid'          => $userInfo['user_id'],#用户的id
            ];

            $info = $this->pot_goods->insert($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $map[] = ['c.parent_id','eq','0'];
        $map[] = ['c.is_show','eq','1'];

        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];
        $getlist = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where($map)
            ->order($sort)
            ->select();
        array_shift($map);
        $egetlist = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where($map)
            ->select();

        // 是否属于大师

        $getlist_people = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_people','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_people = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_people','eq','1']])
            ->select();


        // 是否属于容量

        $getlist_capacity = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_capacity','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_capacity = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_capacity','eq','1']])
            ->select();


        // 是否属于容量

        $getlist_shape = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_shape','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_shape = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_shape','eq','1']])
            ->select();

        // 是否属于主题

        $getlist_theme = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_theme','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_theme = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_theme','eq','1']])
            ->select();



        // 是否属于茶叶

        $getlist_tealeaf = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_tealeaf','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_tealeaf = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_tealeaf','eq','1']])
            ->select();

        // 是否属于周边

        $getlist_ambitus = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_ambitus','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_ambitus = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_ambitus','eq','1']])
            ->select();



        // 是否属于铁壶

        $getlist_iron_kettle = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_iron_kettle','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_iron_kettle = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_iron_kettle','eq','1']])
            ->select();
       


        // 是否属于银壶

        $getlist_silver_kettle = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_silver_kettle','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_silver_kettle = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_silver_kettle','eq','1']])
            ->select();


        // 是否属于泥料

        $getlist_mud = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_mud','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_mud = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_mud','eq','1']])
            ->select();

        // 是否属于泥料

        $getlist_chinaware = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_chinaware','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_chinaware = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_chinaware','eq','1']])
            ->select();



        $this->assign([
            // 所有的分类
            'categorylsit' => $getlist,
            'elist' => $egetlist,
            // 大师分类
            'categorylsit_people' => $getlist_people,
            'elist_people' => $egetlist_people,
            // 容量分类
            'categorylsit_capacity' => $getlist_capacity,
            'elist_capacity' => $egetlist_capacity,
            // 器型分类
            'categorylsit_shape' => $getlist_shape,
            'elist_shape' => $egetlist_shape,

            // 主题分类
            'categorylsit_theme' => $getlist_theme,
            'elist_theme' => $egetlist_theme,

             // 茶叶分类
            'categorylsit_tealeaf' => $getlist_tealeaf,
            'elist_tealeaf' => $egetlist_tealeaf,

            // 泥料分类
            'categorylsit_mud' => $getlist_mud,
            'elist_mud' => $egetlist_mud,

            // 周边分类
            'categorylsit_ambitus' => $getlist_ambitus,
            'elist_ambitus' => $egetlist_ambitus,

            // 铁壶分类
            'categorylsit_iron_kettle' => $getlist_iron_kettle,
            'elist_iron_kettle' => $egetlist_iron_kettle,

            // 银壶分类
            'categorylsit_silver_kettle' => $getlist_silver_kettle,
            'elist_silver_kettle' => $egetlist_silver_kettle,

            // 银壶分类
            'categorylsit_chinaware' => $getlist_chinaware,
            'elist_chinaware' => $egetlist_chinaware,

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
                'index'           => $parameter['index'],#推首页
                'new'           => $parameter['new'],#新品
                'special'           => $parameter['special'],#特賣
                'hot'           => $parameter['hot'],#熱銷
                'is_del'          => $parameter['is_del'],#商品状态 0正常 1已删除 2下架 3申请上架
//                'categoryid'      => $parameter['categoryid'],#所属分类

                'categoryid'      => $parameter['shapeid'],#所属分类
                'pid'      => $parameter['pid'],#所属分类
                'shapeid'      => $parameter['shapeid'],#所属分类
                'capacityid'      => $parameter['capacityid'],#所属分类
                'themeid'      => $parameter['themeid'],#所属分类
                'mudid'      => $parameter['mudid'],#所属分类
                'tealeafid'      => $parameter['tealeafid'],#所属分类
                'ambitusid'      => $parameter['ambitusid'],#所属分类
                'iron_kettleid'      => $parameter['iron_kettleid'],#所属分类
                'silver_kettleid'      => $parameter['silver_kettleid'],#所属分类
                'chinawareid'      => $parameter['chinawareid'],#所属分类
                'userid'          => $parameter['userid'],#用户的id
                'updatetime'      => time(),#修改时间
            ];



//            returnResponse('200','true',$data);
            $info = $this->pot_goods->where(['id'=>$parameter['id'],'goods_no'=>$parameter['goods_no']])->update($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $map[] = ['c.parent_id','eq','0'];
        $map[] = ['c.is_show','eq','1'];
        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];
        $getlist = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where($map)
            ->order($sort)
            ->select();
        array_shift($map);
        $egetlist = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where($map)
            ->select();
        $info = $this->pot_goods->where(['id'=>$parameter['eid']])->find();
        $info['bimg'] = json_decode($info['ad_img']);
        $info['iimg'] = json_decode($info['iamge_info']);

        // if($info['userid'] != $userInfo['user_id']){
        //     exit('您无权修改本条商品信息');
        // }
//        print_r($info);



        // 是否属于大师

        $getlist_people = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_people','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_people = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_people','eq','1']])
            ->select();


        // 是否属于容量

        $getlist_capacity = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_capacity','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_capacity = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_capacity','eq','1']])
            ->select();


        // 是否属于容量

        $getlist_shape = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_shape','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_shape = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_shape','eq','1']])
            ->select();

        // 是否属于主题

        $getlist_theme = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_theme','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_theme = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_theme','eq','1']])
            ->select();



        // 是否属于茶叶

        $getlist_tealeaf = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_tealeaf','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_tealeaf = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_tealeaf','eq','1']])
            ->select();

        // 是否属于周边

        $getlist_ambitus = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_ambitus','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_ambitus = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_ambitus','eq','1']])
            ->select();



        // 是否属于铁壶

        $getlist_iron_kettle = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_iron_kettle','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_iron_kettle = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_iron_kettle','eq','1']])
            ->select();



        // 是否属于银壶

        $getlist_silver_kettle = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_silver_kettle','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_silver_kettle = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_silver_kettle','eq','1']])
            ->select();


        // 是否属于泥料

        $getlist_mud = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_mud','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_mud = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_mud','eq','1']])
            ->select();


        // 是否属于瓷器

        $getlist_chinaware = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->where([['c.parent_id','eq','0'],['c.is_show','eq','1'],['c.is_chinaware','eq','1']])
            ->order($sort)
            ->select();

        $egetlist_chinaware = Db('pot_category')
            ->alias('c')
            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')
            ->join('pot_category pc','c.parent_id=pc.cat_id','left')
            ->order($sort)
            ->where([['c.is_show','eq','1'],['c.is_chinaware','eq','1']])
            ->select();
        $this->assign([

            // 大师分类
            'categorylsit_people' => $getlist_people,
            'elist_people' => $egetlist_people,
            // 容量分类
            'categorylsit_capacity' => $getlist_capacity,
            'elist_capacity' => $egetlist_capacity,
            // 器型分类
            'categorylsit_shape' => $getlist_shape,
            'elist_shape' => $egetlist_shape,

            // 主题分类
            'categorylsit_theme' => $getlist_theme,
            'elist_theme' => $egetlist_theme,

            // 茶叶分类
            'categorylsit_tealeaf' => $getlist_tealeaf,
            'elist_tealeaf' => $egetlist_tealeaf,

            // 泥料分类
            'categorylsit_mud' => $getlist_mud,
            'elist_mud' => $egetlist_mud,

            // 周边分类
            'categorylsit_ambitus' => $getlist_ambitus,
            'elist_ambitus' => $egetlist_ambitus,

            // 铁壶分类
            'categorylsit_iron_kettle' => $getlist_iron_kettle,
            'elist_iron_kettle' => $egetlist_iron_kettle,

            // 银壶分类
            'categorylsit_silver_kettle' => $getlist_silver_kettle,
            'elist_silver_kettle' => $egetlist_silver_kettle,

            // 银壶分类
            'categorylsit_chinaware' => $getlist_chinaware,
            'elist_chinaware' => $egetlist_chinaware,

        ]);
        
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
        $account = Db('pot_goods')->field('is_del')->where('id',$id)->find();
        if($account['is_del'] == 0){
            $info = Db('pot_goods')->where('id',$id)->update(['is_del'=>2]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('pot_goods')->where('id',$id)->update(['is_del'=>0]);
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
            $info = Db('pot_goods')->where('id',$id)->update(['is_del'=>'1']);
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
        $data = Db('goodsinfo')->where('is_templete',1)->select();
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
                $res = Db('pot_goods')->where('id',$id)->update(['sell_price'=>$price]);
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
                $res = Db('pot_goods')->where('id',$id)->update(['cost_price'=>$price]);
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
}
