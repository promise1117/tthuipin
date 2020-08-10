<?php
namespace app\admin\validate;
use function PHPSTORM_META\type;
use think\Validate;
class Banner extends Validate
{

    protected $rule =   [
        'order'   => 'require',
        'name'    => 'require',
        'url'     => 'require',
        'img'     => 'require',
        'type'    => 'require',
    ];

    protected $message  =   [
        'order.require' => '排序必须',
        'name.require'  => '标题必须',
        'url.require'   => '链接必须',
        'img.require'   => '图片必须',
        'type.require'  => '类型必须，默认手机端',
    ];

    protected $scene = [
        'addEdit'   =>  ['order','name','url','img','type'],
    ];

}