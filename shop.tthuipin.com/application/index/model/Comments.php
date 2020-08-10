<?php
namespace app\index\model;
use app\index\model\Base;
class Comments extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->comments = Db('Comments');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/20 15:56
     * @param $parameter
     * @description:获取评论列表
     */
    public function getList($parameter)
    {
        if(empty($parameter['gid'])){
            returnResponse('100','参数错误');
        }
        $gid = intval(trim($parameter['gid']));
        $map[] = ['goods_id','eq',$gid];
        $sort = ['sort'=>'desc','id'=>'desc'];
        $info = $this->comments
            ->field('id,username,contents,img_list,point')
            ->where($map)
            ->order($sort)
            ->select();
        returnResponse(200,'请求成功',$info);
    }
}
