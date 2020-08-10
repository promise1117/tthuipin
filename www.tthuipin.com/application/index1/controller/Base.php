<?php
namespace app\index\controller;
use think\App;
use think\Controller;
use think\Db;

class base extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    /**
     * @author:xiaohao
     * @time:2019/11/04 9:31
     * @return mixed
     * @description:获取分类父级
     */
    public function  getCategoryParentList(){
        $catWhere[] = ['parent_id','eq','0'];
        $catWhere[] = ['is_show','eq','1'];
        $catSort    = ['sort_order'=>'desc','cat_id'=>'desc'];
        $catList    = Db('Category')->field('cat_id,cat_name,image,hot,is_show,sort_order,parent_id')->where($catWhere)->order($catSort)->select();
        return $catList;
    }
    //获取所有分类
    public function getCategoryParentListt(){
        $catWheret[] = ['is_show','eq','1'];
        $catSortt    = ['sort_order'=>'desc','cat_id'=>'desc'];
        $catListt    = Db('Category')->field('cat_id,cat_name,image,hot,is_show,sort_order,parent_id')->where($catWheret)->order($catSortt)->select();
        return $catListt;
    }
    /**
     * @author:xiaohao
     * @time:2019/11/04 9:58
     * @param $catid
     * @return mixed
     * @description:获取二级分类
     */
    public function getCategoryTwoList($catid){
        $cattWhere[] = ['parent_id','eq',$catid];
        $catWheret[] = ['is_show','eq','1'];
        $cattSort    = ['sort_order'=>'desc','cat_id'=>'desc'];
        $catTowList[] = Db('Category')->field('cat_id,cat_name,image,hot,is_show,sort_order,parent_id')->where($cattWhere)->order($cattSort)->select();
        return $catTowList;
    }
    //获取一级头像
    public function getParentImageLevel($catid){
        $catTowLists = Db('Category')->field('cat_id,cat_name,image,hot,is_show,sort_order,parent_id')->field('image')->where(['cat_id'=>$catid])->find();
        return $catTowLists;
    }

    /**
     * @author:xiaohao
     * @time:2019/11/04 10:43
     * @return mixed
     * @description:获取热门
     */
    public function getCategoryHotList(){
        $cathWhere[] = ['hot','eq','1'];
        $cathWhere[] = ['is_show','eq','1'];
        $cathSort    = ['sort_order'=>'desc','cat_id'=>'desc'];
        $catTowList = Db('Category')->field('cat_id,cat_name,image,hot,is_show,sort_order,parent_id')->where($cathWhere)->order($cathSort)->select();
        return $catTowList;
    }

}
