<?php
namespace app\index\model;
use app\index\model\Base;
class Category extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->category = Db('Category');
    }


    /**
     * @author:xiaohao
     * @time:2019/10/18 14:44
     * @description:获取一级分类
     */
    public function getList(){
        $map[] = ['is_show','eq','1'];
        $map[] = ['parent_id','eq','0'];
        $order = ['sort_order'=>'desc','cat_id'=>'desc'];
        $info = $this->category
            ->field('cat_id,cat_name,image')
            ->where($map)
            ->order($order)
            ->select();
        returnResponse(200,'请求成功',$info);
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 14:51
     * @param $parameter
     * @description:获取N级分类
     */
    public function getListMore($parameter){
        $map[] = ['is_show','eq','1'];
        $map[] = ['parent_id','eq',$parameter['cat_id']];
        $order = ['sort_order'=>'desc','cat_id'=>'desc'];
        $info = $this->category
            ->field('cat_id,cat_name,image,parent_id')
            ->where($map)
            ->order($order)
            ->select();
        returnResponse(200,'请求成功',$info);
    }
}

