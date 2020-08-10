<?php
namespace app\index\controller;
use app\index\controller\Base;
class Category extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->category = Model('Category');
    }

    /**
     * @author:xiaohao
     * @time:Times
     * @return mixed
     * @description： 获取一级分类
     */
    public function getList(){
        $info = $this->category->getList();
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 14:50
     * @return mixed
     * @description:获取N级分类
     */
    public function getListMore(){
        $parameter = getInput();
        $info = $this->category->getListMore($parameter);
        return $info;
    }


}
