<?php

namespace app\index\controller;

use app\index\controller\Base;

use think\Db;

class Discount extends Base

{

    public function __construct()

    {
        parent::__construct();

    }

    public function getDiscountCode(){
        $code = input('code');
        if($code){
            $res = Db('discount_code')->where('code','eq',$code)->where('status',0)->find();
            if($res){
                $cost_price = $res['cost_price'];
                Db('discount_code')->where('code','eq',$code)->update(['status'=>1,'use_time'=>time()]);
                returnResponse(200,'请求成功',['cost_price'=>$cost_price]);
            }else{
                returnResponse(100,'请求失败',array());
            }
        }else{
            returnResponse(100,'请求失败',array());
        }
    }


}
