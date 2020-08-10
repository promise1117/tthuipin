<?php
namespace app\index\model;
use app\index\model\Base;
class Order extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->order = Db('Order');
        $this->check = Validate('Order');
    }
//`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//`order_no` varchar(20) NOT NULL COMMENT '订单号',
//`user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
//`pay_type` int(11) NOT NULL COMMENT '用户支付方式ID,当为0时表示货到付款',
//`distribution` int(11) DEFAULT NULL COMMENT '用户选择的配送ID',
//`status` tinyint(1) DEFAULT '1' COMMENT '订单状态 1生成订单,2支付订单,3取消订单(客户触发),4作废订单(管理员触发),5完成订单,6退款(订单完成后),7部分退款(订单完成后)',
//`pay_status` tinyint(1) DEFAULT '0' COMMENT '支付状态 0：未支付; 1：已支付;',
//`distribution_status` tinyint(1) DEFAULT '0' COMMENT '配送状态 0：未发送,1：已发送,2：部分发送',
//`accept_name` varchar(20) NOT NULL COMMENT '收货人姓名',
//`postcode` varchar(50) DEFAULT NULL COMMENT '邮编',
//`telphone` varchar(20) DEFAULT NULL COMMENT '联系电话',
//`country` int(11) DEFAULT NULL COMMENT '国ID',
//`province` int(11) DEFAULT NULL COMMENT '省ID',
//`city` int(11) DEFAULT NULL COMMENT '市ID',
//`area` int(11) DEFAULT NULL COMMENT '区ID',
//`address` varchar(250) DEFAULT NULL COMMENT '收货地址',
//`mobile` varchar(20) DEFAULT NULL COMMENT '手机',
//`payable_amount` decimal(15,2) DEFAULT '0.00' COMMENT '应付商品总金额',
//`real_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '实付商品总金额(会员折扣,促销规则折扣)',
//`payable_freight` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '总运费金额',
//`real_freight` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '实付运费',0
//`pay_time` datetime DEFAULT NULL COMMENT '付款时间',
//`send_time` datetime DEFAULT NULL COMMENT '发货时间',
//`create_time` datetime DEFAULT NULL COMMENT '下单时间',
//`completion_time` datetime DEFAULT NULL COMMENT '订单完成时间',
//`invoice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发票：0不索要1索要',
//`if_del` tinyint(1) DEFAULT '0' COMMENT '是否删除1为删除',
//`invoice_info` text COMMENT '发票信息JSON数据',
//`promotions` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '促销优惠金额和会员折扣',
//`discount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '订单折扣或涨价',
//`order_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
//`type` varchar(50) NOT NULL DEFAULT '' COMMENT '默认:普通,groupon:团购,time:限时抢购,costpoint:积分兑换',
//`is_checkout` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否给商家结算货款 0:未结算 1:已结算',
    public function add($parameter){
        if(!$this->check->scene('add')->check($parameter)){
            returnResponse(100,$this->check->getError());
        }
        $data = [
            'good_id'    => $parameter['gid'],  //商品id
//            'gunmbet' => $parameter['goods_no'], //商品的编号
            'gdescription' => $parameter['description'], //商品描述
            'discount' => $parameter['discount'], //折扣码
            'order_no' => setNumber(),  //生成订单号
            'payable_amount' => $parameter['payable_amount'], //应付商品总金额
            'order_amount' => $parameter['order_amount'],  //订单金额
//            'status' => '1',//订单状态 1生成订单,2支付订单,3取消订单(客户触发),4作废订单(管理员触发),5完成订单,6退款(订单完成后),7部分退款(订单完成后)',
            'accept_name' => $parameter['accept_name'], //姓名
            'telphone' => $parameter['telphone'],   //电话
            'email' => $parameter['email'],   //邮箱
            'address' => $parameter['address'], //地址
            'lineid' => $parameter['lineid'], //lineid
            'message' => $parameter['message'], //留言
            'pay_type' => $parameter['pay_type'], //支付方式
            'create_time' => time(),
        ];
        $res = $this->order->insert($data);
        if($res){
            returnResponse(200,'提交成功',$res);
        }
    }

}
