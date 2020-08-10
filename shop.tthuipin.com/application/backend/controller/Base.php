<?php
namespace app\backend\controller;
use think\App;
use think\Controller;
use think\session;
use think\Db;
use think\Cookie;

class Base extends Controller
{
    /**
     * Base constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/22 16:22
     * @return mixed
     * @description:通过token获取用户唯一信息
     */
    public function getUserInfoSession(){
        $token = cookie('token');
        $action = Request()->Action();
        
        if($action === 'login'){
            return $this->login();
            exit;
        }
        $checkTokenOut = Db('AdminUser')->where('token',$token)->find();
        if(empty($checkTokenOut)){
            $this->error('用过安全验证不存在或者已过期，请重新登录','/vlogin','','5');
        }
        return checkTokenUserDataSession($token);
    }


}
