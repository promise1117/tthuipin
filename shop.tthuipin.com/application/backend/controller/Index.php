<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\db\Query;
use think\Db;
class Index extends Base
{
    public function __construct(){
        parent::__construct();
        $this->checkTokenSession = $this->getUserInfoSession();
        $this->right = Model('AdminRight');//侧边栏
        $this->role  = Db('AdminRole'); //用户角色
    }

    
    public function index(){
        if(!$this->checkTokenSession){
            $this->redirect('/vlogin');
        }
        //rbac 用户，角色，列表分权限
        $rightList = $this->right->getRightList();
        $rightListt = $this->right->getRightListt();
        $this->assign([
            'rightlist' => $rightList,   //左侧列表一级
            'rightlistt' => $rightListt,   //左侧列表二级
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/27 0:09
     * @return mixed
     * @description:获取用户信息
     */
    public function getmyself(){
        $getMyself = $this->checkTokenSession;

        $this->assign([
            'myselfinfo' => $getMyself,
        ]);

        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:Times
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:用户自己修改信息
     */
    public function useredit(){
        $getMyself = $this->checkTokenSession;
        $parameter = input();
        if($parameter['id']){
            $data = [
                'password' => setPwd($parameter['pass']),
                'headimg' => $parameter['files'],
                'name' => $parameter['name'],
                'user_name' => $parameter['username'],
                'email' => $parameter['email'],
                'update_time' => time(),
            ];
            if(empty($parameter['pass'])){
                unset($data['password']);
            }
            $info = Db('AdminUser')->where('user_id',$getMyself['user_id'])->update($data);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }


        $dataedit  = Db('AdminUser')->where('user_id',$getMyself['user_id'])->find();
        $this->assign([
            'myselfinfo' => $getMyself,
            'dataedit' => $dataedit,
        ]);

        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/31 15:49
     * @return mixed
     * @description:获取服务器信息
     */
    public function welcome(){
        $userInfo = $this->checkTokenSession;
        //获取服务器相关信息
        $v = Db::query("select VERSION() as ver");
        $mysqlversion = $v[0]['ver'];
        $info = [
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=> 'V '. \think\facade\App::version(),
            'MySQL版本'=> 'V '. $mysqlversion,
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO'
        ];
        //主管权限
        if(empty($userInfo['pid'])){

            //有效用户统计
            $userAllow = Db('AdminUser')->field('user_id')->where(['hidden'=>'0','allow'=>'0'])->count();
            //banner统计
            $banner = Db('Banner')->field('id')->where(['is_show'=>'0','show_in_nav'=>'0'])->count();
            //商品统计
            $goodsAll = Db('Goods')->field('id')->count(); //所有商品
            $goodsOne = Db('Goods')->field('id')->where('is_del','0')->count(); //正常
            $goodsTwo = Db('Goods')->field('id')->where('is_del','2')->count(); //下架
            $goodsDel = Db('Goods')->field('id')->where('is_del','1')->count(); //删除
            //订单统计
            $orderAll   = Db('Order')->field('id')->count();//所有订单
            $orderOne   = Db('Order')->field('id')->where('status','1')->where('is_show',0)->where('is_del',0)->count();//下单成功
            $orderTwo   = Db('Order')->field('id')->where('status','2')->where('is_show',0)->where('is_del',0)->count();//配送中
            $orderThree = Db('Order')->field('id')->where('status','3')->where('is_show',0)->where('is_del',0)->count();//到达代取
            $orderFour  = Db('Order')->field('id')->where('status','4')->where('is_show',0)->where('is_del',0)->count();//签收
            $orderFive  = Db('Order')->field('id')->where('status','5')->where('is_show',0)->where('is_del',0)->count();//拒收
            $orderSix  = Db('Order')->field('id')->where('status','6')->where('is_show',0)->where('is_del',0)->count();//退换货
            $orderSeven  = Db('Order')->field('id')->where('status','7')->where('is_show',0)->where('is_del',0)->count();//清关中
            $orderEight  = Db('Order')->field('id')->where('status','8')->where('is_show',0)->where('is_del',0)->count();//到达台湾
            $orderNine   = Db('Order')->field('id')->where('status','9')->where('is_show',0)->where('is_del',0)->count();//作废
        }else{
            if(!empty($userInfo['pid'])){
                $checkPow = Db('AdminUser')->field('pid')->where('user_id',$userInfo['pid'])->find();
                //组长权限
                if(empty($checkPow['pid'])){
                    $userid[] = ['pid','eq',$userInfo['user_id']];
                    $userids = Db('AdminUser')->where($userid)->column('user_id');
                    array_push($userids,$userInfo['user_id']);
                    //有效用户统计
                    $sureMap[] = ['user_id','in',$userids];
                    //商品统计
                    $goodsMap[] =['userid','in',$userids];
                    //订单统计
                    $orderMap[] = ['user_id','in',$userids];
                }else{
                //员工权限
                    //有效用户统计
                    $sureMap[] = ['user_id','in',$userInfo['user_id']];
                    //商品统计
                    $goodsMap[] =['userid','in',$userInfo['user_id']];
                    //订单统计
//                    $orderMap[] = ['user_id','in',$userInfo['user_id']];
                }
                //有效用户统计
                $userAllow = Db('AdminUser')->field('user_id')->where(['hidden'=>'0','allow'=>'0'])->where($sureMap)->count();
                //banner统计
                $banner = Db('Banner')->field('id')->where(['is_show'=>'0','show_in_nav'=>'0'])->count();
                //商品统计
                $goodsAll = Db('Goods')->field('id')->where($goodsMap)->count(); //所有商品
                $goodsOne = Db('Goods')->field('id')->where('is_del','0')->where($goodsMap)->count(); //正常
                $goodsTwo = Db('Goods')->field('id')->where('is_del','2')->where($goodsMap)->count(); //下架
                $goodsDel = Db('Goods')->field('id')->where('is_del','1')->where($goodsMap)->count(); //删除
                //订单统计
                $orderAll   = Db('Order')->field('id')->where($orderMap)->count();//所有订单
                $orderOne   = Db('Order')->field('id')->where('status','1')->where('is_show',0)->where('is_del',0)->count();//下单成功
                $orderTwo   = Db('Order')->field('id')->where('status','2')->where('is_show',0)->where('is_del',0)->count();//配送中
                $orderThree = Db('Order')->field('id')->where('status','3')->where('is_show',0)->where('is_del',0)->count();//到达代取
                $orderFour  = Db('Order')->field('id')->where('status','4')->where('is_show',0)->where('is_del',0)->count();//签收
                $orderFive  = Db('Order')->field('id')->where('status','5')->where('is_show',0)->where('is_del',0)->count();//拒收
                $orderSix  = Db('Order')->field('id')->where('status','6')->where('is_show',0)->where('is_del',0)->count();//退换货
                $orderSeven  = Db('Order')->field('id')->where('status','7')->where('is_show',0)->where('is_del',0)->count();//清关中
                $orderEight  = Db('Order')->field('id')->where('status','8')->where('is_show',0)->where('is_del',0)->count();//到达台湾
                $orderNine   = Db('Order')->field('id')->where('status','9')->where('is_show',0)->where('is_del',0)->count();//作废
            }
        }
        // 统计添加到桌面
        $count = Db('count')->where('id','eq','1')->find();
        $count = $count['desktop'];
        $this->assign([
            'count' => $count,
            'systeminfo' => $info, //平台信息
            'userallow'  => $userAllow, //正常用户数
            'banner'     => $banner, //正常 banner数
            'goodsone'   => $goodsOne, //正常商品数
            'goodstwo'   => $goodsTwo, //已下架商品数
            'goodsdel'   => $goodsDel, //已删除商品数
            'goodsall'   => $goodsAll, //所有商品数
            'orderall'   => $orderAll, //所有订单数
            'orderone'   => $orderOne,   //下单成功
            'ordertwo'   => $orderTwo,   //配送中
            'orderthree' => $orderThree, //到达代取
            'orderfour'  => $orderFour,  //签收
            'orderfive'  => $orderFive,  //拒收
            'ordersix'   => $orderSix,   //退换货
            '$orderseven'   => $orderSeven,   //清关中
            'ordereight'   => $orderEight,   //到达台湾
            'ordernine'   => $orderNine,   //作废
        ]);
        return $this->fetch();
    }
}
