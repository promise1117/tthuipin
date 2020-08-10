<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Comments extends Base
{

    public function __construct(){
        parent::__construct();
        $this->comments = Model('Comments');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 10:39
     * @return mixed
     * @description:获取comments的列表
     */
    public function getList(){
        $parameter = getInput();
        $info = $this->comments->getList($parameter);
        return $info;
    }
    /**
     * @author:xiaohao
     * @time:2019/10/14 9:46
     * @return mixed
     * @description:comments修改添加
     */
    public function addEdit(){
        $parameter = getInput();
        $info = $this->comments->addEdit($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 9:48
     * @return mixed
     * @description:comments删除
     */
    public function deleteComments(){
        $parameter = getInput();
        $info = $this->comments->deleteComments($parameter);
        return $info;
    }



}
