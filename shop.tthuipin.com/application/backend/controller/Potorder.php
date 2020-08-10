<?php
namespace app\backend\controller;
use app\backend\controller\Base;
class Potorder extends Base
{
    public function __construct(){
        parent::__construct();
        $this->pot_order = Db('pot_order');
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

        empty($parameter['userid']) ? $parameter['userid']     : $userid = $parameter['userid']; //选择管理员 位置不能动，做权限用

        $map[] = ['create_time','between',[$start,$end]];    //时间段查询
        empty($parameter['order_no']) ? $parameter['order_no'] : $map[] = ['order_no','eq',$parameter['order_no']];//订单号
        empty($parameter['status'])   ? $parameter['status']   : $map[] = ['status','eq',$parameter['status']];//订单状态1：下单成功 2： 厂商配货 3：国际运输:4：抵达台湾:5：订单完成',
        empty($parameter['is_show'])  ? $parameter['is_show']  : $map[] = ['is_show','eq',$parameter['is_show']];//订单是否作废
        empty($parameter['listrow'])  ? $parameter['listrow'] = '20' : $parameter['listrow']; //分页：默认16条

        $sort = ['id'=>'desc'];
        //主管执行权限
        if($userInfo['role_id'] !== 19){
            $getlist = Db('pot_order')
                ->alias('o')
                ->field('o.*,u.name user_name')
                ->join('admin_user u','o.user_id=u.user_id','left')
    //                ->field('order_info')
                ->where($map)
                ->order($sort)
                ->paginate($parameter['listrow'],false,['query' => request()->param()]);



//        dump($getlist);die;

        }
//        dump($userInfo);
        if($userInfo['role_id'] == 19){


            $getlist = Db('pot_order')
                ->alias('o')
                ->field('o.*,u.name user_name')
                ->join('admin_user u','o.user_id=u.user_id','left')
//                ->field('order_info')
                ->where($map)
                ->where('o.user_id','eq',$userInfo['user_id'])
                ->order($sort)
                ->paginate($parameter['listrow'],false,['query' => request()->param()]);

        }

        if($userInfo['user_id'] == 47){


            $getlist = Db('pot_order')
                ->alias('o')
                ->field('o.*,uu.name user_name')
                ->join('admin_user uu','o.user_id=uu.user_id','left')
//                ->field('order_info')
                ->where($map)
                ->order($sort)
                ->paginate($parameter['listrow'],false,['query' => request()->param()]);
        }
//        dump($getlist);
        //搜索下拉，用户名
        $userMap[] = ['hidden','eq','0'];
        $userSort = ['user_id'=>'desc'];
        $getUserList = Db('AdminUser')
            ->field('name,user_id,pid,hidden')
            ->alias('u')
            ->where($userMap)
            ->order($userSort)
            ->select();
        $userMap[] = ['pid','eq','0'];
        $tgetUserList = Db('AdminUser')
            ->field('name,user_id,pid,hidden')
            ->alias('u')
            ->where($userMap)
            ->order($userSort)
            ->select();

        $this->assign([
            'list' => $getlist,
            'userlsit' => $getUserList, //用户列表一级
            'userlsitt' => $tgetUserList, //用户列表
        ]);

        return $this->fetch();
    }


    public function add(){
        $parameter = input();
        // dump($parameter);
        $number = setNumber();
        // $number = getRandomString(6);
//        $number = strtoupper(substr(md5(rand()),1,6));


        $userInfo = $this->checkTokenSession;

//        $arr = explode(',','2213,2214');
//        $order_info = array();
//        foreach ($arr as $v){
//            $data = Db('pot_goods')->where('id',$v)->find();
//            array_push($order_info,$data);
//        }
//        dump($order_info);
//        print_r($img);
        if($parameter){


            $order['order_no'] = $number;
            $order['status'] = 1;
            $order['name'] = $parameter['name'];
            $order['telphone'] = $parameter['telphone'];

            $order['payable_amount'] = $parameter['payable_amount'];
            $order['message'] = $parameter['message'];
            $order['create_time'] = time();
            $order['order_time'] = $parameter['order_time'];
            $order['payment'] = $parameter['payment'];
            $order['address'] = $parameter['address'];
            $order['link_gid'] = $parameter['gid'];
            $order['user_id'] = $userInfo['user_id'];

            $info = $this->pot_order->insert($order);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }


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
        $order_id = intval($parameter['eid']);
        $edit_id= intval($parameter['editid']);
        $info_id= intval($parameter['iid']);

        if(!empty($edit_id)){
            $checkPow = Db('AdminRole')->where('id',$userInfo['role_id'])->value('id');
            if($checkPow != 1){
                returnResponse(100,'您暂无权限修改');
            }
            $map[] = ['id','eq',$edit_id];
            switch ($parameter['status']){
                case 1:
                    $info1 = Db('pot_order')->where($map)->update(['status'=>'1']);
                    if($info1){
                        returnResponse(200,'订单状态已改为“下单成功”',$info1);
                    }elseif ($info1 == 0){
                        returnResponse(200,'订单状态已改为“下单成功”',$info1);
                    }
                    returnResponse(100,'操作失败,稍后再试1');
                    break;
                case 2:
                    $info2 = Db('pot_order')->where($map)->update(['status'=>'2','cargo_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info2){
                        returnResponse(200,'订单状态已改为“厂商配货”',$info2);
                    }elseif ($info2 == 0){
                        returnResponse(200,'订单状态已改为“厂商配货”',$info2);
                    }
                    returnResponse(100,'操作失败,稍后再试2');
                    break;
                case 3:
                    $info3 = Db('pot_order')->where($map)->update(['status'=>'3','sport_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info3){
                        returnResponse(200,'订单状态已改为“国际运输”',$info3);
                    }elseif ($info3 == 0){
                        returnResponse(200,'订单状态已改为“国际运输”',$info3);
                    }
                    returnResponse(100,'操作失败,稍后再试3');
                    break;
                case 4:
                    $info4 = Db('pot_order')->where($map)->update(['status'=>'4','arrive_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info4){
                        returnResponse(200,'订单状态已改为“抵达台湾”',$info4);
                    }elseif ($info4 == 0){
                        returnResponse(200,'订单状态已改为“抵达台湾”',$info4);
                    }
                    returnResponse(100,'操作失败,稍后再试4');
                    break;
                case 5:
                    $info5 = Db('pot_order')->where($map)->update(['status'=>'5','over_time'=>time(),'user_id'=>$userInfo['user_id']]);
                    if($info5){
                        returnResponse(200,'订单状态已改为“订单完成”',$info5);
                    }elseif ($info5 == 0){
                        returnResponse(200,'订单状态已改为“订单完成”',$info5);
                    }
                    returnResponse(100,'操作失败,稍后再试5');
                    break;
                default:;
            }
        }
        $status = Db('pot_order')->where('id',$order_id)->value('status');
        $editid = Db('pot_order')->where('id',$order_id)->value('id');
        $info   = Db('pot_order')->where('id',$info_id)->find();
        $this->assign([
            'status' => $status,
            'editid' => $editid,
            'info' => $info
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
        $account = Db('pot_order')->field('is_show')->where('id',$id)->find();

        $checkPow = Db('AdminRole')->where('id',$userInfo['role_id'])->value('id');

        // return $checkPow;
//        var_dump($checkPow);
        if($checkPow != 1){
            returnResponse(100,'您暂无权限修改');
        }
        if($account['is_show'] == 1){
            $info = Db('pot_order')->where('id',$id)->update(['is_show'=>'0']);
            $status = Db('pot_order')->field('status')->where('id',$id)->value('status');
            if($info){
                returnResponse(200,'true0',$status);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('pot_order')->where('id',$id)->update(['is_show'=>'1']);
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
        $account = Db('pot_order')->field('is_show')->where('id',$id)->find();

        $checkPow = Db('AdminRole')->where('id',$userInfo['role_id'])->value('id');

        // return $checkPow;
//        var_dump($checkPow);
//        if($checkPow != 1){
//            returnResponse(100,'您暂无权限修改');
//        }

        $info = Db('pot_order')->where('id',$id)->delete();

        if($info){
            returnResponse(200,'删除成功');
        }else{
            returnResponse(100,'删除失败');
        }
//
    }
}
