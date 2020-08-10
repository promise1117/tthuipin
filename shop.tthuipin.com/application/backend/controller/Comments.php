<?php
namespace app\backend\controller;
use app\backend\controller\Base;
class Comments extends Base
{
    public function __construct(){
        parent::__construct();
        $this->comments = Db('Comments');
        $this->checkTokenSession = $this->getUserInfoSession();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 14:19
     * @return mixed
     * @description:获取列表
     */
    public function getList(){
        $parameter = input();
        empty($parameter['goods_id']) ? $parameter['goods_id'] :$map[] = ['goods_id','eq',$parameter['goods_id']];
        empty($parameter['listrow'])  ? $parameter['listrow'] = '16' : $parameter['listrow'];
        $map[] = ['c.status','eq','0'];
        $map[] = ['c.username','neq',''];
        $sort = ['g.id'=>'desc','c.sort'=>'desc','c.id'=>'desc'];
        $getlist = $this->comments
            ->field('c.id,c.comment_time,c.contents,c.point,c.img_list,c.username,c.sort,g.name gname,a.name uname')
            ->alias('c')
            ->join('Goods g','c.goods_id=g.id','left')
            ->join('AdminUser a','c.user_id= a.user_id','left')
            ->where($map)
            ->order($sort)
            ->paginate($parameter['listrow'],false,['query'=>request()->param()]);
        $this->assign([
            'commlsit' => $getlist,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/27 23:32
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:添加评论
     */
    public function add(){
        $userInfo = $this->checkTokenSession;
        $parameter = input();
        if(empty($parameter['cid'])){
            $dataf = [
                'goods_id' => $parameter['goods_id'],//商品id
                'user_id'  => $userInfo['user_id'],//用户id
                'comment_time' => time(), //上传时间
            ];
            $keyId = $this->comments->insertGetId($dataf);
        }
        if(!empty($parameter['cid'])){
            $data = [
                'contents' => $parameter['contents'],//上传内容
                'point'    => $parameter['point'],//分数
                'username' => $parameter['username'],//昵称
                'sort'     => $parameter['sort'], //排序
                'img_list' => $parameter['files'],//图片
                'one'      => $parameter['filess'],//图片
                'two'      => $parameter['filesss'],//图片
                'three'    => $parameter['filessss'],//图片
            ];
            $cid = intval($parameter['cid']);
            $info = $this->comments->where(['id'=>$cid])->update($data);

            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $this->assign([
            'keyid' => $keyId,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 15:43
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:修改
     */
    public function edit(){
        $parameter = input();
        if(!empty($parameter['cid'])){
            $data = [
                'contents' => $parameter['contents'],//上传内容
                'point'    => $parameter['point'],//分数
                'username' => $parameter['username'],//昵称
                'sort'     => $parameter['sort'], //排序
                'img_list' => $parameter['files'],//图片
                'one'      => $parameter['filess'],//图片
                'two'      => $parameter['filesss'],//图片
                'three'    => $parameter['filessss'],//图片
            ];
            $cid = intval($parameter['cid']);
            $info = $this->comments->where(['id'=>$cid])->update($data);

            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $info = $this->comments->where('id',$parameter['eid'])->find();
        $userInfo = $this->checkTokenSession;
        if($info['user_id'] !== $userInfo['user_id']){
            returnResponse('100','您无权修改');
        }
        $this->assign([
            'info' => $info,
        ]);
        return $this->fetch();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/25 16:52
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:评论删除
     */
    public function deleteComments(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        if($id){
            $imgUrl = $this->comments->where('id',$id)->find();
            if(file_exists($imgUrl['img_list'])){
                unlink($imgUrl['img_list']);
            }
            $info = $this->comments->where('id',$id)->delete();
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
        returnResponse(100,'网路拥挤，稍后再试');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/27 17:40
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:多选删除
     */
    public function delComMany(){
        $parameter = input();
        $ids = $parameter['ids'];
//        print_r($ids);die;
        foreach($ids as $v){
            $imgUrl = $this->comments->where('id',$v)->find();
            if(file_exists($imgUrl['img_list'])){
                unlink($imgUrl['img_list']);
            }
            $info = $this->comments->where('id',$v)->delete();
        }
        if($info){
            returnResponse(200,'true',$info);
        }
        returnResponse(100,'false');
    }

}
