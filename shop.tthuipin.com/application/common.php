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
        $info = Db('AdminUser')->field('user_id,user_name,role_id,name,password,token,headimg,last_login,last_ip,add_time,email,times,pid')->where($map)->find();
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

function getRandomString($len, $chars=null)
{
    if (is_null($chars)) {  
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}


function getBreadcrumbNav(){

}



/**
 * @param string $phoneNumber
 * @return mixed
 * @user promise_1117
 * @time 2020/4/26/15:51
 * @description 阿里云短信厂商配货状态修改
 */

function distribution($phoneNumber)
{
    $accessKeyId = 'LTAI4G7Rqy64H3zygVFewC6Y';
    $accessSecret = 'q7KcshSCQejyPsJAxl8QSmjlsfSxd7'; //注意不要有空格
    $signName = '開心樂購'; //配置签名
//    $templateCode = 'SMS_188992132';//配置短信模板编号
//    $templateCode = 'SMS_191800570';//配置短信模板编号
    $templateCode = 'SMS_195576067';//配置短信模板编号


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
 * @param string $phoneNumber
 * @return mixed
 * @user promise_1117
 * @time 2020/4/26/15:56
 * @description 阿里云短信国际运输状态修改
 */
function transport($phoneNumber = '15172441559')
{
    $accessKeyId = 'LTAI4G7Rqy64H3zygVFewC6Y';
    $accessSecret = 'q7KcshSCQejyPsJAxl8QSmjlsfSxd7'; //注意不要有空格
    $signName = '開心樂購'; //配置签名
//    $templateCode = 'SMS_189026750';//配置短信模板编号
//    $templateCode = 'SMS_191815500';//配置短信模板编号
    $templateCode = 'SMS_195586014';//配置短信模板编号


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
 * @param string $phoneNumber
 * @return mixed
 * @user promise_1117
 * @time 2020/4/26/15:57
 * @description 阿里云短信抵达台湾短信提醒
 */
function arrival($phoneNumber = '15172441559')
{
    $accessKeyId = 'LTAI4G7Rqy64H3zygVFewC6Y';
    $accessSecret = 'q7KcshSCQejyPsJAxl8QSmjlsfSxd7'; //注意不要有空格
    $signName = '開心樂購'; //配置签名
//    $templateCode = 'SMS_189016751';//配置短信模板编号
//    $templateCode = 'SMS_191830545';//配置短信模板编号
    $templateCode = 'SMS_195720161';//配置短信模板编号


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


// 抓取1688產品
function catchData($url) {
    header("Content-type: text/html; charset=gb2312");
    $headers=[
        "Accept: application/json, text/javascript, */*; q=0.01",
        "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
        "Origin:https://detail.1688.com",
        "Referer: $url",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36",

    ];
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);//指定头部参数
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    //重要！
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)"); //模拟浏览器代理

    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
    return $data;
}
