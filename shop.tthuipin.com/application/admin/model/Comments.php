<?php
namespace app\admin\model;
use app\admin\model\Base;
use Exception;
class Comments extends Base
{
    protected $tableName = "Comments";

    /**
     * AdminUser constructor.实例化自动执行
     */
    public function __construct()
    {
        parent::__construct();
        $this->comments    = Db('Comments');
        $this->checkToken = $this->checkTokenUserDatas();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/20 15:32
     * @param $parameter
     * @description:获取评论
     */
    public function getList($parameter){
        empty($parameter['listrow'])   ? $parameter['listrow']   = 16 : $parameter['listrow'];
        empty($parameter['liststart']) ? $parameter['liststart'] = 1  : $parameter['liststart'];
        empty($parameter['keywords'])  ? $parameter['keywords'] = ''  : $parameter['keywords'];
        empty($parameter['goods_id'])  ? returnResponse(200,'获取评论失败')  : $parameter['goods_id'];

        $map[] = ['username|contents','like','%'.$parameter['keywords'].'%'];
        $map[] = ['goods_id','eq',$parameter['goods_id']];
        $map[] = ['status','neq',1];
        $order = ['sort'=>'desc','id'=>'desc'];

        $info = Db('Comments')
            ->field('goods_id,order_no,username,contents,img_list,point')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->select();
        $count = Db('Comments')
            ->field('id')
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
     * @time:2019/10/20 15:32
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:评论添加与就该
     */
    public function addEdit($parameter){
        $data = [
            'id'       => $parameter['cid'],
            'goods_id' => $parameter['goods_id'],
            'username' => $parameter['username'],
            'contents' => $parameter['contents'],
            'img_list' => $parameter['img_list'],
            'point'    => $parameter['point'],
            'sort'     => $parameter['sort'],
        ];
        $shift  = array_shift($data);
        if(empty($parameter['cid'])){
            $data['comment_time'] = time();
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
     * @time:2019/10/20 15:38
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:评论删除
     */
    public function deleteComments($parameter){
        $id  = json_decode($parameter['cid'],true);
        foreach($id as $k =>$v){
            $res = Db('Comments')->where('id',$v)->update(['status'=>1]);
        }
        returnResponse(200,'删除成功',$res);
    }

}
