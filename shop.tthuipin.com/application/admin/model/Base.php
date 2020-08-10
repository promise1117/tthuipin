<?php
namespace app\admin\Model;
use think\App;
use think\Model;
use think\Request;
use think\Db;
class Base extends Model
{
    protected $parameter = '';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/10 14:03
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @description:检测token，并返回用户信息
     */
    public function checkTokenUserDatas(){
        $parameter = input();
        $action = Request()->Action();
        if($action === 'login'){
//            $shift = array_shift($parameter);
//            print_r($parameter);die;
            $this->login($parameter);
            exit;
        }
        return checkTokenUserData();
    }
}
