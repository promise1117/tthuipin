<?php
namespace app\index\controller;
use app\index\controller\Base;
class Category extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->category = Db('Category'); //分类表
    }


    public function getCategoryList(){
        //获取一级分类名
        $catList = $this->getCategoryParentList();
        //获取热卖 分类
        $catHotList = $this->getCategoryHotList();

        $this->assign([
            'catlist' => $catList,
            'hotlist' => $catHotList,
            'oncatid'   => '2',
        ]);
         return $this->fetch();
    }

}
