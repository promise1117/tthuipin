<?php
namespace app\index\controller;
use app\index\controller\Base;
class Shopcar extends Base
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getShopcarList(){
        $parameter = input();
        //获取一级分类名
        $catList = $this->getCategoryParentList();
        //获取热卖 分类
        $catHotList = $this->getCategoryHotList();

        $this->assign([
            'catlist' => $catList,
            'hotlist' => $catHotList,
            'oncatid'   => '3',
        ]);
        return $this->fetch();
    }


}
