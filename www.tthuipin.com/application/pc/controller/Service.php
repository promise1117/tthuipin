<?php
namespace app\pc\controller;
use app\pc\controller\Base;
use think\Db;
use think\Request;
class Service extends Base
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

    public function index()
    {
        return $this->fetch();
    }

    public function enquire(){
        return $this->fetch();
    }

    public function orderSearch(){
        $param = input('post.order_no')?input('post.order_no'):'';
        if(!empty($param)){
            $res = Db('order')->where('order_no',$param)->whereOr('telphone',$param)->whereOr('name',$param)->select();
        }else{
            $res = [];
        }

        $data = [];
        if($res){
            $data['code'] = 0;
            $data['msg'] = '查詢訂單成功';
            $data['res'] = $res;
        }else{
            $data['code'] = 1;
            $data['msg'] = '查詢訂單失敗';
            $data['code'] = '暫無數據';
        }

        return $res;
    }
}
