<?php
/**
 * 系统配文件
 * 所有系统级别的配置
 */
//PC端常量
define('TERMINAL','home');
//配置
return array(
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__'    => __ROOT__ . '/Public/Static',
        '__IMG__'       => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__'       => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__'        => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'sx2016_home',  //session前缀
    'COOKIE_PREFIX'  => 'sx2016_home_', //cookie前缀 避免冲突
    'VAR_SESSION_ID' => 'session_id',	//修复上传插件无法传递session_id的bug

    /* 互联登录 */
    'INTERCONNECT_CALLBACK' => 'http://域名?c=Plugins&a=execute&plugins=Interconnect&controller=Login&action=callback',

    /* 错误设置 */
    'ERROR_PAGE'            =>  '/e404',	// 错误定向页面

    /* 路由设置 */
    'URL_ROUTER_ON'         =>  true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       =>  array(
        'detail/:detail_cate_id/:item_id'   => 'Index/detail',          //高端洗护详细介绍页
        'confirm/:item_id'                  => 'Flow/confirmOrder',     //下单流程页
        //'login'                             => 'Login/login',           //登陆页
        'del_address/:addr_id'              => 'Center/delAddress',     //删除地址
        'orders/:status/:p'                 => 'OrderInfo/myOrders',    //我的订单
        'od/:order_id/:is_modify'           => 'OrderInfo/orderDetail',    //我的订单详情

        'pay/:order_id'                     => 'OrderPay/pay',

        'e404'                              => 'System/error404',
        'pay_error/:desc/:cause'            => 'System/payError',
        'pay_success/:msg'                  => 'System/paySuccess',
        'download'                          => 'System/download',

        'home/:reg_item_id/:item_id'             => 'Index/index',                       //首页
    ), // 默认路由规则 针对模块
    'URL_MAP_RULES'         =>  array(), // URL映射定义规则 静态路由

    'HOME_HOST' => 'http://meili-home.meili.com/',
);
