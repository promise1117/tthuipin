<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\Db;

class Artist extends Base
{
    public function __construct(){
        parent::__construct();
        $this->goods = Db('Goods');
        $this->pot_artist = Db('pot_artist');
        $this->pot_artist = Db('pot_artist');
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

        empty($parameter['listrow'])     ? $parameter['listrow'] = '20' : $parameter['listrow'];
        empty($parameter['cat_name'])     ? $parameter['cat_name']    :$map[] = ['c.cat_name','like',$parameter['cat_name']];
        $map[] = ['g.is_del','neq','1'];
        $sort = ['g.cat_id'=>'desc'];


        $userInfo = $this->checkTokenSession;



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


            $getlist = $this->pot_artist
                ->alias('g')
                ->field('g.*,c.cat_id,c.cat_name,c.image')
                ->join('AdminUser u','g.userid=u.user_id','left')
                ->join('pot_category c','g.cat_id=c.cat_id','left')
                ->where($map)

                ->order($sort)
                ->paginate($parameter['listrow'],false,['query'=>request()->param()]);
//            dump($this->pot_artist->getLastSql());die;

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
//        dump($cCategoryList);




        $cat_name_getlist =
            Db::name('pot_artist')
                ->alias('g')
                ->field('c.cat_name')
                ->join('AdminUser u','g.userid=u.user_id','left')
                ->join('pot_category c','g.cat_id=c.cat_id','left')

                ->order($sort)
                ->select();


        
        $this->assign([
            'cat_name_getlist' => $cat_name_getlist,
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

                'categoryid'      => $parameter['shapeid'],#所属分类
                'pid'      => $parameter['pid'],#所属分类

                'userid'          => $userInfo['user_id'],#用户的id
            ];

            $info = $this->pot_artist->insert($data);
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

            $img = request()->param('filedan');

            $data = [

                'img'             => $img,#原图

                'position'        => $parameter['position'], #标签关键词

                'description'         => $parameter['description'],#商品描述

            ];



//            returnResponse('200','true',$data);
            $info = $this->pot_artist->where(['id'=>$parameter['id']])->update($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
//        $map[] = ['c.parent_id','eq','0'];
//        $map[] = ['c.is_show','eq','1'];
//        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];

        $info = $this->pot_artist->where(['id'=>$parameter['eid']])->find();
//        $info['bimg'] = json_decode($info['ad_img']);
//        $info['iimg'] = json_decode($info['iamge_info']);

        // if($info['userid'] != $userInfo['user_id']){
        //     exit('您无权修改本条商品信息');
        // }
//        print_r($info);




        $this->assign([

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
        $account = Db('pot_artist')->field('is_del')->where('id',$id)->find();
        if($account['is_del'] == 0){
            $info = Db('pot_artist')->where('id',$id)->update(['is_del'=>2]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('pot_artist')->where('id',$id)->update(['is_del'=>0]);
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
            $info = Db('pot_artist')->where('id',$id)->update(['is_del'=>'1']);
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
}
