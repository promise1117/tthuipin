<?php
namespace app\admin\validate;
use think\Validate;
class Brand extends Validate
{
    protected $rule =   [
        'brand_name' => 'require',
        'brand_logo' => 'require',
        'brand_desc' => 'require',
        'site_url'   => 'require',
        'sort_order' => 'require',
        'is_show'    => 'require',
    ];

    protected $message  =   [
        'brand_name.require' => '品牌名称必须',
        'brand_logo.require' => 'logo必须',
        'brand_desc.require' => '描述必须',
        'site_url.require'   => '链接必须',
        'sort_order.require' => '排序必须',
        'is_show.require'    => '是否展示默认为展示',
    ];

    protected $scene = [
        'addEdit'   =>  ['brand_name'],
    ];

}