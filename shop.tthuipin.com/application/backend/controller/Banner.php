<?php
namespace app\backend\controller;
use app\backend\controller\Base;
use http\Env\Request;

class Banner extends Base
{
    public function __construct(){
        parent::__construct();
        $this->banner = Db('Banner');
//        $this->check = Validate('Category');
        $this->checkTokenSession = $this->getUserInfoSession();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/25 16:25
     * @return mixed
     * @description:banner列表
     */
    public function getList(){
        $parameter = input();
        empty($parameter['name']) ? $parameter['name'] :$map[] = ['name','like','%'.$parameter['name'].'%'];
        empty($parameter['listrow'])  ? $parameter['listrow'] == '16' : $parameter['listrow'];
        $map[] = ['is_show','eq','0'];
        $sort = ['order'=>'desc','id'=>'asc'];
        $getlist = $this->banner
            ->where($map)
            ->order($sort)
            ->paginate($parameter['listrow']);
        $this->assign([
            'banerlsit' => $getlist,
        ]);
        return $this->fetch();
    }


    /**
     * @author:xiaohao
     * @time:2019/10/25 16:34
     * @return mixed
     * @description:banner添加
     */
    public function add(){
        $parameter = input();
        $data = [
            'name' => $parameter['name'],
            'url' => $parameter['urll'],
            'show_in_nav' => $parameter['show_in_nav'],
            'img' => $parameter['files'],
            'order' => $parameter['order'],
            'addtime' => time(),
        ];
        if($parameter){
            $info = $this->banner->insert($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
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
        $data = [
            'name' => $parameter['name'],
            'url' => $parameter['urll'],
            'show_in_nav' => $parameter['show_in_nav'],
            'img' => $parameter['files'],
            'order' => $parameter['order'],
            'type' => $parameter['type'],
            'updatetime' => time(),
        ];
        if($parameter['id']){
            $info = $this->banner->where('id',$parameter['id'])->update($data);
            if($info){
                returnResponse('200','true',$info);
            }
            returnResponse('100','false');
        }
        $info = $this->banner->where('id',$parameter['eid'])->find();
        $this->assign([
            'info' => $info,
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/25 15:52
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:显示隐藏
     */
    public function show(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        $account = Db('Banner')->field('show_in_nav')->where('id',$id)->find();
        if($account['show_in_nav']==1){
            $info = Db('Banner')->where('id',$id)->update(['show_in_nav'=>0]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }else{
            $info = Db('Banner')->where('id',$id)->update(['show_in_nav'=>1]);
            if($info){
                returnResponse(200,'true',$info);
            }
            returnResponse(100,'false');
        }
    }

    /**
     * @author:xiaohao
     * @time:Times
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:banner删除
     */
    public function deleteBanner(){
        $parameter = input();
        $id = intval(trim($parameter['id']));
        if($id){
            $info = $this->banner->where('id',$id)->update(['is_show'=>'1']);
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
