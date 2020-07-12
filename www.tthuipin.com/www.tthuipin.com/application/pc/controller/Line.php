<?php

namespace app\pc\controller;



use app\pc\controller\Base;



class Line extends Base{

    public function __construct()

    {

        parent::__construct();

    }



    public function index()

    {

        return $this->fetch();

    }

    public function yixinClayLine()

    {

        return $this->fetch();

    }

    public function teaLine()

    {

        return $this->fetch();

    }


    public function add()

    {

        $telphone = input('post.telphone')?input('post.telphone'):'';

        $demand= input('post.demand')?input('post.demand'):[];





        $data = [];

        $location = $this->getApiData('http://mobsec-dianhua.baidu.com/dianhua_api/open/location?tel='.$telphone);

        $location = $location['response'][$telphone]['location'];

        $user_agent = $this->userBrowser(request()->header('user-agent'));



        if($telphone){

            $data['code'] = 0;

            $data['msg'] = '添加成功';

            $data['res']['telphone'] = $telphone;

            $data['res']['name'] = '';

            $data['res']['browser'] = $user_agent;

            $data['res']['addtime'] = request()->time();

            $data['res']['IP'] = request()->server('REMOTE_ADDR');

            $data['res']['ownership'] = $location;

            $data['res']['demand'] = $demand?$demand:[];



//            dump($data['res']['telphone']);die;

            Db('line')->insert($data['res']);



        }else{

            $data['code'] = 1;

            $data['msg'] = '添加失敗';

            $data['res'] = [];



        }



        return json($data);

    }



    public function getApiData($url,$way='true'){

        $ch = curl_init();

        //设置选项，包括URL

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_POST, $way); //post提交方式

        //执行并获取HTML文档内容

        $output = curl_exec($ch);

        //释放curl句柄

        curl_close($ch);

        //转换json数据

        $data = json_decode($output,true);

        //返回数据

        return $data;

    }





    public function userBrowser($user_OSagent='') {



        if (strpos($user_OSagent, "Maxthon") && strpos($user_OSagent, "MSIE")) {

            $visitor_browser = "Maxthon(Microsoft IE)";

        } elseif (strpos($user_OSagent, "Maxthon 2.0")) {

            $visitor_browser = "Maxthon 2.0";

        } elseif (strpos($user_OSagent, "Maxthon")) {

            $visitor_browser = "Maxthon";

        } elseif (strpos($user_OSagent, "MSIE 9.0")) {

            $visitor_browser = "MSIE 9.0";

        } elseif (strpos($user_OSagent, "MSIE 8.0")) {

            $visitor_browser = "MSIE 8.0";

        } elseif (strpos($user_OSagent, "MSIE 7.0")) {

            $visitor_browser = "MSIE 7.0";

        } elseif (strpos($user_OSagent, "MSIE 6.0")) {

            $visitor_browser = "MSIE 6.0";

        } elseif (strpos($user_OSagent, "MSIE 5.5")) {

            $visitor_browser = "MSIE 5.5";

        } elseif (strpos($user_OSagent, "MSIE 5.0")) {

            $visitor_browser = "MSIE 5.0";

        } elseif (strpos($user_OSagent, "MSIE 4.01")) {

            $visitor_browser = "MSIE 4.01";

        } elseif (strpos($user_OSagent, "MSIE")) {

            $visitor_browser = "MSIE 较高版本";

        } elseif (strpos($user_OSagent, "NetCaptor")) {

            $visitor_browser = "NetCaptor";

        } elseif (strpos($user_OSagent, "Netscape")) {

            $visitor_browser = "Netscape";

        } elseif (strpos($user_OSagent, "Chrome")) {

            $visitor_browser = "Chrome";

        } elseif (strpos($user_OSagent, "Lynx")) {

            $visitor_browser = "Lynx";

        } elseif (strpos($user_OSagent, "Opera")) {

            $visitor_browser = "Opera";

        } elseif (strpos($user_OSagent, "Konqueror")) {

            $visitor_browser = "Konqueror";

        } elseif (strpos($user_OSagent, "Mozilla/5.0")) {

            $visitor_browser = "Mozilla";

        } elseif (strpos($user_OSagent, "Firefox")) {

            $visitor_browser = "Firefox";

        } elseif (strpos($user_OSagent, "U")) {

            $visitor_browser = "Firefox";

        } else {

            $visitor_browser = "其它";

        }

        return $visitor_browser;

    }





}