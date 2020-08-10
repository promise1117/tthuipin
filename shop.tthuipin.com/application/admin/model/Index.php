<?php
namespace app\admin\model;
use app\admin\model\Base;
class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->checkToken = $this->checkTokenUserDatas();
    }


}
