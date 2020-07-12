<?php
namespace app\pc\controller;
use app\pc\controller\Base;
use think\Db;
use think\Request;
class Order extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->order = Db('Order');

        //获取header頂部分类名
        $this->catList = $this->getCategoryParentList();
        foreach($this->catList as $k=>$v){
            $cat_two = Db::name('category')->where('parent_id',$v['cat_id'])->where('is_show','1')->order('sort_order','desc')->select();
            $this->catList[$k]['cat_two'] = $cat_two;
        }
        $this->assign('catlist',$this->catList);

        // 購物車
        $this->shop_car = $this->carSession();
        $this->assign('shop_car',$this->shop_car);
    }

    public function pcSureOrder(Request $request)
    {

//        dump($request->post());

        if(input("?post.goods_no")){
            $goods_no = input("post.goods_no");

            $res = Db('Goods')->where('goods_no',$goods_no)->find();
            $res['img'] = input('post.img');
            $param = $request->post();

            foreach($param['color'] as $k=>$v){
                // 获取该ID下面的商品图片
                $goodsImgId = intval(substr($v,strpos($v,'_')+1));
                $goodsImgSrc = Db('Goodsinfo')->field('image')->where('id',$goodsImgId)->find();
                $param['photo'][$k] = $goodsImgSrc['image'];

                // 重新赋值商品颜色字符串
                $goosImgWords = substr($v,0,strpos($v,'_'));
                $param['color'][$k] = $goosImgWords;
//	        dump($goodsImgSrc);

            }
//        dump($param);die;
            $res['order'] = $param;

            $res['order']['num'] = 1;
            $res['random_num'] = setNumber();
            $shop_car = session('shop_car')?session('shop_car'):array();


            foreach ($shop_car as $k=>$v){
                if($v['random_num'] == $res['random_num']){
                    array_splice($shop_car,$k,1);
                    $this->error('请勿重复提交数据!','pc/Order/pcSureOrder');
                }
            }
            array_push($shop_car,$res);
            // dump(session('shop_car'));die;
            session('shop_car',$shop_car);


            $this->shop_car = session('shop_car');
//            dump($this->shop_car);
        }else{

            $this->shop_car = session('shop_car')??array();

        }
//        dump($this->shop_car);

//        $parameter = input();
//        $data = [
//            'order_no' => 'TW'.setNumber(), //订单号
//            ''
//        ];
//        $info = $this->order->add();
//        return $info;

        /**
         * 根據提交的方式,如果是添加購物車方式直接跳到原頁面,否則進入購物車頁面
         */

        if(input('type')=='symbol'){

            if(input("?post.goods_no")){
                $goods_no = input("post.goods_no");

                $res = Db('Goods')->where('goods_no',$goods_no)->find();
                $res['img'] = input('post.img');
                $param = $request->post();

                foreach($param['color'] as $k=>$v){
                    // 获取该ID下面的商品图片
                    $goodsImgId = intval(substr($v,strpos($v,'_')+1));
                    $goodsImgSrc = Db('Goodsinfo')->field('image')->where('id',$goodsImgId)->find();
                    $param['photo'][$k] = $goodsImgSrc['image'];

                    // 重新赋值商品颜色字符串
                    $goosImgWords = substr($v,0,strpos($v,'_'));
                    $param['color'][$k] = $goosImgWords;
                    //	        dump($goodsImgSrc);

                }
                //        dump($param);die;
                $res['order'] = $param;

                $res['order']['num'] = 1;
                $res['random_num'] = setNumber();


                $shop_car = array();


                array_push($shop_car,$res);

                session('shop_car',$shop_car);


                $this->shop_car = session('shop_car');
            }else{

                $this->shop_car = session('shop_car')??array();
            }

//            array(3) {
//                ["num"] => array(1) {
//                    [0] => string(1) "1"
//  }
//  ["total_sell_price"] => string(4) "1210"
//                ["total_cheap_price"] => string(6) "590.00"
//}

            $parameter = [
                'num'=>['1'],
                'total_sell_price'=>$this->shop_car[0]['order']['sell_price'],
                'total_cheap_price'=>$this->shop_car[0]['order']['cost_price'],
                'total_cost_price'=>$this->shop_car[0]['order']['cost_price']
            ];


            return $this->fetch('pc_sure_address',[
                'data'=> $parameter,
                'shop_car'=>$this->shop_car,
//                'symbol'=>1
            ]);
        }elseif(input('type')=='addCart'){
            //            dump(getenv("HTTP_REFERER"));die;
            header('location:'.$_SERVER["HTTP_REFERER"]);

        }else{
//            dump($this->shop_car);
            return $this->fetch('pc_sure_order',[
                'shop_car'=>$this->shop_car,
            ]);
        }

    }

    public function pcDel(){
        $data_number = input('post.data_number');
        if(session('shop_car')){
            $data = session('shop_car');
            foreach($data as $k=>$v){
                if($data_number == $v['goods_no']) unset($data[$k]);
            }
        }
        session('shop_car',$data);
        return session('shop_car');
    }

    public function pcSureAddress(){
    
        $parameter = input();
//        dump($parameter);
        if(empty(session('shop_car')) || empty($parameter['total_sell_price'])){
            $this->error('您的購物車沒有商品哦，快去逛逛吧！','/');
        }


        if(session('shop_car')){
            foreach(session('shop_car') as $k=>$v){
                $temp = session('shop_car');
                $temp[$k]['order']['num'] = $parameter['num'][$k];
                session('shop_car',$temp);

            }
        }
//        dump($parameter);
        return $this->fetch('pc_sure_address',[
            'data'=> $parameter,
            'shop_car'=>session('shop_car')
        ]);
    }
    public function pcSureMoney(){


        $parameter = input();

        if(session('shop_car')){
            foreach(session('shop_car') as $k=>$v){
                $order['order_info'][$k] = $v['order'];
                $order['order_info'][$k]['sell_price'] = $parameter['payable_amount'];
                $user_id = Db::name('goods')->where('goods_no',$v['order']['goods_no'])->find();
                Db('Goods')->where('goods_no',$v['order']['goods_no'])->setInc('buynumber');//自动添加购买次数
                Db('Goods')->where('goods_no',$v['order']['goods_no'])->setInc('sale');//自动添加购买次数
                $order['order_info'][$k]['userid'] = $user_id['userid'];
            }
        }else{
            $this->error('您的購物車沒有商品哦，快去逛逛吧！','/');
        }

        $order_no = date('md',time()).rand(1000000,9999999);

        $order['order_no'] = $order_no;
        $order['status'] = 1;
        $order['name'] = $parameter['name'];
        $order['telphone'] = $parameter['telphone'];
        $order['email'] = $parameter['email'];
        $order['lineid'] = $parameter['lineid'];
        $order['address'] = $parameter['address'];
        $order['country'] = $parameter['detail_city'];
        $order['province'] = $parameter['detail_area'];
        $order['city'] = $parameter['detail_address'];
        $order['payable_amount'] = $parameter['payable_amount'];
        $order['payable_costall'] = $parameter['payable_costall'];
        $order['payment'] = $parameter['payment'];
        $order['message'] = $parameter['message'];
        $order['discount'] = $parameter['discount'];
        $order['create_time'] = time();
        $order['order_info'] = json_encode($order['order_info']);
        $order['url'] = request()->server('HTTP_REFERER');
        $order['ip'] = request()->server('REMOTE_ADDR');

        $order['ip_address'] = $this->ipSearch(request()->server('REMOTE_ADDR'));
        Db::name('order')->insert($order);
        session('shop_car',[]);

        return $this->fetch('pc_sure_money',[
            'order_no'=> $order_no,
        ]);
    }


    public function ipSearch($ip){
        // AppKey：26fa4ecd776f761261e4af3e295e7a03
        $appkey = "26fa4ecd776f761261e4af3e295e7a03";

        $url = "http://apis.juhe.cn/ip/ip2addr";


        $params = array(
            "ip" => $ip,//需要查询的IP地址或域名
            "key" => $appkey,//应用APPKEY(应用详细页查询)
            "dtype" => "json",//返回数据的格式,xml或json，默认json
        );

        $paramstring = http_build_query($params);
        $content = $this->juhecurl($url,$paramstring);
        $result = json_decode($content,true);
        if($result['resultcode'] == '200'){
            $address = $result['result']['area'].$result['result']['location'];
        }else{
            $address = '未找到指定IP';
        }

        return $address;
    }

    public function juhecurl($url,$params=false,$ispost=0){


        $httpInfo = array();
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }
}
