<?php
namespace app\index\controller;
use app\index\controller\Base;
class Comments extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->comments = model('Comments');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 10:00
     * @return mixed
     * @description:获取评论
     */
    public function getList()
    {
        $parameter = getInput();
        $info = $this->comments ->getList($parameter);
        return $info;
    }
}
