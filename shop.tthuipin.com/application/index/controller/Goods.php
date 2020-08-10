<?php
namespace app\index\controller;
use app\index\controller\Base;
class Goods extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->goods = Model('Goods');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 10:00
     * @return mixed
     * @description:首页推荐
     */
    public function getIndexList()
    {
        $info = $this->goods->getIndexList();
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 14:12
     * @return mixed
     * @description:内页列表
     */
    public function getList(){
        $parameter = getInput();
        $info = $this->goods->getList($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 16:09
     * @return mixed
     * @description:获取商品详情
     */
    public function getInfo(){
        $parameter = getInput();
        $info = $this->goods->getInfo($parameter);
        return $info;
    }


}
