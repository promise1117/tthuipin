<?php
namespace app\backend\model;
use think\Model;
class AdminRight extends Model
{
 /*   public function __construct($data = [])
    {
        parent::__construct($data);
        $this->role = Db('AdminRole');
        $this->right = Db('AdminRight');
    }*/

    /**
     * @author:xiaohao
     * @time:2019/10/23 14:01
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @description:获取侧边栏目
     */
    public function getRightList(){
        $roleWhere[] = ['id','eq',cookie('role_id')];
        $role_id = db('AdminRole')->where($roleWhere)->column('rights');
        $idGroup = json_encode($role_id,true);
        $idone = str_replace('[','',$idGroup);
        $idtwo = str_replace(']','',$idone);
        $idonesy = str_replace('"','',$idtwo);
        $rightWhere[] = ['pid','eq','0'];
        $rightWhere[] = ['id','in',$idonesy];
        $rightWhere[] = ['is_del','eq','0'];
        $rightOrder   = ['sort'=>'desc','id'=>'asc'];
        $getRightList = $this->where($rightWhere)->order($rightOrder)->select();
//        echo $this->right->getLastSql();die;
        return $getRightList;
    }
    public function getRightListt(){
        $roleWhere[] = ['id','eq',cookie('role_id')];
        $role_id = db('AdminRole')->where($roleWhere)->column('rights');
        $idGroup = json_encode($role_id,true);
        $idone = str_replace('[','',$idGroup);
        $idtwo = str_replace(']','',$idone);
        $idonesy = str_replace('"','',$idtwo);
        $rightWhere[] = ['pid','neq','0'];
        $rightWhere[] = ['id','in',$idonesy];
        $rightWhere[] = ['is_del','eq','0'];
        $rightOrder   = ['sort'=>'desc','id'=>'asc'];
        $getRightList = $this->where($rightWhere)->order($rightOrder)->select();
//        echo $this->getLastSql();die;
        return $getRightList;
    }
}
