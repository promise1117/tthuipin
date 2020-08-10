<?php
namespace app\admin\model;
use app\admin\model\Base;
class AdminUser extends Base
{
    protected $tableName = "AdminUser";

    /**
     * AdminUser constructor.实例化自动执行
     */
    public function __construct()
    {
        parent::__construct();
        $this->user  = Db('AdminUser');
        $this->check = validate('AdminUser');
        $this->checkToken = $this->checkTokenUserDatas();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/13 9:12
     * @param $parameter
     * @description:用户列表
     */
    public function getList($parameter){
        empty($parameter['listrow'])   ? $parameter['listrow']   = 16 : $parameter['listrow'];
        empty($parameter['liststart']) ? $parameter['liststart'] = 1  : $parameter['liststart'];
        $map[] = ['hidden','neq','1'];
        $order = ['sort'=>'desc','user_id'=>'desc'];
        $info = Db('AdminUser')
//            ->field('user_id')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->select();
        $count = Db('AdminUser')
            ->field('user_id')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->count();
        $totalPage   = ceil($count/$parameter['listrow']);
        $currentPage = $parameter['liststart'];/*$totalPage- ($count - $parameter['liststart']*$parameter['listrow'])/$parameter['listrow']*/
        $data = [
            'info'  => $info,
            'total' => $count,
            'totalpage'   => $totalPage,
            'currentPage' => $currentPage
        ];
        returnResponse(200,'请求成功',$data);
    }

    /**
     * @author:xiaohao
     * @time:2019/10/10 14:23
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:用户登录
     */
    public function Login($parameter){
        $user = $parameter['username'];
        $pwd  = $parameter['password'];
        if(empty($user)||empty($pwd)){
            returnResponse(100,'用户名和密码不能为空');
        }
        if(!validate('AdminUser')->scene('login')->check($parameter)){
            returnResponse(100,validate('AdminUser')->getError());
        }

        $token = setToken();
        $ip    = getIp();

        $map[] = ['user_name','eq',trim($user)];
        $map[] = ['password','eq',setPwd($pwd)];
        $info = Db('AdminUser')->where($map)->find();
        if(empty($info)){
            returnResponse(100,'用户不存在,或者密码错误');
        }
        if($info['allow'] == 1){
            returnResponse(100,'当前用户被禁止登录');
        }
        if($info['hidden'] == 1){
            returnResponse(100,'当前用户被拉黑');
        }
        $res = Db('AdminUser')->where($map)->update([
            'token'   => $token,
            'last_ip' => $ip,
        ]);
        unset($info['token']);
        $info['token'] = $token;
        if($res){
            returnResponse(200,'登录成功',$info);
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/11 10:49
     * @param $parameter
     * @description:用户的添加和修改
     */
    public function addEdit($parameter){
        if(!$this->check->scene('addEdit')->check($parameter)){
            returnResponse('100',$this->check->getError());
        }
        $data = [
            'user_id'   => $parameter['uid'],
            'user_name' => trim($parameter['username']),
            'password'  => setPwd(trim($parameter['password'])),
            'headimg'   => $parameter['headimg'],
            'sort'      => trim($parameter['sort']),
        ];
        $map[]  = ['user_name','eq',$data['user_name']];
        $double = $this->user->field('user_id,user_name,password,headimg,sort')->where($map)->find();
        if(!empty($double['user_name']) && empty($parameter['uid'])){
            returnResponse(100,'此账号已被注册');
        }
        $shift = array_shift($data);
        if(empty($parameter['uid'])){
            $res  = Db('AdminUser')->data($data)->insert();
            $res == true ? returnResponse(200,'添加成功',$res) : returnResponse(100,'添加失败');
        }
        if($double['user_name'] == $data['user_name'] && $double['password'] == $data['password'] && $double['headimg'] == $data['headimg'] && $double['sort'] == $data['sort']){
            returnResponse(200,'用户信息未改变',1);
        }
        $map[]  = ['user_id','eq',$shift];
        $res    = Db('AdminUser')->where($map)->update($data);
        $res   == true ? returnResponse(200,'用户信息修改成功',$res) : returnResponse(100,'修改失败');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/11 16:55
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:用户禁用和解封
     */
    public function allowLogin($parameter){
        $adminuser = Db('AdminUser');
        if(!$this->check->scene('allow')->check($parameter)){
            returnResponse('100',$this->check->getError());
        }
        $map[] = ['user_id','eq',$parameter['uid']];
        $info  = $this->user->field('user_id,allow')->where($map)->find();
        if(empty($info)){
            returnResponse(100,'参数不存在');
        }
        switch($info['allow']){
            case "0":
                $res = $adminuser->where($map)->update(['allow'=>1]);
                if($info){
                    returnResponse(200,'该用户被封号',$res);
                }
                break;
            case "1":
                $res = $adminuser->where($map)->update(['allow'=>0]);
                if($info){
                    returnResponse(200,'该用户账号被解封',$res);
                }
                break;
            default: ;
        }
    }


    /**
     * @author:xiaohao
     * @time:2019/10/11 17:58
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:用户删除（可批量删除）
     */
    public function deleteUser($parameter){
        $id   = json_decode($parameter['uid'],true);
        $data = ['hidden'=>1];
        foreach($id as $k => $v){
            $res = $this->where('user_id',$v)->update($data);
        }
        if($res){
            returnResponse(200,'删除成功',$res);
        }
    }

}
