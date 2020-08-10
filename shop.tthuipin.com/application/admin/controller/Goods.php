<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Goods extends Base
{

    public function __construct(){
        parent::__construct();
        $this->goods = model('Goods');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 10:55
     * @return mixed
     * @description:获取商品列表
     */
    public function getList(){
        $parameter = getInput();
        $info = $this->goods->getList($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 13:18
     * @return mixed
     * @description:商品添加修改
     */
    public function addEdit(){
        $parameter = getInput();
        $info = $this->goods->addEdit($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 10:53
     * @return mixed
     * @description:商品删除（隐藏）
     */
    public function deleteGoods(){
        $parameter = getInput();
        $info = $this->goods->deleteGoods($parameter);
        return $info;
    }

}
