<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Brand extends Base
{

    public function __construct(){
        parent::__construct();
        $this->brand = model('Brand');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 17:30
     * @return mixed
     * @description:获取品牌列表
     */
    public function getList(){
        $parameter = getInput();
        $info = $this->brand->getList($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 13:18
     * @return mixed
     * @description:品牌添加修改
     */
    public function addEdit(){
        $parameter = getInput();
        $info = $this->brand->addEdit($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 17:26
     * @return mixed
     * @description:品牌删除（隐藏）
     */
    public function deleteBrand(){
        $parameter = getInput();
        $info = $this->brand->deleteBrand($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 17:26
     * @return mixed
     * @description:品牌恢复（显示）
     */
    public function showBrand(){
        $parameter = getInput();
        $info = $this->brand->showBrand($parameter);
        return $info;
    }



}
