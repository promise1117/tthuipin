<?php

namespace app\pc\controller;



use app\pc\controller\Base;



class Banner extends Base{

    public function __construct()

    {

        parent::__construct();

    }



    public function BannerPc()

    {

        $banner = Db('banner')->where('is_show',0)->where('type',1)->where('show_in_nav',0)->order('order','desc')->select();


        if($banner){

            $data = array();
            foreach ($banner as $k=>$v){
                $data[$k]['href'] = $v['url'];
                $data[$k]['src'] = $v['img'];
            }


            return ['code'=>0,'msg'=>'请求成功','data'=>$data];
        }else{
            return ['code'=>1,'msg'=>'请求失败','data'=>array()];
        }


    }

    public function BannerMb(){

        $banner = Db('banner')->where('is_show',0)->where('type',2)->where('show_in_nav',0)->order('order','desc')->select();


        if($banner){

            $data = array();
            foreach ($banner as $k=>$v){
                $data[$k]['href'] = $v['url'];
                $data[$k]['src'] = $v['img'];
            }


            return json(['code'=>0,'msg'=>'请求成功','data'=>$data]);
        }else{
            return json(['code'=>1,'msg'=>'请求失败','data'=>array()]);
        }
    }


}