<?php
namespace app\index\controller;
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
        $this->hot = Db::name('category')->where('parent_id','neq',0)->where('hot','1')->order('sort_order','desc')->select();
        $this->style = Db::name('category')->where('parent_id','neq',0)->where('style','1')->order('sort_order','desc')->select();
        $this->fashion = Db::name('category')->where('parent_id','neq',0)->where('fashion','1')->order('sort_order','desc')->select();

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

    public function aboutUs()
    {
        return $this->fetch();
    }

    public function conditions(){
        return $this->fetch();
    }

    public function contactUs(){
        return $this->fetch();
    }

    public function delivery(){
        return $this->fetch();
    }

    public function distribution(){
        return $this->fetch();
    }

    public function exchange(){
        return $this->fetch();
    }

    public function payment(){
        return $this->fetch();
    }

    public function privacy(){
        return $this->fetch();
    }
    public function enquire(){
        return $this->fetch();
    }

}
