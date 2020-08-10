<?php
namespace app\admin\model;
use app\admin\model\Base;
use Exception;
class Brand extends Base
{
    protected $tableName = "Brand";

    /**
     * AdminUser constructor.实例化自动执行
     */
    public function __construct()
    {
        parent::__construct();
        $this->brand     = Db('Brand');
        $this->check     = validate('Brand');
        $this->checkToken = $this->checkTokenUserDatas();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 17:39
     * @param $parameter
     * @description:品牌列表
     */
    public function getList($parameter){
        empty($parameter['listrow'])   ? $parameter['listrow']   = 16 : $parameter['listrow'];
        empty($parameter['liststart']) ? $parameter['liststart'] = 1  : $parameter['liststart'];
        empty($parameter['keywords'])  ? $parameter['keywords'] = ''  : $parameter['keywords'];
        $map[] = ['is_show','eq','1'];
        $map[] = ['brand_name','like','%'.$parameter['keywords'].'%'];
        $order = ['sort_order'=>'desc','brand_id'=>'desc'];
        $info = Db('Brand')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->select();
        $count = Db('Brand')
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
     * @time:2019/10/13 17:21
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:品牌添加和修改
     */
    public function addEdit($parameter){
        if(!$this->check->scene('addEdit')->check($parameter)){
            returnResponse('100',$this->check->getError());
        }
        $data = [
            'brand_id'   => $parameter['brand_id'],
            'brand_name' => $parameter['brand_name'],
            'brand_logo' => $parameter['brand_logo'],
            'brand_desc' => $parameter['brand_desc'],
            'site_url'   => $parameter['site_url'],
            'sort_order' => $parameter['sort_order'],
            'is_show'    => $parameter['is_show'],
        ];
        $map[]  = ['brand_name','eq',$parameter['brand_name']];
        $double = $this->brand->where($map)->find();
        $shift  = array_shift($data);
        if(!empty($double['brand_name']) && $parameter['brand_id'] == ''){
            returnResponse(100,'添加数据已存在，建议更换品牌名称');
        }
        if(empty($parameter['brand_id'])){
            $data['addtime'] = time();
            $res  = $this->insert($data);
            $res == true ? returnResponse(200,'添加成功',$res) : returnResponse(100,'添加失败');
        }
        if(!empty($double['brand_name'])){
            returnResponse(100,'修改数据已存在，建议更换品牌名称');
        }
        $data['updatetime'] = time();
        $there[]  = ['brand_id','eq',$shift];
        $res    = $this->where($there)->update($data);
        $res == true ? returnResponse(200,'品牌信息修改成功',$res): returnResponse(100,'品牌修改失败');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 17:24
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:品牌删除（隐藏）
     */
    public function deleteBrand($parameter){
        $id   = json_decode($parameter['brand_id'],true);
        $data = ['is_show' => 2];
        foreach($id as $k => $v){
            $res = $this->where('brand_id',$v)->update($data);
        }
        if($res){
            returnResponse(200,'删除成功',$res);
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 17:24
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:品牌恢复（显示）
     */
    public function showBrand($parameter){
        $id   = json_decode($parameter['brand_id'],true);
        $data = ['is_show' => 1];
        foreach($id as $k => $v){
            $res = $this->where('brand_id',$v)->update($data);
        }
        if($res){
            returnResponse(200,'恢复成功',$res);
        }
    }




}
