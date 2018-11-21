<?php
/**
 * 菜单配置列表
 * group  父菜单 title标题名称  icon改组图标 class是否选中 默认为空 url链接地址 check_level验证级别 1控制器+方法
 * child 子菜单 同上
 */
return array(
    'MENUS' => array(
        array(
            'group' => array('title' => '主页', 'icon' => 'fa-home', 'url' => 'Index/index'),
        ),
        array(
            'group' => array('title'=>'店铺信息','icon'=>'fa-tasks'),
            '_child' => array(
                array('title'=>'店铺信息','url'=>'Shop/index'),
            )
        ),
        array(
            'group' => array('title'=>'预约管理','icon'=>'fa-tasks'),
            '_child' => array(
                array('title'=>'预约订单','url'=>'Bespeak/index'),
            )
        ),
        array(
            'group' => array('title'=>'服务订单','icon'=>'fa-tasks'),
            '_child' => array(
                array('title'=>'订单管理','url'=>'OrderInfoSer/index/type/1'),
                array('title'=>'退款单管理','url'=>'RefundOrderSer/index/type/2'),
                array('title'=>'取消单管理','url'=>'CancelOrderSer/index/type/3'),
            )
        ),
        array(
            'group' => array('title'=>'商城订单','icon'=>'fa-tasks'),
            '_child' => array(
                array('title'=>'订单管理','url'=>'OrderInfo/index/type/1'),
                array('title'=>'退款单管理','url'=>'RefundOrder/index/type/2'),
                array('title'=>'取消单管理','url'=>'CancelOrder/index/type/3'),
            )
        ),
    ),
    /*菜单映射设置 CURRENT设置当前控制器或者控制器+方法  MAPPING为映射的控制器  键值需一致  及可使访问当前控制器使映射控制器菜单高亮*/
    'CURRENT'   => array('Attribute'),
    'MAPPING'   => array('GoodsType')
);