<?php
namespace app\index\controller;
use app\index\controller\Base;
class Order extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->order = model('Order');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 10:00
     * @return mixed
     * @description:提交订单
     */
    public function addOrder()
    {
        $parameter = getInput();
        $info = $this->order->add($parameter);
        return $info;
    }
}
