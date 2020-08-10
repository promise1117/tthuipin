<?php
namespace app\index\validate;
use think\Validate;
class Order extends Validate
{

    protected $rule =   [
        'gid'            => 'require',
//        'goods_no'       => 'require',
        'description'    => 'require',
        'payable_amount' => 'require',
        'order_amount'   => 'require',
        'accept_name'    => 'require',
        'telphone'       => 'require',
        'email'          => 'require',
        'address'        => 'require',
        'lineid'         => 'require',
        'message'        => 'require',
        'pay_type'       => 'require',
    ];

    protected $message  =   [
        'gid.require'            => '产品id必须',
//        'goods_no.require'       => '产品编号必须',
        'description.require'    => '产品描述必须',
        'payable_amount.require' => '应付商品总金额必须',
        'order_amount.require'   => '订单金额必须',
        'accept_name.require'    => '姓名必须',
        'telphone.require'       => '电话必须',
        'email.require'          => '邮箱必须',
        'address.require'        => '地址必须',
        'lineid.require'         => 'lineid 必须',
        'message.require'        => '备注信息必须',
        'pay_type.require'       => '支付方式必须',
    ];

    protected $scene = [
        'add'   =>  ['gid,description,order_amount,accept_name,telphone,address'],
    ];

}
