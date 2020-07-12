<?php

namespace app\index\controller;


use app\index\controller\Base;

use think\Db;

class Myself extends Base

{

    public function __construct()

     {

     
        parent::__construct();

        $this->banner    = Db('Banner'); //banner表

        $this->category  = Db('Category'); //分类表

        $this->goods     = Db('Goods'); //商品表

        $this->comments  = Db('Comments'); //评论表

        $this->goodsinfo = Db('Goodsinfo'); //商品详情表



        //获取header頂部分类名

        $this->catList = $this->getCategoryParentList();

        $this->hot = Db::name('category')->where('parent_id','neq',0)->where('hot','1')->where('is_show','1')->order('sort_order','desc')->select();
        $this->style = Db::name('category')->where('parent_id','neq',0)->where('style','1')->where('is_show','1')->order('sort_order','desc')->select();
        $this->fashion = Db::name('category')->where('parent_id','neq',0)->where('fashion','1')->where('is_show','1')->order('sort_order','desc')->select();

        foreach($this->catList as $k=>$v){

            $cat_two = Db::name('category')->where('parent_id',$v['cat_id'])->where('is_show','1')->order('sort_order','desc')->select();

            $this->catList[$k]['cat_two'] = $cat_two;



        }


        $this->assign('catlist',$this->catList);
        $this->assign('hot',$this->hot);
        $this->assign('style',$this->style);
        $this->assign('fashion',$this->fashion);



        // 頂部購物車



        $this->shop_car = $this->carSession();

        $this->assign('shop_car',$this->shop_car);



    }


 


    public function index()

    {
        //获取一级分类名
        $catList = $this->getCategoryParentList();
        //获取所有分类
        $catListt = $this->getCategoryParentListt();
        //获取热卖 分类
        $catHotList = $this->getCategoryHotList();
        $this->assign([
            'catlist'   => $catList, //一级分类
            'catlistt'  => $catListt, //所有分类
            'hotlist'   => $catHotList, //获取热卖
            'oncatid'   => '4',
        ]);

        return $this->fetch();

    }



    public function service()

    {

        return $this->fetch();

    }

    public function privacy()

    {

        return $this->fetch();

    }

    public function question()

    {

        return $this->fetch();

    }

     public function my(){
        return $this->fetch();
     }
	 
	 public function myOrder(){
        return $this->fetch();
     }

}

