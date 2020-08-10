<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Banner extends Base
{

    public function __construct(){
        parent::__construct();
        $this->banner = model('Banner');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 10:39
     * @return mixed
     * @description:获取banner的列表
     */
    public function getList(){
        $parameter = getInput();
        $info = $this->banner->getList($parameter);
        return $info;
    }
    /**
     * @author:xiaohao
     * @time:2019/10/14 9:46
     * @return mixed
     * @description:banner添加与修改
     */
    public function addEdit(){
        $parameter = getInput();
        $info = $this->banner->addEdit($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 9:48
     * @return mixed
     * @description:删除banner
     */
    public function deleteBanner(){
        $parameter = getInput();
        $info = $this->banner->deleteBanner($parameter);
        return $info;
    }



}
