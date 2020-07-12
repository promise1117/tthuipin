<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Db;
use think\Request;
use app\index\Discount;

class Order extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->order = Db('Order');
        //获取header頂部分类名
        $this->catList = $this->getCategoryParentList();
        $this->hot = Db::name('category')->where('parent_id','neq',0)->where('hot','1')->order('sort_order','desc')->select();
        $this->style = Db::name('category')->where('parent_id','neq',0)->where('style','1')->order('sort_order','desc')->select();
        $this->fashion = Db::name('category')->where('parent_id','neq',0)->where('fashion','1')->order('sort_order','desc')->select();
        $this->discount = '';

        foreach($this->catList as $k=>$v){

            $cat_two = Db::name('category')->where('parent_id',$v['cat_id'])->where('is_show','1')->order('sort_order','desc')->select();

            $this->catList[$k]['cat_two'] = $cat_two;



        }


        $this->assign('catlist',$this->catList);
        $this->assign('hot',$this->hot);
        $this->assign('style',$this->style);
        $this->assign('fashion',$this->fashion);

        // 購物車
        $this->shop_car = $this->carSession();
        $this->assign('shop_car',$this->shop_car);

    }

    public function sureOrder(Request $request)
    {

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
                  $this->error('请勿重复提交数据!','index/Order/SureOrder');
              }
          }

        array_push($shop_car,$res);

        session('shop_car',$shop_car);


        $this->shop_car = session('shop_car');
      }else{

          $this->shop_car = session('shop_car')??array();
      }

//      dump($this->shop_car);
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

            $parameter = [
                'num'=>['1'],
                'total_sell_price'=>$this->shop_car[0]['order']['sell_price'],
                'total_cheap_price'=>$this->shop_car[0]['order']['cost_price'],
                'total_cost_price'=>$this->shop_car[0]['order']['cost_price']
            ];

            return $this->fetch('sure_address',[
                'shop_car'=>$this->shop_car,
                'data'=> $parameter,
                'symbol'=>1
            ]);
        }elseif(input('type')=='addCart'){
            //            dump(getenv("HTTP_REFERER"));die;
            header('location:'.$_SERVER["HTTP_REFERER"]);

        }else{
            return $this->fetch('sure_order',[
                'shop_car'=>$this->shop_car,
            ]);
        }

//        return $this->fetch('sure_order',[
//          'shop_car'=>$shop_car,
//          'oncatid'=>3,
//        ]);
    }

    public function del(){
      $data_number = input('post.data_number');
      if(session('shop_car')){
        $data = session('shop_car');
        foreach($data as $k=>$v){
          if($data_number == $v['random_num']) unset($data[$k]);
        }
      }
      session('shop_car',$data);
      return session('shop_car');
    }

    public function sureAddress(){
        $parameter = input();

//      dump($parameter);die;
//      dump(session('shop_car'));
        if(empty($parameter['total_sell_price']) || empty(session('shop_car'))){
            $this->error('您的購物車沒有商品哦，快去逛逛吧！','/');
        }
        if(session('shop_car')){

            //  修改订单中商品数量
            foreach(session('shop_car') as $k=>$v){
                $temp = session('shop_car');

                $temp[$k]['order']['num'] = $parameter['num'][$k];
                session('shop_car',$temp);

            }
            // 修改订单中商品的全选与未全选
            $arr  = array();
            for($i = 0;$i < count($parameter['random_num']);$i++){
                foreach(session('shop_car') as $k=>$v){
                    if($v['random_num'] == $parameter['random_num'][$i]){
                        array_push($arr,$v);
                    }

                }
            }

            session('shop_car',$arr);
        }
       
        return $this->fetch('sure_address',[
            'data'=> $parameter,
        ]);
    }

    public function sureMoney(){

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

      $order_no = date('Ymdhis',time()).rand(100,999);

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
      $order['message'] = $parameter['message'];
      $order['create_time'] = time();
      $order['order_info'] = json_encode($order['order_info']);
      $order['id_card'] = $parameter['ID'];
      $order['discount'] = $parameter['discount'];
      $order['payment'] = $parameter['payment'];
      $order['url'] = request()->server('HTTP_REFERER');

      $telphone = '886'.$parameter['telphone'];
//        dump(intval($telphone));die;
      Db::name('order')->insert($order);
      // 订单完成短信通知用户
      sendAliZjjAuthCode($telphone);
      
      session('shop_car',[]);

      return $this->fetch('sure_money',[
        'order_no'=> $order_no,
      ]);
    }


    public function sureMoneyApi(){

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

//        $order_no = date('Ymdhis',time()).rand(100,999);
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
        $order['message'] = $parameter['message'];
        $order['create_time'] = time();
        $order['order_info'] = json_encode($order['order_info']);
        $order['id_card'] = $parameter['ID'];
        $order['payment'] = $parameter['payment'];
        $order['discount'] = $parameter['discount'];
        $order['url'] = request()->server('HTTP_REFERER');
        $order['ip'] = request()->server('REMOTE_ADDR');

        $order['ip_address'] = $this->ipSearch(request()->server('REMOTE_ADDR'));

        if(empty($order['ip_address'])){
            $order['ip_address'] = $parameter['ip_address'];
        }

        $telphone = '886'.$parameter['telphone'];
//        dump(intval($telphone));die;
        Db::name('order')->insert($order);

        // 订单完成短信通知用户
        sendAliZjjAuthCode($telphone);

        session('shop_car',[]);

        // 调用100折价码
        $this->addDiscountOneHundreed();

        return [
            'order_no'=> $order_no,
            'code'=>$this->discount
        ];
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
            $address = '';
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

    public function addDiscountOneHundreed(){

        $code = substr(strtoupper(md5('hanghao'.time())),0,9);
//        $parameter = input();
        $data = [
            'code' => $code,
            'cost_price' => 'reduce_100',
            'create_time' => time(),
        ];


        Db('discount_code')->insert($data);
//        if($info){
//            returnResponse('200','true',$info);
//        }
//        returnResponse('100','false');

        $this->discount = $code;
    }

   
}
