<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class User extends Base
{
    public function __construct(){
        parent::__construct();
        $this->adminuser = model('AdminUser');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/12 17:57
     * @return mixed
     * @description:获取用户列表
     */
    public function getList(){
        $parameter = getInput() ;
        $info = $this->adminuser->getList($parameter);
        return $info;
    }
    /**
     * @author:xiaohao
     * @time:2019/10/10 11:29
     * @return mixed
     * @description:后台管理员登陆
     */
    public function login()
    {
//        $parameter = input();
        $info = $this->adminuser->Login();
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/10 14:25
     * @return mixed
     * @description:用户添加，修改
     */
    public function addEdit(){
        $parameter = getInput();
        $info = $this->adminuser->addEdit($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/11 16:18
     * @return mixed
     * @description:是否禁止登录
     */
    public function allowLogin(){
        $parameter = getInput();
        $info = $this->adminuser->allowLogin($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/11 17:45
     * @return bool
     * @throws \Exception
     * @description:用户删除（可批量）
     */
    public function delete(){
        $parameter = getInput();
        $info = $this->adminuser->deleteUser($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/17 15:46
     * @return mixed
     * @description:获取用户信息
     */
    public function getMyself(){
        $data = checkTokenUserData();
        returnResponse(200,'获取成功',$data);
    }
}
