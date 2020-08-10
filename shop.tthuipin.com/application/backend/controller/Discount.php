<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use http\Env\Request;

/**
 * Class Discount
 * @package app\backend\controller
 * 折价码管理
 */
class Discount extends Base
{
    public function __construct(){
        parent::__construct();
        $this->discount_code = Db('discount_code');
//        $this->check = Validate('Category');
        $this->checkTokenSession = $this->getUserInfoSession();
    }


    /**
     * @return mixed
     * @user promise_1117
     * @time 2020/4/28/10:55
     * @description 所有商品折价码列表
     */
    public function getList(){
        $parameter = input();
        $map = array();
        empty($parameter['code']) ? $parameter['code'] :$map[] = ['code','like','%'.$parameter['code'].'%'];
        empty($parameter['listrow'])  ? $parameter['listrow'] == '16' : $parameter['listrow'];
//        $map[] = array();
        $sort = ['id'=>'desc'];
        $getlist = $this->discount_code
            ->where($map)
            ->order($sort)
            ->paginate($parameter['listrow']);

//        dump($getlist);die;
        $this->assign([
            'codelist' => $getlist,
        ]);
        return $this->fetch();
    }


    /**
     * @return mixed
     * @user promise_1117
     * @time 2020/4/28/11:36
     * @description 折价码添加
     */
    public function add(){

        $code = substr(strtoupper(md5('hanghao'.time())),0,9);
        $parameter = input();
        $data = [
            'code' => $parameter['code'],
            'cost_price' => $parameter['cost_price'],
            'create_time' => time(),
        ];

        if($parameter){
            $info = $this->discount_code->insert($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $this->assign([
           'code'=>$code
        ]);
        return $this->fetch();
    }

    /**
     * @user promise_1117
     * @time 2020/4/28/11:35
     * @description 删除折价码
     */
    public function deleteCode(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        if($id){
            $info = $this->discount_code->where('id',$id)->delete();
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
        returnResponse(100,'网路拥挤，稍后再试');
    }



    public function pcBanner()
    {
        return $this->fetch();
    }

    public function pcBannerList()
    {
        $data = Db('banner')->where('type',1)->select();
        if($data){
            $res['code'] = 0;
            $res['msg'] = '請求成功';
            $res['count'] = count($data);
            $res['data'] = $data;
        }else{
            $res['code'] = 1;
            $res['msg'] = '暂无数据,去添加几条吧!';
            $res['count'] = count($data);
            $res['data'] = $data;
        }

        return $res;
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

    public function pcBannerDel()
    {
        $id = input('post.id');
        if($id){
            $rsp = Db('banner')->where('id',$id)->delete();
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
}
