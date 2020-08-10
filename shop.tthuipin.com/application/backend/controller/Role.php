<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\Db;

class Role extends Base
{
    public function __construct(){
        parent::__construct();
        $this->role = Db('AdminRole');
        $this->checkTokenSession = $this->getUserInfoSession();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/23 10:39
     * @return mixed
     * @description:返回用户角色列表
     */
    public function getRoleList(){
        $parameter = input();
        empty($parameter['rights']) ? $parameter['rights'] :$map[] = ['rights','like','%'.$parameter['rights'].'%'];
        empty($parameter['name'])     ? $parameter['name']     :$map[] = ['name','like','%'.$parameter['name'].'%'];
        empty($parameter['listrow'])  ? $parameter['listrow'] == '16' : $parameter['listrow'];
        $map[] = ['is_del','eq','0'];
        $sort = ['sort'=>'desc','id'=>'asc'];
        $getlist = $this->role
            ->where($map)
            ->order($sort)
            ->paginate($parameter['listrow']);
        $this->assign([
            'rolelsit' => $getlist,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/24 17:24
     * @return mixed
     * @description:添加角色
     */
    public function roleAdd(){
        $parameter = input();
        $data = [
          'name' => $parameter['name'],
          'description' => $parameter['description'],
        ];
        $rights = request()->param('rights/a');
        $datarights = json_encode($rights,true);
        $one = str_replace('"','',$datarights);
        $two = str_replace(']','',$one);
        $data['rights'] = str_replace('[','',$two);
        if($parameter){
            $info = $this->role->insert($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $map[] = ['is_del','eq','0'];
        $sort  = ['sort'=>'desc','id'=>'asc'];
        $rights = Db('AdminRight')->where($map)->order($sort)->select();
        $parentRights = Db('AdminRight')->where($map)->where('pid','eq',0)->order($sort)->select();


        $this->assign([
            'right'=>$rights,
            'parentRight'=>$parentRights
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/24 17:48
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:角色修改
     */
    public function roleEdit(){
        $parameter = input();
        $data = [
            'name' => $parameter['name'],
            'description' => $parameter['description'],
        ];
//        $data['rights'] = request()->param('rights/a');

        if($parameter['id']){
            $rights = request()->param('rights/a');
            $twos = array_values($rights);
            $datarights = json_encode($twos,true);
            $one = str_replace('"','',$datarights);
            $two = str_replace(']','',$one);
            $data['rights'] = str_replace('[','',$two);
            $info = $this->role->where('id',intval($parameter['id']))->update($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $map[] = ['is_del','eq','0'];
        $sort  = ['sort'=>'desc','id'=>'asc'];
        $rights = Db('AdminRight')->where($map)->order($sort)->select();
        $parentRights = Db('AdminRight')->where($map)->where('pid','eq',0)->order($sort)->select();
        $info = Db('AdminRole')->where('id',$parameter['eid'])->find();
        $eright = explode(',',$info['rights']);
        $this->assign([
            'right'=>$rights,
            'parentRight'=>$parentRights,
            'info'=>$info,
            'eright' =>$eright,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 9:19
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @description:角色删除
     */
    public function deleteRole(){
        $parameter = input();
        $role_id = intval(trim($parameter['id']));
        if($role_id){
            $info = Db('AdminRole')->where('id',$role_id)->update(['is_del'=>'1']);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
        returnResponse(100,'网路拥挤，稍后再试');
    }


}
