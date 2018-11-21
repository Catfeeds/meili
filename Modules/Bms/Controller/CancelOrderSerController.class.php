<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/15
 * Time: 13:41
 */

namespace Bms\Controller;


class CancelOrderSerController extends BmsBaseController
{
    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //逻辑层对象
        self::$logicObject = D('OrderInfoSer', 'Logic');
    }
}