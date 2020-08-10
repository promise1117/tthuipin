<?php
namespace app\admin\model;
use app\admin\model\Base;
use Exception;
class Category extends Base
{
    protected $tableName = "Category";

    /**
     * AdminUser constructor.实例化自动执行
     */
    public function __construct()
    {
        parent::__construct();
        $this->category  = Db('Category');
        $this->check     = validate('Category');
        $this->checkToken = $this->checkTokenUserDatas();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 12:00
     * @param $parameter
     * @description:获取列表（一级）
     */
    public function getList($parameter){
        $map[] = ['is_show','eq','1'];
        $map[] = ['parent_id','eq','0'];
        $order = ['sort_order'=>'desc','cat_id'=>'desc'];
        $info = $this->category->where($map)->order($order)->select();
        returnResponse(200,'请求成功',$info);
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 12:02
     * @param $parameter
     * @description:获取列表（N级）
     */
    public function getListn($parameter){
        $map[] = ['is_show','eq','1'];
        $map[] = ['parent_id','eq',$parameter['cat_id']];
        $order = ['sort_order'=>'desc','cat_id'=>'desc'];
        $info = $this->category->where($map)->order($order)->select();
        returnResponse(200,'请求成功',$info);
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 11:13
     * @param $parameter
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @description:分类添加修改
     */
    public function addEdit($parameter){
        if(!$this->check->scene('addEdit')->check($parameter)){
            returnResponse('100',$this->check->getError());
        }
        $data = [
            'cat_id'     => $parameter['cat_id'],
            'cat_name'   => trim($parameter['cat_name']),
            'parent_id'  => $parameter['parent_id'],
            'sort_order' => trim($parameter['sort_order']),
            'image'      => $parameter['image'],
            'is_show'    => $parameter['is_show'],
        ];
        $map[]  = ['cat_name','eq',$parameter['cat_name']];
        $double = $this->category->where($map)->find();
//        print_r($double);die;
        $shift  = array_shift($data);
        if(!empty($double['cat_name']) && $parameter['cat_id'] == ''){
            returnResponse(100,'添加数据已存在，建议更换分类名称');
        }
        if(empty($parameter['cat_id'])){
            $res  = $this->insert($data);
            $res == true ? returnResponse(200,'添加成功',$res) : returnResponse(100,'添加失败');
        }
        if($double['cat_name'] == $data['cat_name'] && $double['cat_name'] == $data['cat_name'] && $double['parent_id'] == $data['parent_id'] && $double['sort_order'] == $data['sort_order'] && $double['image'] == $data['image']){
            returnResponse('100','未修改任何数据');
        }
        try{
            $there[]  = ['cat_id','eq',$shift];
            $res    = $this->where($there)->update($data);
            if($res){
                returnResponse(200,'分类信息修改成功',$res);
            }
        }catch(Exception $e){
            returnResponse(100,'分类修改失败');
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 11:18
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:分类删除（假删）即隐藏
     */
    public function deleteCategory($parameter){
        $id   = json_decode($parameter['cat_id'],true);
        $data = ['is_show' => 2];
        foreach($id as $k => $v){
            $res = $this->where('cat_id',$v)->update($data);
        }
        if($res){
            returnResponse(200,'删除成功',$res);
        }
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 12:05
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:恢复隐藏
     */
    public function showCategory($parameter){
        $id   = json_decode($parameter['cat_id'],true);
        $data = ['is_show' => 1];
        foreach($id as $k => $v){
            $res = $this->where('cat_id',$v)->update($data);
        }
        if($res){
            returnResponse(200,'恢复成功',$res);
        }
    }




}
