<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use http\Env\Request;

class Pot extends Base
{
    public function __construct(){
        parent::__construct();
        $this->banner = Db('Banner');
//        $this->check = Validate('Category');
        $this->checkTokenSession = $this->getUserInfoSession();
    }

    public function pot()
    {
        return $this->fetch();
    }

    public function potList()
    {
        $parameter = input();

        //搜索条件
        empty($parameter['starttime']) ? $parameter['starttime'] = '' :'';
        empty($parameter['endtime'])   ? $parameter['endtime'] = strtotime("+2hours") :'';
        empty($parameter['keywords'])      ? $parameter['keywords']      :$map[] = ['name','like',$parameter['keywords']]; //位置不能动，做权限删除

        $map[] = ['addtime','between time',[$parameter['starttime'],$parameter['endtime']]];//时间段查询

        $page=input('page')?input('page'):1;
        $limit=input('limit');

        $data = Db('pot')->where($map)->limit(($page-1)*$limit,$limit)->select();
        if($data){
            $res['code'] = 0;
            $res['msg'] = '請求成功';
            $res['count'] = count(Db('pot')->where($map)->select());
            $res['data'] = $data;
        }else{
            $res['code'] = 1;
            $res['msg'] = '暂无数据,去添加几条吧!';
            $res['count'] = count(Db('pot')->where($map)->select());
            $res['data'] = $data;
        }
        return json($res);
    }

    public function changeStatus()
    {
        $id = input('get.id');
        $status = input('get.status');
        $res = Db('banner')->where('id',$id)->update(['is_show'=>$status]);
        return $res;
    }

    public function pcBannerEdit()
    {
        $param = input('post.');
        $id = $param['id'];
        $res['img'] = $param['img'];
        $res['name'] = $param['name'];
        $res['url'] = $param['url'];
        $res['is_show'] = $param['is_show'] == 'on'?0:1;
        $res['type'] = 1;
        $res['addtime'] = time();

        $data = Db('banner')->where('id',$id)->update($res);

        header('location:'.getenv("HTTP_REFERER"));
    }

    public function pcBannerAdd()
    {
        $param = input('post.');

        $res['img'] = $param['img'];
        $res['name'] = $param['name'];
        $res['url'] = $param['url'];
        $res['is_show'] = $param['is_show'] == 'on'?0:1;
        $res['type'] = 1;
        $res['addtime'] = time();

        $data = Db('banner')->insert($res);

        header('location:'.getenv("HTTP_REFERER"));
    }

    public function pcUploadImg()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move('public/uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
//            echo $info->getExtension();
//            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//            echo $info->getSaveName();
//            // 输出 42a79759f284b767dfcb2a0197904287.jpg
//            echo $info->getFilename();
            $src = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/public/uploads/'.$info->getSaveName();
            $data = ['code'=>0,'msg'=>'上传成功','data'=>['src'=>$src]];

            return $data;
        }else{
            // 上传失败获取错误信息
            echo $file->getError();

            $data = ['code'=>1,'msg'=>'上传失败','data'=>['src'=>$file->getError()]];

            return $data;
        }
    }

    public function potDel()
    {
        $id = input('post.id');
        if($id){
            $rsp = Db('pot')->where('id',$id)->delete();
            $res['code'] = 0;
            $res['msg'] = '删除成功';
            $res['data'] = $rsp;
        }else{
            $res['code'] = 1;
            $res['msg'] = '删除失败';
            $res['data'] = '';
        }

        return $res;
    }

    public function potLink(){
        return $this->fetch();
    }
}
