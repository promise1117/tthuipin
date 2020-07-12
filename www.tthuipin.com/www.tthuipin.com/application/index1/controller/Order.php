<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Db;
use think\Request;
class Order extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->order = Db('Order');
    }

    public function sureOrder(Request $request)
    {

      if(input("?post.goods_no")){
        $goods_no = input("post.goods_no");

        $res = Db('Goods')->where('goods_no',$goods_no)->find();
	    $res['img'] = input('post.img');
        $res['order'] = $request->post();

        $res['order']['num'] = 1;

        $shop_car = session('shop_car')?session('shop_car'):array();

        array_push($shop_car,$res);
        // dump(session('shop_car'));die;
        session('shop_car',$shop_car);


        $shop_car = session('shop_car');
      }else{

        $shop_car = session('shop_car');
      }


//        $parameter = input();
//        $data = [
//            'order_no' => 'TW'.setNumber(), //订单号
//            ''
//        ];
//        $info = $this->order->add();
//        return $info;

        return $this->fetch('sure_order',[
          'shop_car'=>$shop_car,
          'oncatid'=>3,
        ]);
    }

    public function del(){
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

    public function sureAddress(){
      $parameter = input();
	if(empty($parameter['total_sell_price'])){
            $this->error('您的購物車沒有商品哦，快去逛逛吧！','/');
        }
        if(session('shop_car')){
            foreach(session('shop_car') as $k=>$v){
                $temp = session('shop_car');
                $temp[$k]['order']['num'] = $parameter['num'][$k];
                session('shop_car',$temp);

            }
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
              $user_id = Db::name('goods')->where('goods_no',$v['order']['goods_no'])->find();
              Db('Goods')->where('goods_no',$v['order']['goods_no'])->setInc('buynumber');//自动添加购买次数
              Db('Goods')->where('goods_no',$v['order']['goods_no'])->setInc('sale');//自动添加购买次数
              $order['order_info'][$k]['userid'] = $user_id['userid'];
          }
      }

      $order_no = setNumber();

      $order['order_no'] = $order_no;
      $order['status'] = 1;
      $order['name'] = $parameter['name'];
      $order['telphone'] = $parameter['telphone'];
      $order['email'] = $parameter['email'];
      $order['lineid'] = $parameter['lineid'];
      $order['address'] = $parameter['address'];
      $order['country'] = $parameter['country'];
      $order['province'] = $parameter['province'];
      $order['city'] = $parameter['city'];
      $order['payable_amount'] = $parameter['payable_amount'];
      $order['create_time'] = time();
      $order['order_info'] = json_encode($order['order_info']);

      Db::name('order')->insert($order);
      session('shop_car',[]);

      return $this->fetch('sure_money',[
        'order_no'=> $order_no,
      ]);
    }
}
