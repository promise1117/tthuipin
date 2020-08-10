<?php
namespace app\index\controller;
use app\index\controller\Base;
class Banner extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->banner = model('Banner');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 10:00
     * @return mixed
     * @description:获取banner
     */
    public function getList()
    {
        $parameter = getInput();
        $info = model('Banner')->getList($parameter);
        return $info;
    }
}
