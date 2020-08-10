<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\Db;

class Goodsinfo extends Base
{
    public function __construct(){
        parent::__construct();
        $this->goodsinfo = Db('Goodsinfo');
        $this->checkTokenSession = $this->getUserInfoSession();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 14:19
     * @return mixed
     * @description:获取列表
     */
    public function getList(){
        $parameter = input();
        empty($parameter['goods_id']) ? $parameter['goods_id'] :$map[] = ['g.goods_id','eq',$parameter['goods_id']];
        $map[] = ['g.pid','neq','0'];
        $sort  = ['g.order'=>'desc','g.id'=>'desc'];

        $getlist = $this->goodsinfo
            ->field('g.id,g.taocan,g.package,g.image,g.color,g.size,g.title,g.name,g.addtime,g.sell_price,g.market_price,g.cost_price,g.status,g.order,gi.pid,gi.title ptitle,gi.taocan ptaocan')
            ->alias('g')
            ->join('Goodsinfo gi','g.pid=gi.id','left')
            ->where($map)
            ->order($sort)
            ->select();
//        dump($getlist);
        $this->assign([
            'goodsinfolsit' => $getlist,
        ]);

        return $this->fetch();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/29 17:39
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:商品添加
     */
    public function add(){

        $userInfo = $this->checkTokenSession;
        $parameter = input();



        $user_id = $userInfo['user_id'];
        $addtime = time();

        if(empty($parameter['gid'])){
            $dataf = [
                'goods_id' => $parameter['goods_id'],//商品id
                'user_id'  => $userInfo['user_id'],//用户id
                'addtime'  => time(), //上传时间
            ];
            $goods_id = $parameter['goods_id'];
            $keyId = $this->goodsinfo->insertGetId($dataf);
        }


        if(!empty($parameter['gid'])){


            if($parameter['pid']==''){
                returnResponse('100','请至少选择一个套餐');
            }
            if(empty($parameter['package'])){
                returnResponse('100','请至少选择一个所属商品');
            }
            $data = array();
            foreach ($parameter['pid'] as $k=>$v){
                foreach ($parameter['package'] as $kk=>$vv){
                    $temp = [
                        'pid'     => $v,//组合
                        // 'name'    => $parameter['name'],//组合
                        // 'title'   => $parameter['title'],//昵称
                        'size'    => $parameter['size'], //尺寸
                        'order'   => $parameter['order'], //排序
                        'image'   => $parameter['files'],//图片
                        'sell_price'   => $parameter['sell_price'],//出售价
                        'market_price' => $parameter['market_price'],//市场价
                        'cost_price'   => $parameter['cost_price'],//成本价
                        'color'    => $parameter['color'],//颜色
                        'package'    => $vv,//颜色
                        'goods_id'=>$parameter['goods_id'],
                        'user_id'=>$user_id,
                        'addtime'=>$addtime,
                        'attribute_name'=>$parameter['attribute_name'],
                        'sub_attribute_name'=>$parameter['sub_attribute_name'],
                    ];

                    array_push($data,$temp);
                }
            }
//            $data = [
//                'pid'     => $parameter['pid'],//组合
//                // 'name'    => $parameter['name'],//组合
//                // 'title'   => $parameter['title'],//昵称
//                'size'    => $parameter['size'], //尺寸
//                'order'   => $parameter['order'], //排序
//                'image'   => $parameter['files'],//图片
//                'sell_price'   => $parameter['sell_price'],//出售价
//                'market_price' => $parameter['market_price'],//市场价
//                'cost_price'   => $parameter['cost_price'],//成本价
//                'color'    => $parameter['color'],//颜色
//                'package'    => $parameter['package'],//颜色
//            ];
//            $gid = intval($parameter['gid']);
//            return $gid;
//            return $data;
            $info = $this->goodsinfo->insertAll($data);

            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $getlist = $this->goodsinfo
            ->where('pid',0)
            ->where('goods_id',$parameter['goods_id'])
            ->where('taocan','neq','0')
            ->order(['id'=>'desc'])
            ->select();
//        dump($userInfo['user_id']);

        $template = Db('goodsinfo')->where('is_templete',1)->where('user_id','eq',$user_id)->select();

        $this->assign([
            'template'=>$template,
            'keyid' => $keyId,
            'goodslist' => $getlist,
            'goods_id' =>$goods_id
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
//
//http://www.thinkphpshop.cn/uploads/20191031\c62847ab84c0abd84616d12526d4a773.jpg
    public function edit(){
        $userInfo = $this->checkTokenSession;
        $parameter = input();

        $user_id = $userInfo['user_id'];
        $info = Db('Goodsinfo')->where('id',$parameter['eid'])->find();

        if(!empty($parameter['id'])){

            $data = [
                'pid'     => $parameter['pid'],//组合
                // 'name'    => $parameter['name'],//组合
                // 'title'   => $parameter['title'],//昵称
                'size'    => $parameter['size'], //尺寸
                'order'   => $parameter['order'], //排序
                'image'   => $parameter['files'],//图片
                'sell_price'   => $parameter['sell_price'],//出售价
                'market_price' => $parameter['market_price'],//市场价
                'cost_price'   => $parameter['cost_price'],//成本价
                'color'    => $parameter['color'],//颜色
                'updatetime' => time(),
                'package'=>$parameter['package'],
                'attribute_name'=>$parameter['attribute_name'],
                'sub_attribute_name'=>$parameter['sub_attribute_name'],
            ];
            $id = intval($parameter['id']);
//            图片删除
//            if($info['image'] !== $data['image']){
//                $str = str_replace('\\','/',$info['image']);
//                $servername = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/';
//                $new_str = str_replace($servername,'',$str);
//                print_r($new_str);die;
//                if(file_exists($new_str)){
//                    unlink($new_str);
//                }
//            }


            $info = $this->goodsinfo->where(['id'=>$id])->update($data);

            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $getlist = $this->goodsinfo
            ->where('pid',0)
            ->where('goods_id',$info['goods_id'])
            ->where('taocan','neq','0')
            ->order(['id'=>'desc'])
            ->select();
//        dump($info);
        // if($userInfo['user_id'] != $info['user_id']){
        //     $this->error('您暂无权限修改该内容','/vgoodslist','','2');
        // }
        $template = Db('goodsinfo')->where('is_templete',1)->where('user_id','eq',$user_id)->select();
        $this->assign([
            'template'=>$template,
            'goodslist' => $getlist,
            'info' => $info,
        ]);

        return $this->fetch();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/31 11:28
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:产品删除
     */
    public function deletGoods(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        if($id){
//            $imgUrl = $this->goodsinfo->where('id',$id)->find();
//            if(file_exists($imgUrl['img_list'])){
//                unlink($imgUrl['img_list']);
//            }
            $info = $this->goodsinfo->where(['id'=>$id])->delete();
            if($info){
                returnResponse(200,'已删除',$info);
            }
            returnResponse(100,'false');
        }
        returnResponse(100,'网路拥挤，稍后再试');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/27 17:40
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:多选删除
     */
    public function delGoodsMany(){
        $parameter = input();
        $ids = $parameter['ids'];
//        $idss = json_encode($ids,true);
//        print_r($idss);
//        foreach($ids as $v){
////            $imgUrl = $this->goodsinfo->where('id',$v)->find();
////            if(file_exists($imgUrl['img_list'])){
////                unlink($imgUrl['img_list']);
////            }
//            $info .= $v."|";
//        }

        $info = $this->goodsinfo->delete($ids);
//        echo $this->goodsinfo->getLastSql();die;
        if($info){
            returnResponse(200,'true',$info);
        }
        returnResponse(100,'false');
    }



    /**
     * @author:xiaohao
     * @time:2019/10/29 16:06
     * @return mixed
     * @description:套餐列表
     */
    public function getListtc(){
        $parameter = input();
        empty($parameter['goods_id']) ? $parameter['goods_id'] :$map[] = ['g.goods_id','eq',$parameter['goods_id']];
        $map[] = ['g.pid','eq','0'];
        $map[] = ['g.taocan','neq','0'];
        $sorts =  ['order'=>'desc','id'=>'desc'];
        $tclist = $this->goodsinfo
            ->alias('g')
            ->field('g.*,i.sell_price one_sell_price,i.market_price one_market_price,i.cost_price one_cost_price')
            ->join('goodsinfo i','g.id=i.pid and i.pid <> 0','left')
            ->distinct(true)
            ->field('i.pid')
            ->where($map)->order($sorts)->select();
//        dump($tclist);
        $this->assign([
            'tclist' => $tclist,
        ]);
        return $this->fetch();
    }
    /**
     * @author:xiaohao
     * @time:2019/10/29 14:31
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:添加套餐
     */
    public function addtc(){
        $userInfo = $this->checkTokenSession;
        $parameter = input();
        if(empty($parameter['gid'])){
            $dataf = [
                'goods_id' => $parameter['goods_id'],//商品id
                'user_id'  => $userInfo['user_id'],//用户id
                'addtime'  => time(), //上传时间
            ];
            $keyId = $this->goodsinfo->insertGetId($dataf);
        }
        if(!empty($parameter['gid'])){
            $fc = request()->param('joinid/a');
            if(!empty($fc)){
                $twos = array_values($fc);
                $nooff    = json_encode($twos,true);#条件的开关
                $one = str_replace('"','',$nooff);
                $two = str_replace(']','',$one);
                $onoff = str_replace('[','',$two);
            }

            $data = [
                'taocan'  => $parameter['taocan'],//套餐标题
                'join'  => $onoff,
                'order'  => $parameter['order'],
            ];
            $gid = intval($parameter['gid']);
            $info = $this->goodsinfo->where(['id'=>$gid])->update($data);
            if($info){
//                cache('goods_id'.$userInfo['user_id'],$gid);
                returnResponse('200','true',$info);
            }
            returnResponse('100','false:套餐tc');
        }
        $joinlist = $this->goodsinfo
            ->where('pid',0)
            ->where('goods_id',$parameter['goods_id'])
            ->where('taocan','neq','0')
            ->order(['id'=>'desc'])
            ->select();
//        echo $this->goodsinfo->getLastSql();
        $this->assign([
            'keyid' => $keyId,
            'jointc' => $joinlist,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/29 16:55
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:套餐列表修改
     */
    public function edittc(){
        $userInfo = $this->checkTokenSession;
        $parameter = input();
        if(!empty($parameter['tid'])){
            $fc = request()->param('joinid/a');
            if($fc){
                $twos = array_values($fc);
                $nooff    = json_encode($twos,true);#条件的开关
                $one = str_replace('"','',$nooff);
                $two = str_replace(']','',$one);
                $onoff = str_replace('[','',$two);
            }else{
                $onoff='';
            }
//            $twos = array_values($fc);
//            $nooff    = json_encode($twos,true);#条件的开关
//            $one = str_replace('"','',$nooff);
//            $two = str_replace(']','',$one);
//            $onoff = str_replace('[','',$two);
            $data = [
                'taocan'  => $parameter['taocan'],//套餐标题
                'join'  => $onoff,
                'order'  => $parameter['order'],
                'updatetime' =>time(),
            ];
            $tid = intval($parameter['tid']);
            $info = $this->goodsinfo->where(['id'=>$tid])->update($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false:套餐tc');
        }
        $tcinfo = $this->goodsinfo
            ->where('id',$parameter['eid'])
            ->find();
        $joinlist = Db('Goodsinfo')
            ->where('pid',0)
            ->where('goods_id',$parameter['goods_id'])
            ->where('taocan','neq','0')
            ->order(['id'=>'desc'])
            ->select();
        $arrayTc = explode(',',$tcinfo['join']);
        $this->assign([
            'info' => $tcinfo,
            'jointc' => $joinlist,
            'ejointc' => $arrayTc,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/29 17:23
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:组合删除
     */
    public function gitcDelete(){
        $parameter = input();
        $id = intval($parameter['id']);
        if($id){
            $number = $this->goodsinfo->field('id,image')->where('pid',$id)->select();
            foreach($number as $k=>$v){
                $url = $v['image'];
                $str = str_replace('\\','/',$url);
                $servername = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/';
                $new_str = str_replace($servername,'',$str);
                if(file_exists($new_str)){
                    unlink($new_str);
                }
                Db('Goodsinfo')->where('id',$v['id'])->delete();
            }
            $info = Db('Goodsinfo')->where('id',$id)->delete();
            if($info){
                returnResponse(200,'成功删除,且对应商品也删除了',$info);
            }
            returnResponse(100,'删除失败');
        }
        returnResponse(100,'参数不存在');
    }

    // 编辑单元格修改价格
    public function editMoney(){
        $id = input('id');
        $price = input('price');
        $type = input('type');
        $data = array();
        if($id && $price && $type){
            if($type == 1){
                $res = Db('goodsinfo')->where('pid',$id)->update(['sell_price'=>$price]);
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
            }else if($type == 2){
                $res = Db('goodsinfo')->where('pid',$id)->update(['market_price'=>$price]);
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
            }else if($type == 3){
                $res = Db('goodsinfo')->where('pid',$id)->update(['cost_price'=>$price]);
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
