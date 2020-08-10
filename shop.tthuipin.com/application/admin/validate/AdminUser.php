<?php
namespace app\admin\validate;
use think\Validate;
class AdminUser extends Validate
{
    protected $rule =   [
        'username' => 'require|mobile',
        'password' => 'require|chsAlphaNum',
        'uid'      => 'require|number',
        'sort'     => 'number',
    ];

    protected $message  =   [
        'username.require'     => '用户名必须',
        'username.mobile'      => '用户名称违规',
        'password.require'     => '密码必须',
        'password.chsAlphaNum' => '密码只能是汉字、字母和数字',
        'uid.require'        => '参数错误',
        'uid.number'         => '参数不合法',
        'sort.number'        => '必须是数字',
    ];

    protected $scene = [
        'login'   =>  ['username','password'],
        'addEdit' =>  ['username','password','sort'],
        'allow'   =>  ['uid'],
    ];

}