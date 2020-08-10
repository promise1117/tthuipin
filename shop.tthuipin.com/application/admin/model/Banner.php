<?php
namespace app\admin\model;
use app\admin\model\Base;
use Exception;
class Banner extends Base
{
    protected $tableName = "Banner";

    /**
     * AdminUser constructor.实例化自动执行
     */
    public function __construct()
    {
        parent::__construct();
        $this->banner    = Db('Banner');
        $this->check     = validate('Banner');
        $this->checkToken = $this->checkTokenUserDatas();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 10:38
     * @param $parameter
     * @description:banner列表
     */
    public function getList($parameter){
        empty($parameter['listrow'])   ? $parameter['listrow']   = 16 : $parameter['listrow'];
        empty($parameter['liststart']) ? $parameter['liststart'] = 1  : $parameter['liststart'];
        empty($parameter['keywords'])  ? $parameter['keywords'] = ''  : $parameter['keywords'];

        $map[] = ['name','like','%'.$parameter['keywords'].'%'];
        $order = ['order'=>'desc','id'=>'desc'];
        $info = Db('Banner')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->select();
        $count = Db('Banner')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->count();
        $totalPage   = ceil($count/$parameter['listrow']);
        $currentPage = $parameter['liststart'];
        $data = [
            'info'  => $info,
            'total' => $count,
            'totalpage'   => $totalPage,
            'currentPage' => $currentPage
        ];
        returnResponse(200,'请求成功',$data);
    }


    /**
     * @author:xiaohao
     * @time:2019/10/14 9:44
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:banner修改和添加
     */
    public function addEdit($parameter){
        if(!$this->check->scene('addEdit')->check($parameter)){
            returnResponse('100',$this->check->getError());
        }
        $data = [
            'id'   => $parameter['bid'],
            'order'=> $parameter['order'],
            'name' => $parameter['name'],
            'url'  => $parameter['url'],
            'img'  => $parameter['img'],
            'type' => $parameter['type'],
        ];
        $shift  = array_shift($data);
        if(empty($parameter['bid'])){
            $data['addtime'] = time();
            $res  = $this->insert($data);
            $res == true ? returnResponse(200,'添加成功',$res) : returnResponse(100,'添加失败');
        }
        $data['updatetime'] = time();
        $there[]  = ['id','eq',$shift];
        $res    = $this->where($there)->update($data);
        $res == true ? returnResponse(200,'Banner修改成功',$res): returnResponse(100,'Banner修改失败');
    }


    /**
     * @author:xiaohao
     * @time:2019/10/14 10:36
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description: banner删除
     */
    public function deleteBanner($parameter){
        $id  = json_decode($parameter['bid'],true);
        foreach($id as $k =>$v){
            $info = Db('Banner')->where('id',$v)->find();
            deleteImage($info['url']);
            $res = Db('Banner')->where('id',$v)->delete();
        }
        if($res){
            returnResponse(200,'删除成功',$res);
        }
    }

}
