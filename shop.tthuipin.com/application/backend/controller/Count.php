<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\Db;
class Count extends Base
{
    public function __construct(){
        parent::__construct();
        $this->goods = Db('Goods');
        $this->checkTokenSession = $this->getUserInfoSession();
    }

    /**
     * @author:xiaohao
     * @time:2019/11/18 22:35
     * @return mixed
     * @description:日上传量图形统计
     */
    public function getCount(){

        $userInfo = $this->checkTokenSession;
        $parameter = input();
        empty($parameter['starttime']) ? $parameter['starttime'] = date('Y-m-d 00:00:00',mktime(0,0,0,date('m'),1,date('Y'))) : '';
        empty($parameter['endtime'])   ? $parameter['endtime'] = date('Y-m-d 00:00:00',time()) :'';
//        dump($parameter);
        if(empty($parameter['starttime']) || $parameter['starttime']>$parameter['endtime']){

            for($i=0;$i<intval(date('d'));$i++){
                $time[] = strtotime(date('Y-m-d',time()-($i*60*60*24)));
            }

        }else{

            $falseTime = (strtotime($parameter['endtime']) - strtotime($parameter['starttime']))/(60*24*60);

            for($i=0;$i<=$falseTime;$i++){
                $time[] = strtotime(date('Y-m-d',strtotime($parameter['endtime'])-($i*60*60*24)));
            }
        }


        sort($time);

        //主管权限
        if(empty($userInfo['pid'])){
            $userList = Db::name('AdminUser')->field('user_id,name')->where('pid','neq','0')->where('allow','neq',1)->order(['user_id'=>'desc'])->select();
        }
        if(!empty($userInfo['pid'])){
            $one = Db::name('AdminUser')->field('user_id,pid')->where(['user_id'=>$userInfo['pid']])->where('allow','neq',1)->find();//查当前用户的上级
            if(empty($one['pid'])){
                //组长权限
                $two = Db::name('AdminUser')->field('user_id')->where(['pid'=>$userInfo['user_id']])->where('allow','neq',1)->column('user_id');
                array_push($two,$userInfo['user_id']);
                sort($two);
                foreach($two as $vid){
                    $userList[] = Db::name('AdminUser')->field('user_id,name')->where('user_id',$vid)->where('allow','neq',1)->find();
                }
            }else{
                //员工权限
                $userList[] = Db::name('AdminUser')->field('user_id,name')->where('user_id',$userInfo['user_id'])->where('allow','neq',1)->find();
            }
        }
//        dump(date('Y-m-d H:i:s',$time[14]));



        foreach($userList as $key => $val){
            $userName[$key] = $val['name'];
            foreach($time as $k => $v){
                empty($time[$k+1])?$time[$k+1] = $time[$k]+24*60*60:'';
                $userList[$key]['goodsnum'][] =
                    Db::name('Goods')
                        ->field('id')
                        ->where('userid',$val['user_id'])
                        ->where('is_del','0')
                        ->where('completion','1')
                        ->where('create_time','between',[$time[$k],$time[$k+1]])
                        ->count();
            }
            array_pop($time);
        }

        $times = array_unique($time);
//        array_pop($times);

//        dump($userName);
//        dump($userList);
//        dump($times);
        $this->assign([
            'username' => $userName,
            'list' => $userList,
            'time' => $times,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/11/19 0:14
     * @return mixed
     * @description:购买商品排序
     */
    public function getBuyGoodsList(){
        $parameter = input();
        if(empty($parameter['starttime'])){
            $map[] = ['create_time','>',mktime(0,0,0,date('m'),1,date('Y'))];
            $parameter['starttime'] = mktime(0,0,0,date('m'),1,date('Y'));

        }else{
            $map[] = ['create_time','>',strtotime($parameter['starttime'])];
            $parameter['starttime'] = strtotime($parameter['starttime']);

        }

        if(empty($parameter['endtime'])){
            $map[] = ['create_time','<',time()];
            $parameter['endtime'] = time();

        }else{
            $map[] = ['create_time','<',strtotime($parameter['endtime'])];
            $parameter['endtime'] = strtotime($parameter['endtime']);

        }

        empty($parameter['userid'])  ? $parameter['userid']  : $map[] = ['order_info','like',"%"."\"userid\":".$parameter['userid']."%"];


        $res = Db::name('order')->where('is_del',0)->where($map)->order('id','desc')->select();
//        dump($res);
        $arr = array();
        $arr1 = array();

        foreach ($res as $k=>$v){
            $data = json_decode($v['order_info'],true);

            foreach ($data as $kk=>$vv){

//                $arr[$k][$kk]['goods_no'] = $vv['goods_no'];
//                $arr[$k][$kk]['status'] = $v['status'];
                  array_push($arr,$vv['goods_no'].$v['status']);
                  array_push($arr1,$vv['goods_no']);

            }
        }


        // 对所有订单商品以及状态循环展示, 所有商品订单 , 重复的也显示
//        $countArr = array_count_values($arr);

        $list = array();

        foreach ($arr as $k=>$v){
            $temp = Db::name('Goods')
                ->alias('g')
                ->field('g.id,g.name,g.img,au.name username')
                ->join('AdminUser au','g.userid = au.user_id','left')
//                ->where('g.create_time','between',[$parameter['starttime'],$parameter['endtime']])
                ->where('goods_no',substr($v,0,-1))
                ->find();

            if($temp){
                $temp['status'] = substr($v,-1);
                array_push($list,$temp);
            }
        }
//        dump($list);



        // 对所有订单商品无论状态循环展示
        $countArr1 = array_count_values($arr1); // 统计所有订单中相同商品的数量
        $list1 = array();
        arsort($countArr1);
//        $countArr1 = array_slice($countArr1,0,50);
//        dump($countArr1);exit;
        foreach ($countArr1 as $k => $v){

            $temp1 = Db::name('Goods')
                    ->alias('g')
                    ->field('g.id,g.name,g.img,au.name username')
                    ->join('AdminUser au','g.userid = au.user_id','left')
//                    ->where('g.create_time','between',[$parameter['starttime'],$parameter['endtime']])
                    ->where('goods_no',$k)
                    ->find();

            if($temp1){
                $temp1['buynumber'] = $v;
                $temp1['goods_no'] = $k;


                array_push($list1,$temp1);
            }

        }


        // 对数组进行格式化调整成前台需要的数据
        foreach ($list1 as $k=>$v){
            $list1[$k]['status1'] = 0;
            $list1[$k]['status2'] = 0;
            $list1[$k]['status3'] = 0;
            $list1[$k]['status4'] = 0;
            $list1[$k]['status5'] = 0;
            $list1[$k]['status6'] = 0;
            $list1[$k]['status7'] = 0;
            $list1[$k]['status8'] = 0;
            $list1[$k]['status9'] = 0;
            foreach ($list as $kk=>$vv){

                if($vv['id'] == $v['id']){
                    if($vv['status'] == '1'){
                        $list1[$k]['status1']++;
                    }elseif ($vv['status'] == '2'){
                        $list1[$k]['status2']++;
                    }elseif ($vv['status'] == '3'){
                        $list1[$k]['status3']++;
                    }elseif ($vv['status'] == '4'){
                        $list1[$k]['status4']++;
                    }elseif ($vv['status'] == '5'){
                        $list1[$k]['status5']++;
                    }elseif ($vv['status'] == '6'){
                        $list1[$k]['status6']++;
                    }elseif ($vv['status'] == '7'){
                        $list1[$k]['status7']++;
                    }elseif ($vv['status'] == '8'){
                        $list1[$k]['status8']++;
                    }elseif ($vv['status'] == '9'){
                        $list1[$k]['status9']++;
                    }

                }
            }
        }
        array_multisort(array_column($list1,'buynumber'),SORT_DESC,$list1);

//        $list = Db('Goods')
//            ->alias('g')
//            ->field('g.id,g.name,g.img,g.buynumber,au.name username')
//            ->join('AdminUser au','g.userid = au.user_id','left')
//            ->where('g.create_time','between',[$parameter['starttime'],$parameter['endtime']])
//            ->order(['buynumber'=>'desc','id'=>'desc'])
////            ->limit($parameter['limit'])
//            ->paginate($parameter['limit'],false,['query'=>request()->param()]);
//        dump($list1);die;

        //分类下拉
        $userli[] = ['hidden','eq','0'];
        $userli[] = ['pid','eq','0'];
        $sorts  = ['u.sort'=>'desc','user_id'=>'desc'];
        $pUser = Db::name('AdminUser')
            ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,r.name rolename,allow,headimg,hidden')
            ->alias('u')
            ->where($userli)
            ->where('u.role_id','eq','15')
            ->order($sorts)
            ->join('AdminRole r','u.role_id=r.id','left')
            ->select();

        $cUser = Db::name('AdminUser')
            ->field('u.name,u.user_id,user_name,email,add_time,last_login,last_ip,pid,s.name rolename,allow,headimg,hidden')
            ->alias('u')
            ->where('hidden','0')
            ->order($sorts)
            ->join('AdminRole s','u.role_id=s.id','left')
            ->select();


        $this->assign([
            'puser' => $pUser,
            'cuser' => $cUser,
            'list' => $list1,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/11/26 9:44
     * @return mixed
     * @description:商品浏览排名
     */
    public function getVisitGoodsList(){
        $parameter = input();
        empty($parameter['starttime']) ? $parameter['starttime'] = '' :'';
        empty($parameter['endtime'])   ? $parameter['endtime'] = time() :'';
        empty($parameter['limit'])     ? $parameter['limit'] = 20 : '';
//        empty($parameter['listrow'])   ? $parameter['listrow'] = 20 : '';
        $list = Db('Goods')
            ->alias('g')
            ->field('g.id,g.name,g.img,g.liulan,au.name username')
            ->join('AdminUser au','g.userid = au.user_id','left')
            ->where('g.create_time','between',[$parameter['starttime'],$parameter['endtime']])
//            ->where('g.is_del','eq',0)
            ->order(['liulan'=>'desc'])
//            ->limit($parameter['limit'])
            ->paginate($parameter['limit'],false,['query'=>request()->param()]);
        $this->assign([
            'list' => $list,
            'limit' => $parameter['limit']
        ]);
        return $this->fetch();
    }

    /**
     * @return mixed
     * 订单量统计
     */
    public function daySales(){
        $parameter = input();
        empty($parameter['starttime']) ? $parameter['starttime'] = '' :'';
//        empty($parameter['endtime'])   ? $parameter['endtime'] = date('Y-m-d') :'';


        if(empty($parameter['starttime']) || $parameter['starttime']>$parameter['endtime'] || strtotime($parameter['starttime'])>=time()){
            //根据单日统计

            for($i=0;$i<=9;$i++){
                $time[] = strtotime(date('Y-m-d',time()-($i*60*60*24)));
            }
//            dump($time);
            //根据产品人员判断
            for($j=0;$j<intval(date('d'));$j++){
                $time1[] = strtotime(date('Y-m-d',time()-($j*60*60*24)));
            }

            //根据小时判断
            // 获取0点时间戳
            //当日0点的时间

            $dateStr = date('Y-m-d', time());
//            $timestamp0 = strtotime($dateStr);

            //当日24点的时间

            $timestamp24 = strtotime($dateStr) + 86399;
            for($j=0;$j<intval(date('d'));$j++){
                $time2[$j]['day']['time'] = strtotime(date('Y-m-d H:i:s', $timestamp24-($j*60*60*24)));
                for($k=0;$k<24;$k++){
                    $time2[$j]['hour'][$k]['time'] = strtotime(date('Y-m-d H:i:s', $timestamp24-($j*60*60*24-$k*60*60)-60*60*24));
                }
            }


            // 根据推广人员统计
            for($j=0;$j<intval(date('d'));$j++){
                $time3[] = strtotime(date('Y-m-d',time()-($j*60*60*24)));
            }
        }else{


//            $endtime = date('Y-m-d',$parameter['endtime']);
//            dump($parameter['endtime']);
//            $starttime = date('Y-m-d',$parameter['starttime']);

            $falseTime = (strtotime($parameter['endtime']) - strtotime($parameter['starttime']))/(60*24*60);
            $falseTime1 = (strtotime($parameter['endtime']) - strtotime($parameter['starttime']))/(60*24*60);
            $falseTime2 = (strtotime($parameter['endtime']) - strtotime($parameter['starttime']))/(60*24*60);
            $falseTime3 = (strtotime($parameter['endtime']) - strtotime($parameter['starttime']))/(60*24*60);
            if($falseTime1 >=31){
                $falseTime1 = 31;
            }
            if($falseTime2 >=31){
                $falseTime2 = 31;
            }
            if($falseTime3 >=31){
                $falseTime3 = 31;
            }


            for($i=0;$i<=$falseTime;$i++){
                $time[] = strtotime(date('Y-m-d',strtotime($parameter['endtime'])-($i*60*60*24)));
            }

            for($j=0;$j<=$falseTime1;$j++){
                $time1[] = strtotime(date('Y-m-d',strtotime($parameter['endtime'])-($j*60*60*24)));
            }

            //根据小时判断
            $dateStr = date('Y-m-d', time());
//            $timestamp0 = strtotime($dateStr);

            //当日24点的时间

            $timestamp24 = strtotime($dateStr) + 86399;
            for($j=0;$j<=$falseTime2;$j++){
                $time2[$j]['day']['time'] = strtotime(date('Y-m-d H:i:s', strtotime($parameter['endtime'])-($j*60*60*24)));
                for($k=0;$k<24;$k++){
                    $time2[$j]['hour'][$k]['time'] = strtotime(date('Y-m-d H:i:s', strtotime($parameter['endtime'])-($j*60*60*24-$k*60*60)-60*60*24));
                }
            }

            for($j=0;$j<=$falseTime3;$j++){
                $time3[] = strtotime(date('Y-m-d',strtotime($parameter['endtime'])-($j*60*60*24)));
            }

        }

        sort($time);
        sort($time1);
        sort($time2);
        sort($time3);
//        dump($time3);

//        $time2 = array_multisort(array_column($time2, 'time'), SORT_ASC, $time2);

        //  1日订单查询
        foreach($time as $k=>$v){

            //根据是否有区间查询给予末端时间
            if(empty($parameter['endtime'])){
                empty($time[$k+1])?$time[$k+1] = time():'';
            }else{
                empty($time[$k+1])?$time[$k+1] = strtotime($parameter['endtime']):'';
            }
//            empty($time[$k+1])?$time[$k+1] = $time[$k]+24*60*60:'';

            $daysales[$k]['daysalessum'] = Db('Order')->field('id')->where('is_show','0')->where('is_del',0)->where('status','neq',9)->whereTime('create_time','between',[$time[$k],$time[$k+1]])->count();

        }

        $times = array_unique($time);

        array_pop($times);

        //  2产品人员对应订单查询
        $userList = Db('AdminUser')->field('user_id,name')->where('pid','neq','0')->where('allow','neq',1)->order(['user_id'=>'desc'])->select();
        $nameArr = array();
//        dump($time1);
        foreach($userList as $key => $val){
            $userName[$key] = $val['name'];
            array_push($nameArr,$val['name']);

//
            foreach($time1 as $k => $v){
                //根据是否有区间查询给予末端时间
                if(empty($parameter['endtime'])){
                    empty($time1[$k+1])?$time1[$k+1] = time():'';
                }else{
                    empty($time1[$k+1])?$time1[$k+1] = strtotime($parameter['endtime']):'';
                }
                $userList[$key]['ordernum'][] =
                    Db('order')
                        ->field('id')
                        ->where('order_info','like',"%"."\"userid\":".$val['user_id']."%")
                        ->where('is_show','0')
                        ->where('is_del','0')
                        ->where('status','neq',9)
                        ->where('create_time','between',[$time1[$k],$time1[$k+1]])
                        ->count();
//                $userList[$key]['stack'][] = '总量';
//                $userList[$key]['type'][] = 'line';
            }
            array_pop($time1);
        }

        // 3.小时订单查询




//        dump($time2);
        foreach($time2 as $k=>$v){
            if(empty($parameter['endtime'])){
                empty($time2[$k+1])?$time2[$k+1] = time():'';
            }else{
                empty($time2[$k+1])?$time2[$k+1] = strtotime($parameter['endtime']):'';
            }
            $time2[$k]['day']['daysalessum'] = Db::name('Order')->field('id')->where('is_show','0')->where('is_del',0)->where('status','neq',9)->whereTime('create_time','between',[$time2[$k]['day']['time'],$time2[$k+1]['day']['time']])->count();
            foreach ($time2[$k]['hour'] as $kk=>$vv){
                //当天最后一个时间戳
                $dateStr = date('Y-m-d', $vv['time']);
                $timestamp24 = strtotime($dateStr) + 86399;
//                dump(date('Y-m-d H:i:s',$timestamp24));
                empty($time2[$k]['hour'][$kk+1]['time'])?$time2[$k]['hour'][$kk+1]['time'] = $timestamp24:'';
//                echo date('Y-m-d H:i:s',$time2[$k]['hour'][$kk]['time']).'<br>';
//                echo date('Y-m-d H:i:s',$time2[$k]['hour'][$kk+1]['time']).'<br><hr>';
                $time2[$k]['hour'][$kk]['hoursalessum'] = Db::name('Order')->field('id')->where('is_show','0')->where('is_del',0)->where('status','neq',9)->whereTime('create_time','between',[$time2[$k]['hour'][$kk]['time'],$time2[$k]['hour'][$kk+1]['time']])->count();

            }
            array_pop($time2[$k]['hour']);
        }


        array_pop($time2);





        // 4 产品推广人员订单每日统计
        $promoteList = array();
        $promoteSellList = array();
        $promoteCostList = array();
        foreach($time3 as $k => $v){
            //根据是否有区间查询给予末端时间
            if(empty($parameter['endtime'])){
                empty($time3[$k+1])?$time3[$k+1] = time():'';
            }else{
                empty($time3[$k+1])?$time3[$k+1] = strtotime($parameter['endtime']):'';
            }
            // 根据域名分组查询
            $ceshi = Db('order')
                ->field('count(url) as countUrl,url,sum(payable_amount) as sellall,sum(payable_costall) as costall')
                ->where('is_show','0')
                ->where('is_del','0')
                ->where('status','neq',9)
                ->where('create_time','between',[$time3[$k],$time3[$k+1]])
                ->group('url')
                ->order('countUrl')
                ->select();
//            dump($ceshi);
            // 获取所有域名并进行初始值设定=>0
            $start = Db('order')
                ->field('count(url) as countUrl,url')
                ->where('is_show','0')
                ->where('is_del','0')
                ->where('status','neq',9)
                ->group('url')
                ->order('countUrl')
                ->select();


            $ceshiArr = array();
            $sellArr = array();
            $costArr = array();
            foreach($start as $k=>$v){
                $tempUrl = parse_url($v['url'])['host'];

                //                公共
                //www.youmeistore.shop
                //
                //客服
                //www.happytesco.shop
                //
                //王金雷
                //www.jingwyd.shop
                //
                //章晋超
                //www.youxuanjingpin.shop
                //www.youxuanjingpin.store
                //
                //时杰伟
                //www.yougouchaopin.shop
                //www.chaolgw.shop
                //
                //时海龙
                //www.youpinxuanwu.shop
                //www.qiboshop.shop


                //                www.meiriyigou.shop   这是时杰伟
                //www.qiboshop.store    这是时海龙
                switch ($tempUrl){
                    case 'www.happytesco.shop':
                        $tempUrl = '(客服)www.happytesco.shop';
                        break;
                    case 'www.tiantianhuipin.com':
                        $tempUrl = '(客服)www.tiantianhuipin.com';
                        break;
                    case 'www.tthuipin.com':
                        $tempUrl = '(客服)www.tthuipin.com';
                        break;
                    case 'www.jingwyd.shop':
                        $tempUrl = '(王金雷)www.jingwyd.shop';
                        break;
                    case 'www.promiseyou.shop':
                        $tempUrl = '(王金雷)www.promiseyou.shop';
                        break;
                    case 'www.chaofengguwu.shop':
                        $tempUrl = '(王金雷)www.chaofengguwu.shop';
                        break;
                    case 'www.youxuanjingpin.shop':
                        $tempUrl = '(章晋超)www.youxuanjingpin.shop';
                        break;
                    case 'www.youxuanjingpin.store':
                        $tempUrl = '(章晋超)www.youxuanjingpin.store';
                        break;
                    case 'www.qiboshop.shop':
                        $tempUrl = '(章晋超)www.qiboshop.shop';
                        break;
                    case 'www.daydayshop.shop':
                        $tempUrl = '(章晋超)www.daydayshop.shop';
                        break;
                    case 'www.chaofengshop.shop':
                        $tempUrl = '(章晋超)www.chaofengshop.shop';
                        break;
                    case 'www.youmeistore.shop':
                        $tempUrl = '(章晋超)www.youmeistore.shop';
                        break;
                    case 'www.yougouchaopin.shop':
                        $tempUrl = '(时杰伟)www.yougouchaopin.shop';
                        break;
                    case 'www.chaolgw.shop':
                        $tempUrl = '(时杰伟)www.chaolgw.shop';
                        break;
                    case 'www.meiriyigou.shop':
                        $tempUrl = '(时杰伟)www.meiriyigou.shop';
                        break;
                    case 'www.qualityhall.shop':
                        $tempUrl = '(时杰伟)www.qualityhall.shop';
                        break;
                    case 'www.fashiongou.shop':
                        $tempUrl = '(时杰伟)www.fashiongou.shop';
                        break;
                    case 'www.youpinxuanwu.shop':
                        $tempUrl = '(时海龙)www.youpinxuanwu.shop';
                        break;
                    case 'www.qiboshop.store':
                        $tempUrl = '(时海龙)www.qiboshop.store';
                        break;
                    case 'www.chaolowprice.shop':
                        $tempUrl = '(时海龙)www.chaolowprice.shop';
                        break;
                    case 'www.jingwenxuan.shop':
                        $tempUrl = '(时海龙)www.jingwenxuan.shop';
                        break;
                    case 'www.newgoods.shop':
                        $tempUrl = '(张清政)www.newgoods.shop';
                        break;
                    default:;
                }
                if($tempUrl){
                    if(!array_key_exists($tempUrl,$ceshiArr)){
                        $ceshiArr[$tempUrl] = 0;
                        $sellArr[$tempUrl] = 0;
                        $costArr[$tempUrl] = 0;
                    }

                }


            }


            foreach($ceshi as $k=>$v){
                $tempUrl = parse_url($v['url'])['host'];

                switch ($tempUrl){
                    case 'www.happytesco.shop':
                        $tempUrl = '(客服)www.happytesco.shop';
                        break;
                    case 'www.tiantianhuipin.com':
                        $tempUrl = '(客服)www.tiantianhuipin.com';
                        break;
                    case 'www.tthuipin.com':
                        $tempUrl = '(客服)www.tthuipin.com';
                        break;
                    case 'www.jingwyd.shop':
                        $tempUrl = '(王金雷)www.jingwyd.shop';
                        break;
                    case 'www.promiseyou.shop':
                        $tempUrl = '(王金雷)www.promiseyou.shop';
                        break;
                    case 'www.chaofengguwu.shop':
                        $tempUrl = '(王金雷)www.chaofengguwu.shop';
                        break;
                    case 'www.youxuanjingpin.shop':
                        $tempUrl = '(章晋超)www.youxuanjingpin.shop';
                        break;
                    case 'www.youxuanjingpin.store':
                        $tempUrl = '(章晋超)www.youxuanjingpin.store';
                        break;
                    case 'www.qiboshop.shop':
                        $tempUrl = '(章晋超)www.qiboshop.shop';
                        break;
                    case 'www.daydayshop.shop':
                        $tempUrl = '(章晋超)www.daydayshop.shop';
                        break;
                    case 'www.chaofengshop.shop':
                        $tempUrl = '(章晋超)www.chaofengshop.shop';
                        break;
                    case 'www.youmeistore.shop':
                        $tempUrl = '(章晋超)www.youmeistore.shop';
                        break;
                    case 'www.yougouchaopin.shop':
                        $tempUrl = '(时杰伟)www.yougouchaopin.shop';
                        break;
                    case 'www.chaolgw.shop':
                        $tempUrl = '(时杰伟)www.chaolgw.shop';
                        break;
                    case 'www.meiriyigou.shop':
                        $tempUrl = '(时杰伟)www.meiriyigou.shop';
                        break;
                    case 'www.qualityhall.shop':
                        $tempUrl = '(时杰伟)www.qualityhall.shop';
                        break;
                    case 'www.fashiongou.shop':
                        $tempUrl = '(时杰伟)www.fashiongou.shop';
                        break;
                    case 'www.youpinxuanwu.shop':
                        $tempUrl = '(时海龙)www.youpinxuanwu.shop';
                        break;
                    case 'www.qiboshop.store':
                        $tempUrl = '(时海龙)www.qiboshop.store';
                        break;
                    case 'www.chaolowprice.shop':
                        $tempUrl = '(时海龙)www.chaolowprice.shop';
                        break;
                    case 'www.jingwenxuan.shop':
                        $tempUrl = '(时海龙)www.jingwenxuan.shop';
                        break;
                    case 'www.newgoods.shop':
                        $tempUrl = '(张清政)www.newgoods.shop';
                        break;
                    default:;
                }
                if($tempUrl){
                    if(array_key_exists($tempUrl,$ceshiArr)){
                        $ceshiArr[$tempUrl] += $v['countUrl'];
                    }else{
                        $ceshiArr[$tempUrl] = $v['countUrl'];
                    }

                    if(array_key_exists($tempUrl,$sellArr)){
                        $sellArr[$tempUrl] += $v['sellall'];
                    }else{
                        $sellArr[$tempUrl] = $v['sellall'];
                    }


                    if(array_key_exists($tempUrl,$costArr)){
                        $costArr[$tempUrl] += $v['costall'];
                    }else{
                        $costArr[$tempUrl] = $v['costall'];
                    }

                }


            }

//            ksort($ceshiArr);
//            ksort($sellArr);
//            ksort($costArr);

            // 对数组进行自定义排序
            $customCeshiArr = array();
            $customSellArr = array();
            $customCostArr = array();

            // 客服
            foreach($ceshiArr as $k=>$v){
                if(preg_match('/客服/',$k)){
                    $customCeshiArr[$k] = $v;
                }
            }
            foreach($sellArr as $k=>$v){
                if(preg_match('/客服/',$k)){
                    $customSellArr[$k] = $v;
                }
            }
            foreach($costArr as $k=>$v){
                if(preg_match('/客服/',$k)){
                    $customCostArr[$k] = $v;
                }
            }
            // 时杰伟
            foreach($ceshiArr as $k=>$v){
                if(preg_match('/时杰伟/',$k)){
                    $customCeshiArr[$k] = $v;
                }
            }
            foreach($sellArr as $k=>$v){
                if(preg_match('/时杰伟/',$k)){
                    $customSellArr[$k] = $v;
                }
            }
            foreach($costArr as $k=>$v){
                if(preg_match('/时杰伟/',$k)){
                    $customCostArr[$k] = $v;
                }
            }
            // 王金雷
            foreach($ceshiArr as $k=>$v){
                if(preg_match('/王金雷/',$k)){
                    $customCeshiArr[$k] = $v;
                }
            }
            foreach($sellArr as $k=>$v){
                if(preg_match('/王金雷/',$k)){
                    $customSellArr[$k] = $v;
                }
            }
            foreach($costArr as $k=>$v){
                if(preg_match('/王金雷/',$k)){
                    $customCostArr[$k] = $v;
                }
            }
            // 章晋超
            foreach($ceshiArr as $k=>$v){
                if(preg_match('/章晋超/',$k)){
                    $customCeshiArr[$k] = $v;
                }
            }
            foreach($sellArr as $k=>$v){
                if(preg_match('/章晋超/',$k)){
                    $customSellArr[$k] = $v;
                }
            }
            foreach($costArr as $k=>$v){
                if(preg_match('/章晋超/',$k)){
                    $customCostArr[$k] = $v;
                }
            }
            // 时海龙
            foreach($ceshiArr as $k=>$v){
                if(preg_match('/时海龙/',$k)){
                    $customCeshiArr[$k] = $v;
                }
            }
            foreach($sellArr as $k=>$v){
                if(preg_match('/时海龙/',$k)){
                    $customSellArr[$k] = $v;
                }
            }
            foreach($costArr as $k=>$v){
                if(preg_match('/时海龙/',$k)){
                    $customCostArr[$k] = $v;
                }
            }
            // 张清政
            foreach($ceshiArr as $k=>$v){
                if(preg_match('/张清政/',$k)){
                    $customCeshiArr[$k] = $v;
                }
            }
            foreach($sellArr as $k=>$v){
                if(preg_match('/张清政/',$k)){
                    $customSellArr[$k] = $v;
                }
            }
            foreach($costArr as $k=>$v){
                if(preg_match('/张清政/',$k)){
                    $customCostArr[$k] = $v;
                }
            }
            // 其他
            foreach($ceshiArr as $k=>$v){
                if(!preg_match('/客服|时杰伟|王金雷|章晋超|时海龙|张清政/',$k)){
                    $customCeshiArr[$k] = $v;
                }
            }
            foreach($sellArr as $k=>$v){
                if(!preg_match('/客服|时杰伟|王金雷|章晋超|时海龙|张清政/',$k)){
                    $customSellArr[$k] = $v;
                }
            }
            foreach($costArr as $k=>$v){
                if(!preg_match('/客服|时杰伟|王金雷|章晋超|时海龙|张清政/',$k)){
                    $customCostArr[$k] = $v;
                }
            }

            $ceshiArr = $customCeshiArr;
            $sellArr = $customSellArr;
            $costArr = $customCostArr;

            $urlArr = array_keys($ceshiArr);


            $count_total = array_values($ceshiArr);
            $count_sell_total = array_values($sellArr);
            $count_cost_total = array_values($costArr);

            array_push($promoteList,$count_total);
            array_push($promoteSellList,$count_sell_total);
            array_push($promoteCostList,$count_cost_total);



        }
        array_pop($time3);


        // 对数据格式进行调整
        $promoteList1 = array();
        foreach($promoteList as $k=>$v){
            foreach ($v as $kk=>$vv){
                $promoteList1[$kk][$k] = $vv;
            }
        }

        $promoteSellList1 = array();
        foreach($promoteSellList as $k=>$v){
            foreach ($v as $kk=>$vv){
                $promoteSellList1[$kk][$k] = $vv;
            }
        }

        // 统计一个域名下的销售价格
        $promoteSellPrice = array();
        foreach ($promoteSellList1 as $k=>$v){
            $tempSellPrice = 0;
            foreach ($v as $kk=>$vv){
                $tempSellPrice += floor($vv/4.2);
            }
            $promoteSellPrice[$k] = $tempSellPrice;
        }

        $promoteCostList1 = array();
        foreach($promoteCostList as $k=>$v){
            foreach ($v as $kk=>$vv){
                $promoteCostList1[$kk][$k] = $vv;
            }
        }

        // 统计一个域名下的成本价格
        $promoteCostPrice = array();
        foreach ($promoteCostList1 as $k=>$v){
            $tempCostPrice = 0;
            foreach ($v as $kk=>$vv){
                $tempCostPrice += floor($vv/4.2);
            }
            $promoteCostPrice[$k] = $tempCostPrice;
        }

//        dump($sellArr);
//        dump($promoteCostList1);
//        dump($promoteSellPrice);
//        dump($promoteCostPrice);
        $this->assign([
            'list' => $daysales,
            'list1' => $userList,
            'time' => $times,
            'time1' => $time1,
            'time2' => $time2,
            'time3' => $time3,
            'nameArr'=>$nameArr,
            'promoteArr'=>$promoteList,
            'promoteArr1'=>$promoteList1,
            'promoteSellList'=>$promoteSellList,
            'promoteSellList1'=>$promoteSellList1,
            'promoteSellPrice'=>$promoteSellPrice,
            'promoteCostList'=>$promoteCostList,
            'promoteCostList1'=>$promoteCostList1,
            'promoteCostPrice'=>$promoteCostPrice,
            'urlArr'=>$urlArr
        ]);
        return $this->fetch();
    }

    /**
     * @return mixed
     * 销售额统计
     */
    public function amount(){
        $param = input();
        $timeArr = array();

        if($param['starttime'] && $param['endtime']){
            if(strtotime($param['endtime']) - strtotime($param['starttime']) > 0){
                $days = (strtotime($param['endtime']) - strtotime($param['starttime']))/(24*60*60);
                for($i = 0; $i < $days;$i++){
                    array_push($timeArr,strtotime($param['starttime'])+$i*24*60*60);
                }
                $starttime = strtotime($param['starttime']);
                $endtime = strtotime($param['endtime']);
            }else{
                // 格式不正确显示最近七天
                for($i = 0;$i < 7;$i++){
                    array_push($timeArr,strtotime('-7 days',mktime(0,0,0,date('m'),date('d'),date('Y')))+$i*24*60*60);
                }
                $starttime = strtotime('-7 days',mktime(0,0,0,date('m'),date('d'),date('Y')));
                $endtime = strtotime(date('Y-m-d 00:00:00'),time());
            }
        }elseif($param['starttime'] && !$param['endtime']){
            // 只有开始日期显示当前开始日期起始7天
            for($i = 0;$i < 7;$i++){
                array_push($timeArr,strtotime($param['starttime'])+$i*24*60*60);
            }
            $starttime = strtotime('-7 days',mktime(0,0,0,date('m'),date('d'),date('Y')));
            $endtime = strtotime(date('Y-m-d 00:00:00'),time());

        }elseif($param['endtime'] && !$param['starttime']){
            // 只有结束日期显示当前结束日期前面7天
            for($i = 0;$i < 7;$i++){
                array_push($timeArr,strtotime($param['endtime'])+$i*24*60*60 - 7*24*60*60);
            }
            $starttime = strtotime('-7 days',mktime(0,0,0,date('m'),date('d'),date('Y')));
            $endtime = strtotime(date('Y-m-d 00:00:00'),time());

        }else{

            // 不填显示最近一月
            for($i = 0;$i < intval(date('d'));$i++){
                array_push($timeArr,mktime(0,0,0,date('m'),1,date('Y'))+$i*24*60*60);
            }
            $starttime = mktime(0,0,0,date('m'),1,date('Y'));
            $endtime = mktime(0,0,0,date('m')+1,1,date('Y'));

        }

        $data = array();

        for($i = 0;$i < count($timeArr);$i++){
            $temp = Db('order')->where('is_show',0)->where('is_del',0)->where('create_time','between time',[date('Y-m-d',$timeArr[$i]),date('Y-m-d',intval($timeArr[$i])+24*60*60)])->select();
            $users = Db('admin_user')->where([['pid','neq',0],['allow','neq',1]])->select();

            $arr = array();
            foreach ($users as $kk=>$vv){
                $arr[$vv['name']] = 0;
                foreach ($temp as $k=>$item) {
                    $order_infos = json_decode($item['order_info'],true);

                    foreach ($order_infos as $subitem) {
                        if($subitem['userid'] == $vv['user_id']){
                            $arr[$vv['name']] += floor(intval($subitem['sell_price']));
                        }
                    }
                }

            }
            array_push($data,$arr);

        }



        $dataTime = array();
        foreach ($timeArr as $v){
            array_push($dataTime,date('Y-m-d',$v));
        }

        // 对数据进行处理成可用的数据

        $series = array_keys($data[0]);
        $temp = array();
        foreach ($series as $k => $v){
            $temp[$k]['name'] = $v;
            $temp[$k]['type'] = 'bar';
            foreach ($data as $kk=>$vv){

                $temp[$k]['data'][$kk] = $vv[$v];

            }
        }




        

        // 销售总额统计
        $dataAll = array();

        $tempAll = Db('order')->where('is_show',0)->where('is_del',0)->select();

        $arr = array();


        foreach ($users as $kk=>$vv){
            $arr[$vv['name']] = 0;
            foreach ($tempAll as $k=>$item) {
                $order_infos = json_decode($item['order_info'],true);

                foreach ($order_infos as $subitem) {
                    if($subitem['userid'] == $vv['user_id']){
                        $arr[$vv['name']] += floor(intval($subitem['sell_price']));
                    }
                }
            }

        }
        array_push($dataAll,$arr);
        $arr1 = array();
        foreach ($dataAll[0] as $k => $v){
            array_push($arr1,['name'=>$k,'value'=>$v]);
        }


        // 销售总额月统计

        // 1. 本月除了今日的所有订单统计
        $dataMonthAll = array();

        $tempMonthAll = Db('order')->where('is_show',0)->where('is_del',0)->where('create_time','between time',[date('Y-m-d 00:00:00',$starttime),date('Y-m-d 00:00:00',$endtime)])->select();

        $arr = array();
        foreach ($users as $kk=>$vv){
            $arr[$vv['name']] = 0;
            foreach ($tempMonthAll as $k=>$item) {
                $order_infos = json_decode($item['order_info'],true);

                foreach ($order_infos as $subitem) {
                    if($subitem['userid'] == $vv['user_id']){
                        $arr[$vv['name']] += floor(intval($subitem['sell_price']));
                    }
                }
            }

        }

        array_push($dataMonthAll,$arr);


        $arr2 = array();
        foreach ($dataMonthAll[0] as $k => $v){
            array_push($arr2,['name'=>$k,'value'=>$v]);
        }

        $userArr = array();
        $saleMonthArr = array();
        $countMonth = 0;
        foreach ($dataMonthAll[0] as $k => $v){
            array_push($userArr,$k);
            array_push($saleMonthArr,$v);
            $countMonth += $v;
        }

        $modify_price = Db('order')->where('is_show',0)->where('is_del',0)->where('create_time','between time',[date('Y-m-d 00:00:00',$starttime),date('Y-m-d 00:00:00',$endtime)])->sum('payable_amount');

        array_push($userArr,'总计(修改价格前统计)');
        array_push($userArr,'总计(修改价格后统计)');
        array_push($saleMonthArr,$countMonth);
        array_push($saleMonthArr,$modify_price);



        // 1. 本月今日的所有订单统计
        $dataDayAll = array();

        $tempDayAll = Db('order')->where('is_show',0)->where('is_del',0)->where('create_time','between time',[date('Y-m-d 00:00:00'),date('Y-m-d H:i:s',time())])->select();

        $arr = array();
        foreach ($users as $kk=>$vv){
            $arr[$vv['name']] = 0;
            foreach ($tempDayAll as $k=>$item) {
                $order_infos = json_decode($item['order_info'],true);

                foreach ($order_infos as $subitem) {
                    if($subitem['userid'] == $vv['user_id']){
                        $arr[$vv['name']] += floor(intval($subitem['sell_price']));
                    }
                }
            }

        }

        array_push($dataDayAll,$arr);


        $arr3 = array();
        foreach ($dataDayAll[0] as $k => $v){
            array_push($arr3,['name'=>$k,'value'=>$v]);
        }

        $userArr1 = array();
        $saleMonthArr1 = array();
        $countMonth1 = 0;
        foreach ($dataDayAll[0] as $k => $v){
            array_push($userArr1,$k);
            array_push($saleMonthArr1,$v);
            $countMonth1 += $v;
        }
        $modify_price1 = Db('order')->where('is_show',0)->where('is_del',0)->where('create_time','between time',[date('Y-m-d 00:00:00'),date('Y-m-d H:i:s',time())])->sum('payable_amount');

        array_push($userArr1,'总计(修改价格前统计)');
        array_push($userArr1,'总计(修改价格后统计)');
        array_push($saleMonthArr1,$countMonth1);
        array_push($saleMonthArr1,$modify_price1);

//        dump($userArr);
//        dump($saleMonthArr);
//        dump($dataTime);
//        dump($series);
//        dump($temp);
//        dump($arr1);
        $this->assign([
           'dataTime'=>$dataTime,
            'legend'=>$series,
            'temp'=>$temp,
            'dataAll'=>$arr1,
            'userArr'=>$userArr,
            'saleMonthArr'=>$saleMonthArr,
            'saleMonthArr1'=>$saleMonthArr1
        ]);
        return $this->fetch();
    }
}
