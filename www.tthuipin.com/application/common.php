<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Db;
use think\Request;


// 阿里云短信服务
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
// 应用公共文件

error_reporting(E_ERROR | E_WARNING | E_PARSE);
// 指定允许其他域名访问
//header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Origin: *');
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');
//允许cookie 跨域（跨域资源共享）
header('Access-Control-Allow-Credentials: true');


/**
 * @param string $phoneNumber
 * @user promise_1117
 * @time 2020/4/17/10:13
 * @description 阿里云短信接口
 */
function sendAliZjjAuthCode($phoneNumber = '15172441559')
{
    $accessKeyId = 'LTAI4G7Rqy64H3zygVFewC6Y';
    $accessSecret = 'q7KcshSCQejyPsJAxl8QSmjlsfSxd7'; //注意不要有空格
    $signName = '開心樂購'; //配置签名
    $templateCode = 'SMS_195575991';//配置短信模板编号


    AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
        ->regionId('cn-hangzhou')
        ->asDefaultClient();
    try {
        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            // ->scheme('https') // https | http
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => 'cn-hangzhou',
                    'PhoneNumbers' => $phoneNumber,//目标手机号
                    'SignName' => $signName,
                    'TemplateCode' => $templateCode,

                ],
            ])
            ->request();
        $opRes = $result->toArray();
        //print_r($opRes);
        if ($opRes && $opRes['Code'] == "OK"){
            //进行Cookie保存
            return $opRes;
        }
    } catch (ClientException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    } catch (ServerException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    }
}




/**
 * @author:xiaohao
 * @time:2019/10/09 23:03
 * @param $code
 * @param string $msg   返回code描述
 * @param array $data   返回数据array
 * @description:
 */
function returnResponse ($code,$msg = '',$data=array()){
    $retData = array(
        'code' => $code,
        'message' => $msg,
        'data' => $data?$data:'',
    );
    #错误时不显示data
    if($code != 200){
        unset($retData['data']);
    }
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($retData));
}

/**
 * @author:xiaohao
 * @time:2019/10/09 23:07
 * @param $password
 * @param string $str
 * @return bool|string
 * @description:返回密码
 */
function setPwd($password,$str="hanghaoshop"){
    return substr(md5($password.$str),0,16);
}

/**
 * @author:xiaohao
 * @time:2019/10/09 23:09
 * @return string
 * @description:设置登录token
 */
function setToken(){
    $str = md5(uniqid(md5(microtime(true)),true));
    $str = sha1($str);
    return $str;
}

/**
 * @author:xiaohao
 * @time:2019/10/11 15:28
 * @return mixed
 * @description:处理接受参数
 */
function getInput(){
    $parameter = input();
    $shift = array_shift($parameter);
    return $parameter;
}
/**
 * @author:xiaohao
 * @time:2019/10/11 15:20
 * @description:检测后端的token
 */
function checkTokenUserData(){
    $parameter = input();
    if(empty($parameter['token'])){
        returnResponse(100,'token不存在');
    }
    if(!empty($parameter['token'])){
        $map[] = ['token','eq',$parameter['token']];
        $info = Db('AdminUser')->field('user_id,user_name,token,headimg')->where($map)->find();
        if(empty($info)){
            returnResponse(100,'注意账号安全，异地登录！！！');
        }
    }
    return $info;
}

/**
 * @author:xiaohao
 * @time:2019/10/22 16:19
 * @return mixed
 * @description:通过session缓存获取用户信息
 */
function checkTokenUserDataSession($token){
//    $token = session('token');
    if(!empty($token)){
        $map[] = ['token','eq',$token];
        $info = Db('AdminUser')->field('user_id,user_name,name,password,token,headimg,last_login,last_ip,add_time,email,times,pid')->where($map)->find();
        return $info;
    }
}

/**
 * @author:xiaohao
 * @time:2019/10/10 15:36
 * @return mixed
 * @description:获取用户ip
 */
function getIp(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * @author:xiaohao
 * @time:2019/10/10 17:00
 * @return array
 * @description:图片上传
 */
function uploadImage(){
    $urlAll = '';
    $dir    = 'uploads';
    $absUrl = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER['SERVER_NAME'].'/'.$dir.'/';
    $files = request()->file('image');
    foreach($files as $file){
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $info = $file->move( 'uploads');
        if($info){
            $urlAll .= $absUrl.$info->getSaveName().'|';
        }else{
            returnResponse(100,$file->getError());
        }
    }
//    $imageurl = str_replace('/\\/','/',$urlAll);
    $url = substr($urlAll,0,strlen($urlAll)-1);
    return explode('|',$url);
}

/**
 * @author:xiaohao
 * @time:2019/10/14 10:33
 * @param $url 图片路径
 * @description:图片删除
 */
function deleteImage($url){
    if(file_exists($url)){
        unlink($url);
    }
}

/**
 * @author:xiaohao
 * @time:2019/10/14 14:50
 * @return float
 * @description:获取毫秒级的时间
 */
function msectime() {
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    return $msectime;
}

/**
 * @author:xiaohao
 * @time:2019/10/13 14:27
 * @return string
 * @description:生成唯一编号
 */
function setNumber(){
    $number = date('Ymdhis',time()).rand(100000,999999);
    if(empty($number)){
        $number = setNumber();
    }
    return $number;
}


function getBreadcrumbNav(){

}




