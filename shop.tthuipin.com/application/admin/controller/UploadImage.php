<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class UploadImage extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->check = checkTokenUserData();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/10 17:45
     * @description:图片上传
     */
    public function uploadImage(){
        $info = uploadImage();
        returnResponse(200,'上传成功',$info);
    }

//http://www.thinkphpshop.cn/uploads/20191013\\c3cc886ca6d1f2601f725262046009b1.jpg

/*
 {
    "code": 200,
    "message": "上传成功",
    "data": [
        "http://www.thinkphpshop.cn/uploads/20191013\\25de1d11f546512841a694f312a6154c.jpg",
        "http://www.thinkphpshop.cn/uploads/20191013\\244b6b744b16125f650e45e6d653b500.jpg"
    ]
}
{
	"code": 200,
	"message": "上传成功",
	"data": [
		"http://www.thinkphpshop.cn/uploads/20191013\\e558173a48c8df20235c5c0411d0b5da.jpg",
		"http://www.thinkphpshop.cn/uploads/20191013\\987ab352bc4d929bf264b06e4e3cd8a9.jpg"
	]
}

图片删除未完成
*/
    public function deleteImage(){
        $parameter = input();
        $info = deleteImage($parameter);
        return $info;
    }

}
