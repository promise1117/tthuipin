<?php
namespace app\backend\controller;
use think\App;
use think\Controller;
use think\Db;
class Timingtask extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->goodsinfo = Db('Goodsinfo');//定时任务删除空套餐
    }


    /**
     * @author:xiaohao
     * @time:2019/11/12 10:22
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:定时任务脚本，删除空的套餐
     */
    public function deleteGoodsinfoNullTaocan(){
        $map[] = ['pid','eq','0'];
        $map[] = ['taocan','eq','0'];
        $map[] = ['is_templete','eq','0'];
        $sort = ['id'=>'acs'];
        $getList = $this->goodsinfo->where($map)->order($sort)->limit(50)->select();
        if($getList){
            foreach($getList as $k=>$v){
                Db('Goodsinfo')->where('id',$v['id'])->delete();
            }
        }
    }

}
