<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use think\Db;
use tp5er\Backup;
class Mysqlback extends Base
{
    public function __construct(){
        parent::__construct();
        $this->checkTokenSession = $this->getUserInfoSession();
        $this->config = Config('backupmysqlconfig');
        // require_once "vendor/tp5er/src/Backup.php";
        $this->backdb = new Backup($this->config);
    }

    /**
     * @author:xiaohao
     * @time:2019/11/20 15:01
     * @return mixed
     * @description:数据库列表
     */
    public function getMysqlList(){
        $list = $this->backdb->dataList();
        return $this->fetch(
            'get_mysql_list',
            [
                'list'=>$list,
            ]
        );
    }


    /**
     * @author:xiaohao
     * @time:2019/11/22 13:23
     * @description:备份全部
     */
    public function backup(){
        $parameter = input();
        $time = date('Ymd-His',time());
        foreach ($parameter['bname'] as $k => $v) {
            $start = $this->backdb->setFile()->backup($v, 0);
//            $start = $this->backdb->setFile(['name'=>$time.'alltables'])->backup($v, 0);
        }
        returnResponse(200,'备份成功',$start);
    }

    /**
     * @author:xiaohao
     * @time:2019/11/22 13:52
     * @description:备份单个表
     */
    public function backuponly(){
        $parameter = input();
        $time = date('Ymd-His',time());
//        $this->backdb->setFile(['name'=>$time.$parameter['bname']])->backup($parameter['bname'], 0);
        $this->backdb->setFile()->backup($parameter['bname'], 0);
        returnResponse(200,$parameter['bname'].'表备份成功',$parameter['bname']);
    }

    /**
     * @author:xiaohao
     * @time:2019/11/22 13:53
     * @description:修复表
     */
    public function repairTable(){
        $parameter = input();
        $this->backdb->setFile()->repair($parameter['bname']);
        returnResponse(200,$parameter['bname'].'表修复成功',$parameter['bname']);
    }

    public function reduction(){
        $parameter = input();
        if($parameter['bname']){
            $name = date('Ymd-His'.time());
            $this->backdb->setFile([$name,'1'])->import(0);
            returnResponse(200,$parameter['bname'].'恢复成功',true);
        }
        returnResponse(100,$parameter['bname'].'恢复失败');
    }

    /**
     * @author:xiaohao
     * @time:2019/11/22 14:04
     * @return mixed
     * @description:备份文件列表
     */
    public function backFilesList(){
        return $this->fetch('back_files_list',['list'=>$this->backdb->fileList()]);
    }

    /**
     * @author:xiaohao
     * @time:2019/11/22 17:06
     * @throws \Exception
     * @description:删除备份
     */
    public function delBackup(){
        $parameter = input();
        if($parameter['bname']){
            $this->backdb->delFile($parameter['bname']);
            returnResponse(200,'删除成功',$parameter['bname']);
        }
        returnResponse(100,'参数不存在');
    }
}
