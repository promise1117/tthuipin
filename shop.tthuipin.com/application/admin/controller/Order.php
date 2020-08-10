<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Order extends Base
{

    public function __construct(){
        parent::__construct();
        $this->order = Model('Order');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/21 13:19
     * @return mixed
     * @description:订单列表
     */
    public function getList(){
        $parameter = getInput();
        $info = $this->order->getList($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 9:46
     * @return mixed
     * @description:订单添加与修改
     */
    public function addEdit(){
        $parameter = getInput();
        $info = $this->order->addEdit($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 9:48
     * @return mixed
     * @description:删除订单
     */
    public function deleteOrder(){
        $parameter = getInput();
        $info = $this->order->deleteOrder($parameter);
        return $info;
    }



}
