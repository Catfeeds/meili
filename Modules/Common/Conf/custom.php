<?php
/**
 * 本项目自定义配置
 */
return array(
    'URGE_INTERVAL'         => 1800,  //可催单的时间间隔
    'INTEGRAL_DED_PRO'      => 100,  //积分抵现比例 1:INTEGRAL_DED_PRO
    'ENABLE_INTEGRAL_PRO'   => 10,  //每单-积分最多可抵比例(按订单总额)
    'PROFIT_FEE'    => '5%', //邀请收益

    'REFUND_DELAY' => 10, //退款通道延续时间(天)  签收时间+延续
    'COMMENT_INTEGRAL' => 10,  //评价赠积分
    'FINISH_ORDER_INTEGRAL' => 10, //完成订单赠积分

    'CANCEL_ORDER' => 1800,  //多长时间未支付 自动取消

    'WITHDRAW_DESC' => '预计3个工作日到账',  //提现描述

    'THDZ'=>'http://www.wenfenglipin.com/MobileClient/ValidateVoucher',
    'THJL'=>'http://www.wenfenglipin.com/MobileClient/ValidateVoucher',

    /** 支付回调地址 **/
    'ORDER_ALI_API_NOTIFY_URL' => 'http://wenf-api.toocms.com/OrderPay/aliCallback', //订单支付宝支付用户APP异步回调通知地址
    'ORDER_ALI_HOME_RETURN_URL' => 'http://wenf-home.toocms.com/index.php/OrderPay/aliCallback', //订单支付宝支付网站跳转同步通知页面路径
    'ORDER_ALI_HOME_NOTIFY_URL' => 'http://wenf-home.toocms.com/OrderPay/aliCallback', //订单支付宝支付网站异步回调通知地址

    'ORDER_WX_API_NOTIFY_URL' => 'http://wenf-api.toocms.com/OrderPay/wxCallback', //订单微信支付用户APP异步回调通知地址
    'ORDER_WX_HOME_NOTIFY_URL' => 'http://wenf-home.toocms.com/OrderPay/wxCallback', //订单微信支付扫码异步回调通知地址
    'ORDER_WX_WAP_NOTIFY_URL' => 'http://wenf-wap.toocms.com/OrderPay/wxCallback', //订单微信版支付异步回调通知地址

    /** 支付宝退款回调 **/
    'REFUND_ALI_NOTIFY_URL' => 'http://wenf-bms.toocms.com/RefundCallback/aliRefundCallback',
    
	//liy,充值回调地址
	'RECHARGE_ALI_NOTIFY_URL' => 'http://wenf-api.toocms.com/Pay/notifyUrlS', //订单支付宝支付服务器异步回调通知地址
	'RECHARGE_ALI_RETURN_URL' => 'http://', //订单支付宝支付页面跳转同步通知页面路径
		
	'RECHARGE_WX_NOTIFY_URL' => 'http://wenf-api.toocms.com/Pay/weiXinNotifyUrlS', //订单微信支付服务器异步回调通知地址
	'RECHARGE_WX_RETURN_URL' => 'http://', //订单微信支付页面跳转同步通知页面路径

    'WX_APP_ID' => 'wxbddbd138b27f303d',
    'WX_SECRET' => 'd20d5bc2fcc10820136ce286c3683265',

    'TS' => 1,
);
