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
            'group' => array('title'=>'首页订制','icon'=>'fa-columns'),
            '_child' => array(
//                array('title'=>'栏目管理','url'=>'Channel/index'),
                array('title'=>'版块管理','url'=>'Section/index'),
                array('title'=>'广告管理','url'=>'Advert/index'),
            )
        ),
        array(
            'group' => array('title'=>'内容管理','icon'=>'fa-list-alt'),
            '_child' => array(
                array('title'=>'平台支持银行','url'=>'Bank/index'),
                array('title'=>'文章分类','url'=>'ArticleCategory/index'),
                array('title'=>'文章列表','url'=>'Article/index'),
                array('title'=>'帮助文档管理','url'=>'HelpDoc/index'),
                array('title'=>'服务热词搜索','url'=>'GoodsHotSearch/index'),
                array('title'=>'商城退款原因','url'=>'RefundReason/index'),
                array('title'=>'预约取消原因','url'=>'BespeakReason/index'),
                array('title'=>'意见反馈','url'=>'Feedback/index'),
                array('title'=>'活动管理','url'=>'Activities/index'),
            )
        ),
        array(
            'group' => array('title'=>'用户管理','icon'=>'fa-user'),
            '_child' => array(
                array('title'=>'普通用户管理','url'=>'Member/index'),
                array('title'=>'用户地址管理','url'=>'Address/index'),
//                array('title'=>'用户评价','url'=>'GoodsComment/index'),
                array('title'=>'用户银行卡','url'=>'UserBankcard/index'),
//                array('title'=>'邀请记录','url'=>'InviteLog/index'),
            )
        ),
        array(
            'group' => array('title'=>'店铺管理','icon'=>'fa-columns'),
            '_child' => array(
                array('title'=>'开通地区','url'=>'ShopRegion/index'),
                array('title'=>'实体店铺','url'=>'Shop/index'),
                array('title'=>'店铺预约时间','url'=>'BespeakTime/index'),
            )
        ),
        array(
            'group' => array('title'=>'商品管理','icon'=>'fa-cart-plus'),
            '_child' => array(
                array('title'=>'商城商品分类','url'=>'GoodsCategory/index'),
                array('title'=>'商城商品','url'=>'Goods/index'),
                array('title'=>'服务商品分类','url'=>'ServiceCategory/index'),
                array('title'=>'服务商品','url'=>'Service/index'),
                array('title'=>'服务商品套餐','url'=>'Package/index'),
                array('title'=>'一卡通','url'=>'Card/index'),
                array('title'=>'团购商品','url'=>'ActivityGroupService/index'),
                //array('title'=>'商品类型管理','url'=>'GoodsType/index'),
//                array('title'=>'专题管理','url'=>'Special/index'),
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
        array(
            'group' => array('title'=>'财务管理','icon'=>'fa-credit-card'),
            '_child' => array(
                array('title'=>'资金记录','url'=>'CashTurnover/index/status/1'),
                array('title'=>'余额记录','url'=>'BalanceTurnover/index/status/1'),
                //array('title'=>'积分记录','url'=>'IntegralLog/index'),
//                array('title'=>'优惠券管理','url'=>'Coupon/index'),
//                array('title'=>'用户优惠券','url'=>'MemberCoupon/index'),
//                array('title'=>'充值卡管理','url'=>'RechargeCard/index'),
//                array('title'=>'充值码管理','url'=>'RechargeCode/index'),
                array('title'=>'提现管理','url'=>'Withdraw/index')
            )
        ),
//        array(
//            'group' => array('title'=>'发信管理','icon'=>'fa-comments'),
//            '_child' => array(
//                array('title'=>'信息模板','url'=>'SendTemplate/index'),
//                array('title'=>'发信记录','url'=>'SendLog/index'),
//                //array('title'=>'站内信','url'=>'SiteMessage/index')
//            )
//        ),
        array(
            'group' => array('title'=>'管理员操作','icon'=>'fa-group'),
            '_child' => array(
                array('title'=>'管理员信息','url'=>'Administrator/index'),
                //array('title'=>'分组权限','url'=>'AuthGroup/index'),
                //array('title'=>'行为信息','url'=>'Action/index'),
                //array('title'=>'行为日志','url'=>'ActionLog/index'),
            )
        ),
        array(
            'group' => array('title'=>'系统设置','icon'=>'fa-cogs'),
            '_child' => array(
                array('title'=>'APP版本更新','url'=>'ApkUpdate/index'),
                array('title'=>'网站设置','url'=>'ConfigSet/index/config_group/1'),
//                array('title'=>'配置管理','url'=>'Config/index'),
                array('title'=>'数据备份','url'=>'Database/export','check_level'=>1),
                array('title'=>'数据还原','url'=>'Database/import','check_level'=>1)
            )
        ),
//        array(
//            'group' => array('title'=>'数据备份/还原','icon'=>'fa-database'),
//            '_child' => array(
//                array('title'=>'数据备份','url'=>'Database/export','check_level'=>1),
//                array('title'=>'数据还原','url'=>'Database/import','check_level'=>1)
//            )
//        ),
        array(
            'group' => array('title'=>'插件扩展','icon'=>'fa-sitemap'),
            '_child' => array(
                array('title'=>'插件管理','url'=>'Plugins/index'),
                array('title'=>'钩子管理','url'=>'Hooks/index')
            )
        ),
    ),

    /*菜单映射设置 CURRENT设置当前控制器或者控制器+方法  MAPPING为映射的控制器  键值需一致  及可使访问当前控制器使映射控制器菜单高亮*/
    'CURRENT'   => array('Attribute'),
    'MAPPING'   => array('GoodsType')
);