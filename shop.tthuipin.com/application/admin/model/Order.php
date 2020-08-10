<?php
namespace app\admin\model;
use app\admin\model\Base;
use Exception;
use think\Db;
class Order extends Base
{
    protected $tableName = "Order";

    /**
     * AdminUser constructor.实例化自动执行
     */
    public function __construct()
    {
        parent::__construct();
        $this->order    = Db('Order');
        $this->checkToken = $this->checkTokenUserDatas();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 10:38
     * @param $parameter
     * @description:订单列表
     */
    public function getList($parameter){
        empty($parameter['listrow'])   ? $parameter['listrow']   = 16 : $parameter['listrow'];
        empty($parameter['liststart']) ? $parameter['liststart'] = 1  : $parameter['liststart'];
        empty($parameter['keywords'])  ? $parameter['keywords'] = ''  : $parameter['keywords'];
        empty($parameter['status'])    ? $parameter['status']  : $map[] = ['status','eq',$parameter['status']];

        $map[] = ['order_no|telphone|accept_name','like','%'.$parameter['keywords'].'%'];
        $map[] = ['is_show','eq','0'];
        $order = ['id'=>'desc'];
        $info = Db::name('Order')
            ->field('id,order_no,pay_type,status,pay_status,distribution_status,accept_name,postcode,telphone,country,province,city,area,address,mobile,payable_amount,order_amount,good_id,gdescription,lineid,message,create_time')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
//            ->each(function($items){
//                $items['pay_type']==1?$items['pay_type']='其他付款':$items['pay_type']='货到付款';
//                $items['status'] = [1=>'订单状态',2=>'支付订单',3=>'取消订单',4=>'作废订单',5=>'完成订单',6=>'退款',7=>'部分退款'];//订单状态 1生成订单,2支付订单,3取消订单(客户触发),4作废订单(管理员触发),5完成订单,6退款(订单完成后),7部分退款(订单完成后)',
//                return $items;
//            })
            ->select();
        $count = Db('Order')
            ->field('id')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->count();
        $totalPage   = ceil($count/$parameter['listrow']);
        $currentPage = $parameter['liststart'];
        $data = [
            'info'  => $info,
            'total' => $count,
            'totalpage'   => $totalPage,
            'currentPage' => $currentPage
        ];
        returnResponse(200,'请求成功',$data);
    }


    /**
     * @author:xiaohao
     * @time:2019/10/14 9:44
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:banner修改和添加
     */
    public function addEdit($parameter){
        $data = [
            'id'   => $parameter['oid'],
            'status'=> $parameter['status'],
        ];
        $shift  = array_shift($data);

        $data['update_time'] = time();
        $there[]  = ['id','eq',$shift];
        $res    = $this->where($there)->update($data);
        $res == true ? returnResponse(200,'订单状态修改成功',$res): returnResponse(100,'订单状态修改失败');
    }


    /**
     * @author:xiaohao
     * @time:2019/10/14 10:36
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description: banner删除
     */
    public function deleteOrder($parameter){
        $id  = json_decode($parameter['oid'],true);
        foreach($id as $k =>$v){
            $res = Db('Order')->where('id',$v)->delete();
        }
        if($res){
            returnResponse(200,'删除成功',$res);
        }
    }

}
