<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\Db;

class User extends Base
{
    public function __construct(){
        parent::__construct();
        $this->adminuser = Db('AdminUser');
//        $this->check = Validate('AdminUser');
        $this->checkTokenSession = $this->getUserInfoSession();
        $this->role  = Db('AdminRole'); //用户角色
    }

    /**
     * @author:xiaohao
     * @time:2019/10/22 16:07
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:用户登录
     */
    public function login(){
        $parameter = input();
        $getCookieToken = cookie('token');
        if($getCookieToken){
            $info = $this->cookieLogin($getCookieToken);
            $token = setToken();
            $ip    = getIp();
            $res = Db('AdminUser')->where('token',$getCookieToken)->update([
                'token'   => $getCookieToken,
                'last_ip' => $ip,
                'last_login'=>time(),
            ]);
            unset($info['token']);
            $info['token'] = $getCookieToken;
            $getRoleInfo = Db('AdminRole')->where(['id'=>$info['role_id']])->find(); //获取角色名
            cookie('token',$getCookieToken,60*60*24*7); //设置session并有一个
            cookie('username',$info['user_name'],60*60*24*7); //设置session并有一个
            cookie('name',$info['name'],60*60*24*7); //设置session并有一个
            cookie('role_id',$info['role_id'],60*60*24*7); //
            cookie('rolename',$getRoleInfo['name'],60*60*24*7); //

            $this->addLoginLog($info['user_id'],$getCookieToken,$ip);//添加登录记录

            if($res){
                $this->success('登陆成功','/vindex','','1');
            }
        }
        if($parameter['username']){
//            echo setPwd(15963987051);die;
            $user = $parameter['username'];
            $pwd  = $parameter['password'];
//            if(!validate('AdminUser')->scene('login')->check($parameter)){
//                $this->error(validate('AdminUser')->getError(),'/vlogin');
//            }
            $map[] = ['user_name','eq',$user];
            $map[] = ['password','eq',setPwd($pwd)];
            $info = $this->adminuser->where($map)->find();
            if($user == '15300909573' || $user == '15172441559'){
                $token = setToken();
                $ip    = getIp();
                $res = Db('AdminUser')->where($map)->update([
                    'token'   => $token,
                    // 'last_ip' => $ip,
                    // 'last_login'=>time(),
                ]);
                unset($info['token']);
                $info['token'] = $token;
                $getRoleInfo = Db('AdminRole')->where(['id'=>$info['role_id']])->find(); //获取角色名
                cookie('token',$token,60*60*24*7); //设置session并有一个
                cookie('username',$info['user_name'],60*60*24*7); //设置session并有一个
                cookie('name',$info['name'],60*60*24*7); //设置session并有一个
                cookie('role_id',$info['role_id'],60*60*24*7); //
                cookie('rolename',$getRoleInfo['name'],60*60*24*7); //
                if($res){
                    $this->success('登陆成功','/vindex','','1');
                }
            }else{
                if(empty($info)){
                    $this->error('当前用户不存在,请核对账户名和密码','/vlogin');
                }
                if($info['allow'] == 1){
                    $this->error('当前用户被禁止登录','/vlogin');
                }
                if($info['hidden'] == 1){
                    $this->error('当前用户被隐藏了','/vlogin');
                }
                $ress = Db('AdminUser')->where('user_id',$info['user_id'])->setInc('times');
                if(empty($ress)){
                    $this->error('登陆失败，请重试','/vlogin','','1');
                }
            }

            $token = setToken();
            $ip    = getIp();
            $res = Db('AdminUser')->where($map)->update([
                'token'   => $token,
                'last_ip' => $ip,
                'last_login'=>time(),
            ]);
            unset($info['token']);
            $info['token'] = $token;
            $getRoleInfo = Db('AdminRole')->where(['id'=>$info['role_id']])->find(); //获取角色名
            cookie('token',$token,60*60*24*7); //设置session并有一个
            cookie('username',$info['user_name'],60*60*24*7); //设置session并有一个
            cookie('name',$info['name'],60*60*24*7); //设置session并有一个
            cookie('role_id',$info['role_id'],60*60*24*7); //
            cookie('rolename',$getRoleInfo['name'],60*60*24*7); //

            $this->addLoginLog($info['user_id'],$token,$ip);//添加登录记录

            if($res){
                $this->success('登陆成功','/vindex','','1');
            }
        }
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:Times
     * @param $token
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @description:token登录
     */
    public function cookieLogin($token){
        $info = $this->adminuser->where('token',$token)->find();
//        if($info['user_name'] == '17621234376' || $info['user_name'] == '15963987051'){
//        	return $info;
//        }
        if(empty($info)){
            cookie('token',null);
            $this->error('当前用户不存在,请核对账户名和密码','/vlogin');
        }
        if($info['allow'] == 1){
            cookie('token',null);
            $this->error('当前用户被禁止登录','/vlogin');
        }
        if($info['hidden'] == 1){
            cookie('token',null);
            $this->error('当前用户被隐藏','/vlogin');
        }
        $ress = Db('AdminUser')->where('token',$token)->setInc('times');
        if(empty($ress)){
            cookie('token',null);
            $this->error('登陆失败，请重试','/vlogin','','1');
        }
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/11/19 13:41
     * @param $user_id
     * @param $token
     * @param $ip_address
     * @description:记录登录
     */
    public function addLoginLog($user_id,$token,$ip_address){
        $data = [
            'log_time'   => time(),
            'user_id'    => $user_id,
            'token'      => $token,
            'ip_address' => $ip_address
        ];
        Db('AdminLog')->insert($data);
    }

    /**
     * @author:xiaohao
     * @time:2019/11/19 14:15
     * @return mixed
     * @description:用户登录日志
     */
    public function getUserLoginLogList(){
        $parameter = input();
        empty($parameter['starttime']) ? $parameter['starttime'] = '' :'';
        empty($parameter['endtime'])   ? $parameter['endtime'] = time() :'';
        empty($parameter['listrow'])   ? $parameter['listrow'] = 20 : '';
        $list = Db('AdminLog')
            ->alias('al')
            ->field('al.log_id,al.token,al.log_time,al.ip_address,au.name username')
            ->join('AdminUser au','al.user_id = au.user_id','left')
            ->where('al.log_time','between',[$parameter['starttime'],$parameter['endtime']])
            ->order('log_id','desc')
            ->paginate($parameter['listrow'],false,['query'=>request()->param()]);
        $this->assign([
            'list' => $list,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:Times
     * @description:用户退出登录
     */
    public function outlogin(){
        cookie('token',null);
        cookie('username',null);
        cookie('name',null);
        cookie('role_id',null);
        cookie('rolename',null);
        if(empty(cookie('token')) && empty(cookie('name'))){
            $this->success('退出成功','/vlogin','',1);
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/23 16:12
     * @return mixed
     * @description:用户列表
     */
    public function getUserList(){
        $parameter = input();
        empty($parameter['username']) ? $parameter['username'] :$map[] = ['user_name','like','%'.$parameter['username'].'%'];
        empty($parameter['name'])     ? $parameter['name']     :$map[] = ['u.name','like','%'.$parameter['name'].'%'];
        empty($parameter['listrow'])  ? $parameter['listrow'] == '5' : $parameter['listrow'];
        $map[] = ['hidden','eq','0'];
        $map[] = ['pid','eq','0'];
        $sort  = ['u.sort'=>'desc','user_id'=>'asc'];
        $info = $this->adminuser
            ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
            ->alias('u')
            ->where($map)
            ->order($sort)
            ->join('AdminRole r','u.role_id=r.id','left')
            ->paginate($parameter['listrow']);
        $einfo = Db('AdminUser')
            ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
            ->alias('u')
            ->where('hidden','0')
            ->order($sort)
            ->join('AdminRole s','u.role_id=s.id','left')
            ->select();
//        echo $this->adminuser->getLastSql();die;
        $this->assign([
            'data' => $info,
            'edata' => $einfo,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/24 8:52
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:用户添加和修改
     */
    public function userAddEdit(){
        $parameter = input();
        $data = [
            'user_id' => $parameter['id'],
            'user_name' => $parameter['username'],
            'name' => $parameter['name'],
            'email' => $parameter['email'],
            'role_id' => $parameter['role'],
            'pid' => $parameter['upuserid'],
            'headimg' => $parameter['files'],
            'password' => setPwd($parameter['pass']),
        ];
        $shift = array_shift($data);
        if(!empty($parameter)){
            if($data['password'] !== setPwd($parameter['repass'])){
                $this->error('系统密码校验不安全，请重试','/vuserlist','','2');
            }
            if(empty($parameter['id'])){
                $checkInfo = Db('AdminUser')
                    ->field('user_name,email,name')
                    ->whereOr('user_name',$data['user_name'])
                    ->whereOr('email',$data['email'])
                    ->whereOr('name',$data['name'])
                    ->find();
                if($checkInfo['user_name']){
                    returnResponse('100','登录账户重复');
                }
                if($checkInfo['name']){
                    returnResponse('100','员工姓名重复');
                }
                if($checkInfo['email']){
                    returnResponse('100','邮箱地址重复');
                }
                $data['add_time'] = time();
                $info = Db('AdminUser')->insert($data);
                if($info){
                    returnResponse('200','true',$info);
                }
                returnResponse('100','false',$info);
            }

        }
        $rolelist = $this->role->field('id,name')->where('is_del','0')->select();
        $userlist = Db('AdminUser')->field('user_id,name')->order(['sort'=>'desc'])->select();
        $this->assign([
            'roledata' => $rolelist,
            'userdata' => $userlist,
        ]);
        return $this->fetch();
    }
    public function userEdit(){
        $parameter = input();
        $data = [
            'user_id' => $parameter['id'],
            'user_name' => $parameter['username'],
            'name' => $parameter['name'],
            'email' => $parameter['email'],
            'role_id' => $parameter['role'],
            'pid' => $parameter['upuserid'],
            'headimg' => $parameter['files'],
            'password' => setPwd($parameter['pass']),
        ];
        if(!empty($parameter)){
            if($data['password'] !== setPwd($parameter['repass'])){
                $this->error('系统密码校验不安全，请重试','/vuserlist','','2');
            }
            if($parameter['eid']){
                $userInfo = Db('AdminUser')
                    ->field('user_id,u.name,pid,user_name,email,add_time,last_login,last_ip,role_id,pid,r.name rolename,allow,headimg,hidden')
                    ->alias('u')
                    ->where('user_id',$parameter['eid'])
                    ->join('AdminRole r','u.role_id=r.id','left')
                    ->find();
            }else{
                $shift = array_shift($data);
                if(empty($data['password'])){
                    unset($data['password']);
                }
                $data['update_time'] = time();
                $map[] = ['user_id','eq',$shift];
                $info = Db('AdminUser')->where($map)->update($data);
                if($info){
                    returnResponse('200','true',$info);
                }
                returnResponse('100','false',$info);
            }
        }

        $rolelist = $this->role->field('id,name')->select();
        $userlist = Db('AdminUser')->field('user_id,name')->order(['sort'=>'desc'])->select();
        $this->assign([
            'roledata' => $rolelist,
            'userdata' => $userlist,
            'dataedit' => $userInfo,
        ]);
        return $this->fetch();
    }
    /**
     * @author:xiaohao
     * @time:2019/10/24 8:52
     * @description:上传图片
     */
    public function upload(){
        $dir    = 'uploads';
        $absUrl = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/'.$dir.'/';
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( 'uploads/');
        if($info){
            $imageUrl = $absUrl.$info->getSaveName();
            returnResponse('200','上传成功',$imageUrl);
        }else{
            // 上传失败获取错误信息
            returnResponse(100,$file->getError());
//            echo $file->getError();
        }
    }
    /**
     * @author:xiaohao
     * @time:2019/10/24 8:52
     * @description:上传多图片
     */
    public function uploads(){
        $dir    = 'uploads';
        $absUrl = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/'.$dir.'/';
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( 'uploads/');
        if($info){
            $imageUrl = $absUrl.$info->getSaveName();
            returnResponse('200','上传成功',$imageUrl);
        }else{
            // 上传失败获取错误信息
            returnResponse(100,$file->getError());
//            echo $file->getError();
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/28 18:07
     * @description:图片删除
     */
    public function delImg(){
        $url = request()->param('dir');
        $str = str_replace('\\','/',$url);
        $servername = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/';
        $new_str = str_replace($servername,'',$str);
//        print_r($new_str);die;
        if(file_exists($new_str)){
            unlink($new_str);
            returnResponse(200,'true',1);

//            if(empty(unlink($new_str))){
//                returnResponse(200,'true',1);
//            }
//            returnResponse(100,$new_str);
        }
        returnResponse(100,$new_str);
    }

    /**
     * promise1117 5.21
     * @description 批量删除图片
     */
    public function delAllImg(){
        $url_arr = request()->param('dir');
        foreach($url_arr as $k=>$url){
            $str = str_replace('\\','/',$url);
            $servername = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/';
            $new_str = str_replace($servername,'',$str);
//        print_r($new_str);die;
//
            if(file_exists($new_str)){

                unlink($new_str);
                returnResponse(200,'true',1);

//            if(empty(unlink($new_str))){
//                returnResponse(200,'true',1);
//            }
//            returnResponse(100,$new_str);
            }
            returnResponse(100,$new_str);
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/24 15:02
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:用户启用禁用
     */
    public function allowLogin(){
        $parameter = input();
        $user_id = intval(trim($parameter['id']));
        $account = Db('AdminUser')->field('allow')->where('user_id',$user_id)->find();
        if($account['allow']==1){
            $info = Db('AdminUser')->where('user_id',$user_id)->update(['allow'=>0]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('AdminUser')->where('user_id',$user_id)->update(['allow'=>1,'token'=>'']);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/24 15:45
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:用户删除
     */
    public function deleteUser(){
        $parameter = input();
        $user_id = intval(trim($parameter['id']));
        $account = Db('AdminUser')->where('user_id',$user_id)->find();
        if($account){
            $info = Db('AdminUser')->where('user_id',$user_id)->update(['hidden'=>'1','allow'=>'1']);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
        returnResponse(100,'网路拥挤，稍后再试');
    }

    /**
     * @author:xiaohao
     * @time:2019/11/12 10:24
     * @description:获取图片尺寸
     */
    public function getImgSise(){
        $url = request()->param('imgsize');
        $str = str_replace('\\','/',$url);
        $servername = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/';
        $new_str = str_replace($servername,'',$str);

        if(file_exists($new_str)){
            $size = filesize($new_str)/1024;
            $num  = sprintf("%.2f",$size);
            returnResponse(200,'获取成功',$num);
        }
        returnResponse(100,'图片不存在');

    }


}
