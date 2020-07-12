<?php

namespace app\index\controller;

use app\index\controller\Myself;

class Myself extends Base

{

    public function __construct()

    {

        parent::__construct();

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

}

