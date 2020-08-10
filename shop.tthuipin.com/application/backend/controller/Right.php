<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\Db;

class Right extends Base
{
    public function __construct(){
        parent::__construct();
        $this->right = Db('AdminRight');
        $this->checkTokenSession = $this->getUserInfoSession();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 10:44
     * @return mixed
     * @description:权限分类
     */
    public function getRightList(){
        $parameter = input();
        empty($parameter['url']) ? $parameter['url'] :$map[] = ['r.url','like','%'.$parameter['url'].'%'];
        empty($parameter['name'])     ? $parameter['name']     :$map[] = ['r.name','like','%'.$parameter['name'].'%'];
        empty($parameter['listrow'])  ? $parameter['listrow'] == '16' : $parameter['listrow'];
        $map[] = ['r.is_del','eq','0'];
        $map[] = ['r.pid','eq','0'];
        $sort = ['r.sort'=>'desc','r.id'=>'asc'];
        $getlist = $this->right
            ->alias('r')
            ->field('r.id,r.name,r.url,r.pid,r.ico,r.description,ar.name pname')
            ->join('AdminRight ar','r.pid=ar.id','left')
            ->where($map)
            ->order($sort)
            ->paginate($parameter['listrow']);
        $egetlist = Db('AdminRight')
            ->alias('s')
            ->field('s.id,s.name,s.url,s.pid,s.ico,s.description,ars.name pname')
            ->join('AdminRight ars','s.pid=ars.id','left')
            ->where('s.is_del','0')
            ->order(['s.sort'=>'desc','s.id'=>'asc'])
            ->select();
     
        $this->assign([
            'rightlsit' => $getlist,
            'elist' => $egetlist,
        ]);
        return $this->fetch();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/25 11:02
     * @return mixed
     * @description:权限添加
     */
    public function rightAdd(){
        $parameter = input();
        $data = [
          'name' => $parameter['name'],
          'description' => $parameter['description'],
          'url' => $parameter['url'],
          'pid' => $parameter['pid'],
          'sort' => $parameter['sort'],
          'ico'  => $parameter['ico'],
        ];
        if($parameter){
            $info = $this->right->insert($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $map[] = ['is_del','eq','0'];
        $map[] = ['pid','eq','0'];
        $sort  = ['sort'=>'desc','id'=>'asc'];
        $rights = Db('AdminRight')->where($map)->order($sort)->select();
        $erights = Db('AdminRight')->where('is_del','0')->order($sort)->select();
        $this->assign([
            'right'=>$rights,
            'eright'=>$erights,
        ]);
        return $this->fetch();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/25 11:32
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:修改
     */
    public function rightEdit(){
        $parameter = input();
        $data = [
            'name' => $parameter['name'],
            'description' => $parameter['description'],
            'url' => $parameter['url'],
            'pid' => $parameter['pid'],
            'sort' => $parameter['sort'],
            'ico'  => $parameter['ico'],
        ];
        if($parameter['id']){
            $info = $this->right->where('id',$parameter['id'])->update($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $map[] = ['is_del','eq','0'];
        $map[] = ['pid','eq','0'];
        $sort  = ['sort'=>'desc','id'=>'asc'];
        $info  = Db('AdminRight')->where('id',$parameter['eid'])->find();
        $rights = Db('AdminRight')->where($map)->order($sort)->select();
        $erights = Db('AdminRight')->where('is_del','0')->order($sort)->select();
        $this->assign([
            'info' =>$info,
            'right'=>$rights,
            'eright'=>$erights,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 13:10
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:删除
     */
    public function deleteRight(){
        $parameter = input();
        $right_id = intval(trim($parameter['id']));
        if($right_id){
            $info = Db('AdminRight')->where('id',$right_id)->update(['is_del'=>'1']);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
        returnResponse(100,'网路拥挤，稍后再试');
    }


}
