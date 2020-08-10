<?php
namespace app\index\model;
use app\index\model\Base;
class Banner extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->banner = Db('Banner');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 10:27
     * @param $parameter
     * @description:获取banner
     */
    public function getList($parameter)
    {
        if($parameter['type'] == '1'){
            $type = $parameter['type'];
        }
        if($parameter['type'] == '2'){
            $map[] = ['type','eq',$parameter['type']];
        }
        if(empty($parameter['type'])){
            returnResponse('100','参数错误');
        }

        $map[] = ['type','eq',$parameter['type']];
        $sort = ['order'=>'desc','id'=>'desc'];
        $info = $this->banner
            ->field('id,url,img,type')
            ->where($map)
            ->order($sort)
            ->select();
        returnResponse(200,'请求成功',$info);
    }
}
