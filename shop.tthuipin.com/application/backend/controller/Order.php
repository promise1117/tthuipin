<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\Db;
// 阿里云短信服务
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;



class Order extends Base
{
    public function __construct(){
        parent::__construct();
        $this->order = Db::name('Order');
//        $this->check = Validate('Category');
        $this->checkTokenSession = $this->getUserInfoSession();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 14:19
     * @return mixed
     * @description:获取列表
     */
    public function getList(){
        $userInfo = $this->checkTokenSession;
        $parameter = input();
        empty($parameter['start'])  ? $start = '0' : $start = strtotime($parameter['start']); //开始时间
        empty($parameter['end'])    ? $end   = strtotime("+2hours") :$end = strtotime($parameter['end']); //结束时间

//        empty($parameter['userid']) ? $parameter['userid']     : $userid = $parameter['userid']; //选择管理员 位置不能动，做权限用

        $map[] = ['create_time','between',[$start,$end]];    //时间段查询
        $map[] = ['is_del','eq',0];    //时间段查询
        empty($parameter['order_no']) ? $parameter['order_no'] : $map[] = ['order_no','eq',$parameter['order_no']];//订单号
        empty($parameter['status'])   ? $parameter['status']   : $map[] = ['status','eq',$parameter['status']];//订单状态1：下单成功 2： 厂商配货 3：国际运输:4：抵达台湾:5：订单完成',
        empty($parameter['is_show'])  ? $parameter['is_show']  : $map[] = ['is_show','eq',$parameter['is_show']];//订单是否作废
        empty($parameter['name'])  ? $parameter['name']  : $map[] = ['name|telphone|email|url','like','%'.trim($parameter['name']).'%'];//客户姓名
//        empty($parameter['telphone'])  ? $parameter['telphone']  : $map[] = ['telphone','like','%'.$parameter['telphone'].'%'];//客户姓名
//        empty($parameter['email'])  ? $parameter['email']  : $map[] = ['email','like','%'.$parameter['email'].'%'];//客户姓名
//        empty($parameter['url'])  ? $parameter['url']  : $map[] = ['url','like','%'.$parameter['url'].'%'];//下单地址
        empty($parameter['listrow'])  ? $parameter['listrow'] = '20' : ($parameter['listrow'] > 120 ? $parameter['listrow'] = '120':$parameter['listrow'] = $parameter['listrow']); //分页：20
        empty($parameter['user_id'])  ? $parameter['user_id']  : $map[] = ['order_info','like',"%"."\"userid\":".$parameter['user_id']."%"];
        empty($parameter['goods_no'])  ? $parameter['goods_no']  : $map[] = ['order_info','like',"%".$parameter['goods_no']."%"];


        $sort = ['id'=>'desc'];
        //主管执行权限
//        if(empty($userInfo['pid'])){
            $getlist = $this->order
//                ->field('order_info')
                ->where($map)
//                ->whereOr($map1)
                ->order($sort)
                ->paginate($parameter['listrow'],false,['query' => request()->param()]);
//        }

//        if(!empty($userInfo['pid'])){
//            array_shift($map);
//            $checkPow = Db('AdminUser')->field('pid')->where('id',$userInfo['pid'])->find();
//            if(empty($checkPow['pid'])){
//            //组长的权限
//                $userid[] = ['pid','eq',$userInfo['user_id']];
//                $userids = Db('AdminUser')->where($userid)->column('user_id');
//                array_push($userids,$userInfo['user_id']);
//
//                if(in_array($parameter['user_id'],$userids)){
//                    $map[] = ['d.user_id','in',$parameter['userid']];
//                }
//
//                $groupMap[] = ['user_id','in',$userids];
//            }else{
//            //员工权限
//                //有效用户统计
//                $selfMap[] = ['user_id','in',$userInfo['user_id']];
//            }
//
//
//
//        }
//        dump($getlist);
        //搜索下拉，用户名
        $userMap[] = ['hidden','eq','0'];
        // role_id = 2 组长 role_id=16 组员
        $userMap[] = ['role_id','in','2,16'];
        $userSort = ['user_id'=>'desc'];
        $getUserList = Db('AdminUser')
            ->field('name,user_id,pid,hidden')
            ->alias('u')
            ->where($userMap)
            ->order($userSort)
            ->select();
//        $userMap[] = ['pid','eq','0'];
//        $tgetUserList = Db('AdminUser')
//            ->field('name,user_id,pid,hidden')
//            ->alias('u')
//            ->where($userMap)
//            ->order($userSort)
//            ->select();

        $this->assign([
            'list' => $getlist,
            'userlsit' => $getUserList, //用户列表一级
//            'userlsitt' => $tgetUserList, //用户列表
        ]);

        return $this->fetch();
    }



    public function orderRecycle(){
        $userInfo = $this->checkTokenSession;
        $parameter = input();
        empty($parameter['start'])  ? $start = '0' : $start = strtotime($parameter['start']); //开始时间
        empty($parameter['end'])    ? $end   = strtotime("+2hours") :$end = strtotime($parameter['end']); //结束时间

//        empty($parameter['userid']) ? $parameter['userid']     : $userid = $parameter['userid']; //选择管理员 位置不能动，做权限用

        $map[] = ['create_time','between',[$start,$end]];    //时间段查询
        $map[] = ['is_del','eq',1];    //时间段查询
        empty($parameter['order_no']) ? $parameter['order_no'] : $map[] = ['order_no','eq',$parameter['order_no']];//订单号
        empty($parameter['status'])   ? $parameter['status']   : $map[] = ['status','eq',$parameter['status']];//订单状态1：下单成功 2： 厂商配货 3：国际运输:4：抵达台湾:5：订单完成',
        empty($parameter['is_show'])  ? $parameter['is_show']  : $map[] = ['is_show','eq',$parameter['is_show']];//订单是否作废
        empty($parameter['name'])  ? $parameter['name']  : $map[] = ['name','like','%'.$parameter['name'].'%'];//客户姓名
        empty($parameter['telphone'])  ? $parameter['telphone']  : $map[] = ['telphone','like','%'.$parameter['telphone'].'%'];//客户姓名
        empty($parameter['email'])  ? $parameter['email']  : $map[] = ['email','like','%'.$parameter['email'].'%'];//客户姓名
        empty($parameter['url'])  ? $parameter['url']  : $map[] = ['url','like','%'.$parameter['url'].'%'];//下单地址
        empty($parameter['listrow'])  ? $parameter['listrow'] = '20' : $parameter['listrow']; //分页：20
        empty($parameter['user_id'])  ? $parameter['user_id']  : $map[] = ['order_info','like',"%"."\"userid\":".$parameter['user_id']."%"];
        empty($parameter['goods_no'])  ? $parameter['goods_no']  : $map[] = ['order_info','like',"%".$parameter['goods_no']."%"];

//        dump($map);

        $sort = ['id'=>'desc'];
        //主管执行权限
//        if(empty($userInfo['pid'])){
        $getlist = Db('order')
//                ->field('order_info')
            ->where($map)
            ->order($sort)
            ->paginate($parameter['listrow'],false,['query' => request()->param()]);
//        }

//        if(!empty($userInfo['pid'])){
//            array_shift($map);
//            $checkPow = Db('AdminUser')->field('pid')->where('id',$userInfo['pid'])->find();
//            if(empty($checkPow['pid'])){
//            //组长的权限
//                $userid[] = ['pid','eq',$userInfo['user_id']];
//                $userids = Db('AdminUser')->where($userid)->column('user_id');
//                array_push($userids,$userInfo['user_id']);
//
//                if(in_array($parameter['user_id'],$userids)){
//                    $map[] = ['d.user_id','in',$parameter['userid']];
//                }
//
//                $groupMap[] = ['user_id','in',$userids];
//            }else{
//            //员工权限
//                //有效用户统计
//                $selfMap[] = ['user_id','in',$userInfo['user_id']];
//            }
//
//
//
//        }
//        dump($getlist);
        //搜索下拉，用户名
        $userMap[] = ['hidden','eq','0'];
        // role_id = 2 组长 role_id=16 组员
        $userMap[] = ['role_id','in','2,16'];
        $userSort = ['user_id'=>'desc'];
        $getUserList = Db('AdminUser')
            ->field('name,user_id,pid,hidden')
            ->alias('u')
            ->where($userMap)
            ->order($userSort)
            ->select();
//        $userMap[] = ['pid','eq','0'];
//        $tgetUserList = Db('AdminUser')
//            ->field('name,user_id,pid,hidden')
//            ->alias('u')
//            ->where($userMap)
//            ->order($userSort)
//            ->select();

        $this->assign([
            'list' => $getlist,
            'userlsit' => $getUserList, //用户列表一级
//            'userlsitt' => $tgetUserList, //用户列表
        ]);

        return $this->fetch();
    }


    /**
     * @author:xiaohao
     * @time:2019/11/11 14:04
     * @description:修改订单状态
     */
    public function edit(){

        $userInfo = $this->checkTokenSession;
        $parameter = input();
//        dump($parameter);
        // 发送厂商配货短信提醒
        $telphone = $parameter['telphone'];
     
        $order_id = intval($parameter['eid']);
        $edit_id= intval($parameter['editid']);
        $info_id= intval($parameter['iid']);

//        <!-- 下单成功 配送中 到达代取 签收 拒收 退换货 总计-->
        if(!empty($edit_id)){
            $telphone = $parameter['telphone'];
            $temp_telphone = '886'.$parameter['telphone'];
            $checkPow = Db('AdminRole')->where('id',$userInfo['role_id'])->value('id');
            if($checkPow != 1 && $checkPow != 17 && $checkPow != 15 && $checkPow != 25){
                returnResponse(100,'您暂无权限修改');
            }
            $map[] = ['id','eq',$edit_id];

            switch ($parameter['status']){
                case 1:

                    $info1 = Db('Order')->where($map)->update(['status'=>'1']);

                    if($info1){
//                        distribution($temp_telphone);
                        returnResponse(200,'订单状态已改为“下单成功”',$info1);

                    }elseif ($info1 == 0){
//                        distribution($temp_telphone);
                        returnResponse(200,'订单状态已改为“下单成功”',$info1);
                    }
                    returnResponse(100,'操作失败,稍后再试');
                    break;
                case 2:

                    $info2 = Db('Order')->where($map)->update(['status'=>'2','cargo_time'=>time(),'user_id'=>$userInfo['user_id']]);

                    if($info2){
//                        distribution($temp_telphone);
                        returnResponse(200,'订单状态已改为“配送中”',$info2);

                    }elseif ($info2 == 0){
//                        distribution($temp_telphone);
                        returnResponse(200,'订单状态已改为“配送中”',$info2);
                    }
                    returnResponse(100,'操作配送中失败,稍后再试');
                    break;
                case 3:
                    $info3 = Db('Order')->where($map)->update(['status'=>'3','arrive_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info3){
//                        transport($temp_telphone);
                        returnResponse(200,'订单状态已改为“到达代取”',$info3);
                    }elseif ($info3 == 0){
//                        transport($temp_telphone);
                        returnResponse(200,'订单状态已改为“到达代取”',$info3);
                    }
                    returnResponse(100,'操作到达代取失败,稍后再试');
                    break;
                case 4:
                    $info4 = Db('Order')->where($map)->update(['status'=>'4','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info4){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“签收”',$info4);
                    }elseif ($info4 == 0){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“签收”',$info4);
                    }
                    returnResponse(100,'操作签收失败,稍后再试');
                    break;
                case 5:
                    $info5 = Db('Order')->where($map)->update(['status'=>'5','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info5){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“拒收”',$info5);
                    }elseif ($info5 == 0){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“拒收”',$info5);
                    }
                    returnResponse(100,'操作拒收失败,稍后再试');
                    break;
                case 6:
                    $info6 = Db('Order')->where($map)->update(['status'=>'6','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info6){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“退换货”',$info6);
                    }elseif ($info6 == 0){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“退换货”',$info6);
                    }
                    returnResponse(100,'操作退换货失败,稍后再试');
                    break;
                case 7:
                    $info7 = Db('Order')->where($map)->update(['status'=>'7','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info7){

                        returnResponse(200,'订单状态已改为“清关中”',$info7);
                    }elseif ($info7 == 0){

                        returnResponse(200,'订单状态已改为“清关中”',$info7);
                    }
                    returnResponse(100,'操作清关中失败,稍后再试');
                    break;
                case 8:
                    $info8 = Db('Order')->where($map)->update(['status'=>'8','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info8){
                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“抵达台湾”',$info8);
                    }elseif ($info8 == 0){
                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“抵达台湾”',$info8);
                    }
                    returnResponse(100,'操作抵达台湾失败,稍后再试');
                    break;
                case 9:
                    $info9 = Db('Order')->where($map)->update(['status'=>'9','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info9){

                        returnResponse(200,'订单状态已改为“作废”',$info9);
                    }elseif ($info9 == 0){

                        returnResponse(200,'订单状态已改为“作废”',$info9);
                    }
                    returnResponse(100,'操作作废失败,稍后再试');
                    break;
                default:;
            }
        }


        $status = Db('Order')->where('id',$order_id)->value('status');
        $editid = Db('Order')->where('id',$order_id)->value('id');
        $info   = Db('Order')->where('id',$info_id)->find();

        $this->assign([
            'telphone'=>$telphone,
            'status' => $status,
            'editid' => $editid,
            'info' => $info
        ]);
        return $this->fetch();
    }


    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * 批量修改订单状态接口
     */
    public function editAll(){

        $userInfo = $this->checkTokenSession;
        $parameter = input();

        // 编辑页面数据
        $tels = $parameter['tels'];
        $ids = $parameter['ids'];


//        <!-- 下单成功 配送中 到达代取 签收 拒收 退换货 总计-->
        if(!empty($parameter['ids_update'])){
            // 保存页面数据
            $tels_update = explode(',',$parameter['tels_update']);

            // 权限判断
            $checkPow = Db('AdminRole')->where('id',$userInfo['role_id'])->value('id');
            if($checkPow != 1 && $checkPow != 17 && $checkPow != 15 && $checkPow != 25){
                returnResponse(100,'您暂无权限修改');
            }

            $map[] = ['id','in',$parameter['ids_update']];

            // 批量修改状态
            switch ($parameter['status']){
                case 1:

                    $info1 = Db('Order')->where($map)->update(['status'=>'1']);

                    if($info1){
//                        distribution($temp_telphone);
                        returnResponse(200,'订单状态已改为“下单成功”',$info1);

                    }elseif ($info1 == 0){
//                        distribution($temp_telphone);
                        returnResponse(200,'订单状态已改为“下单成功”',$info1);
                    }
                    returnResponse(100,'操作失败,稍后再试');
                    break;
                case 2:

                    $info2 = Db('Order')->where($map)->update(['status'=>'2','cargo_time'=>time(),'user_id'=>$userInfo['user_id']]);

                    if($info2){
//                        distribution($temp_telphone);
                        returnResponse(200,'订单状态已改为“配送中”',$info2);

                    }elseif ($info2 == 0){
//                        distribution($temp_telphone);
                        returnResponse(200,'订单状态已改为“配送中”',$info2);
                    }
                    returnResponse(100,'操作配送中失败,稍后再试');
                    break;
                case 3:
                    $info3 = Db('Order')->where($map)->update(['status'=>'3','arrive_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info3){
//                        transport($temp_telphone);
                        returnResponse(200,'订单状态已改为“到达代取”',$info3);
                    }elseif ($info3 == 0){
//                        transport($temp_telphone);
                        returnResponse(200,'订单状态已改为“到达代取”',$info3);
                    }
                    returnResponse(100,'操作到达代取失败,稍后再试');
                    break;
                case 4:
                    $info4 = Db('Order')->where($map)->update(['status'=>'4','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info4){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“签收”',$info4);
                    }elseif ($info4 == 0){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“签收”',$info4);
                    }
                    returnResponse(100,'操作签收失败,稍后再试');
                    break;
                case 5:
                    $info5 = Db('Order')->where($map)->update(['status'=>'5','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info5){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“拒收”',$info5);
                    }elseif ($info5 == 0){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“拒收”',$info5);
                    }
                    returnResponse(100,'操作拒收失败,稍后再试');
                    break;
                case 6:
                    $info6 = Db('Order')->where($map)->update(['status'=>'6','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info6){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“退换货”',$info6);
                    }elseif ($info6 == 0){
//                        arrival($temp_telphone);
                        returnResponse(200,'订单状态已改为“退换货”',$info6);
                    }
                    returnResponse(100,'操作退换货失败,稍后再试');
                    break;
                case 7:
                    $info7 = Db('Order')->where($map)->update(['status'=>'7','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info7){

                        returnResponse(200,'订单状态已改为“清关中”',$info7);
                    }elseif ($info7 == 0){

                        returnResponse(200,'订单状态已改为“清关中”',$info7);
                    }
                    returnResponse(100,'操作清关中失败,稍后再试');
                    break;
                case 8:
                    $info8 = Db('Order')->where($map)->update(['status'=>'8','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info8){
                        foreach ($tels_update as $k=>$v){
                            arrival('886'.$v);
                        }
                        returnResponse(200,'订单状态已改为“抵达台湾”',$info8);
                    }elseif ($info8 == 0){
                        foreach ($tels_update as $k=>$v){
                            arrival('886'.$v);
                        }
                        returnResponse(200,'订单状态已改为“抵达台湾”',$info8);
                    }
                    returnResponse(100,'操作抵达台湾失败,稍后再试');
                    break;
                case 9:
                    $info9 = Db('Order')->where($map)->update(['status'=>'9','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info9){

                        returnResponse(200,'订单状态已改为“作废”',$info9);
                    }elseif ($info9 == 0){

                        returnResponse(200,'订单状态已改为“作废”',$info9);
                    }
                    returnResponse(100,'操作作废失败,稍后再试');
                    break;
                default:;
            }

        }

        $this->assign([
            'tels'=>$tels,
            'ids'=>$ids,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/11/15 17:03
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @description:查看商品详情
     */
    public function getOrderInfo(){
        $parameter = input();
        $info_id= intval($parameter['iid']);
        $info   = Db('Order')->where('id',$info_id)->find();
        $this->assign([
            'info' => $info
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/11/11 13:45
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:订单作废
     */
    public function deleteOrder(){
        $userInfo = $this->checkTokenSession;
        $parameter = input();

        $id = intval(trim($parameter['id']));
        $account = Db('Order')->field('is_show')->where('id',$id)->find();

        $checkPow = Db('AdminRole')->where('id',$userInfo['role_id'])->value('id');

        // return $checkPow;
//        var_dump($checkPow);
        if($checkPow != 1){
            returnResponse(100,'您暂无权限修改');
        }
        if($account['is_show'] == 1){
            $info = Db('Order')->where('id',$id)->update(['is_show'=>'0']);
            $status = Db('Order')->field('status')->where('id',$id)->value('status');
            if($info){
                returnResponse(200,'true0',$status);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('Order')->where('id',$id)->update(['is_show'=>'1']);
//            $status = Db('Order')->field('status')->where('id',$id)->value('status');
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
    }

    public function del(){
      $userInfo = $this->checkTokenSession;
      $parameter = input();

      $id = intval(trim($parameter['id']));
      $account = Db('Order')->field('is_show')->where('id',$id)->find();

      $checkPow = Db('AdminRole')->where('id',$userInfo['role_id'])->value('id');

      // return $checkPow;
//        var_dump($checkPow);
      if($checkPow != 1 && $checkPow!=15){
          returnResponse(100,'您暂无权限修改');
      }

      $info = Db('Order')->where('id',$id)->update(['is_del'=>1,'del_time'=>date('Y-m-d H:i:s')]);

      if($info){
        returnResponse(200,'删除成功');
      }else{
        returnResponse(100,'删除失败');
      }
//
    }




    public function distribution($phoneNumber)
    {
        $accessKeyId = 'LTAI4G7Rqy64H3zygVFewC6Y';
        $accessSecret = 'q7KcshSCQejyPsJAxl8QSmjlsfSxd7'; //注意不要有空格
        $signName = '苑发网络科技'; //配置签名
//    $templateCode = 'SMS_188992132';//配置短信模板编号
        $templateCode = 'SMS_191800570';//配置短信模板编号


        AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => 'cn-hangzhou',
                        'PhoneNumbers' => $phoneNumber,//目标手机号
                        'SignName' => $signName,
                        'TemplateCode' => $templateCode,

                    ],
                ])
                ->request();
            $opRes = $result->toArray();
            //print_r($opRes);
            if ($opRes && $opRes['Code'] == "OK"){
                //进行Cookie保存
                return $opRes;
            }
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }


    /**
     * @param string $phoneNumber
     * @return mixed
     * @user promise_1117
     * @time 2020/4/26/15:56
     * @description 阿里云短信国际运输状态修改
     */
    public function transport($phoneNumber = '15172441559')
    {
        $accessKeyId = 'LTAI4G7Rqy64H3zygVFewC6Y';
        $accessSecret = 'q7KcshSCQejyPsJAxl8QSmjlsfSxd7'; //注意不要有空格
        $signName = '苑发网络科技'; //配置签名
//    $templateCode = 'SMS_189026750';//配置短信模板编号
        $templateCode = 'SMS_191815500';//配置短信模板编号


        AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => 'cn-hangzhou',
                        'PhoneNumbers' => $phoneNumber,//目标手机号
                        'SignName' => $signName,
                        'TemplateCode' => $templateCode,

                    ],
                ])
                ->request();
            $opRes = $result->toArray();
            //print_r($opRes);
            if ($opRes && $opRes['Code'] == "OK"){
                //进行Cookie保存
                return $opRes;
            }
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }

    /**
     * @param string $phoneNumber
     * @return mixed
     * @user promise_1117
     * @time 2020/4/26/15:57
     * @description 阿里云短信抵达台湾短信提醒
     */
    public function arrival($phoneNumber = '15172441559')
    {
        $accessKeyId = 'LTAI4G7Rqy64H3zygVFewC6Y';
        $accessSecret = 'q7KcshSCQejyPsJAxl8QSmjlsfSxd7'; //注意不要有空格
        $signName = '苑发网络科技'; //配置签名
//    $templateCode = 'SMS_189016751';//配置短信模板编号
        $templateCode = 'SMS_191830545';//配置短信模板编号


        AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => 'cn-hangzhou',
                        'PhoneNumbers' => $phoneNumber,//目标手机号
                        'SignName' => $signName,
                        'TemplateCode' => $templateCode,

                    ],
                ])
                ->request();
            $opRes = $result->toArray();
            //print_r($opRes);
            if ($opRes && $opRes['Code'] == "OK"){
                //进行Cookie保存
                return $opRes;
            }
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }




    }




    // 编辑单元格修改订单价格
    public function editOrderMoney(){
        $id = input('id');
        $price = input('price');
        $type = input('type');
        $data = array();
        if($id && $price && $type){
            if($type == 1){
                $res = Db('order')->where('id',$id)->update(['payable_amount'=>$price]);
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
