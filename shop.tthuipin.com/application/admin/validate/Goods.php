<?php
namespace app\admin\validate;
use think\Validate;
class Goods extends Validate
{
    protected $rule =   [
        'name' => 'require',
    ];

    protected $message  =   [
        'name.require'     => '产品名称不能为空',
    ];

    protected $scene = [
        'addEdit'   =>  ['name'],
    ];

}