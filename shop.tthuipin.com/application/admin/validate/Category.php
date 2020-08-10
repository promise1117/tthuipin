<?php
namespace app\admin\validate;
use think\Validate;
class Category extends Validate
{
    protected $rule =   [
        'cat_name' => 'require',
    ];

    protected $message  =   [
        'cat_name.require'     => '分类名必须',
    ];

    protected $scene = [
        'addEdit'   =>  ['cat_name'],
    ];

}