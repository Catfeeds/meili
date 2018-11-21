<?php
namespace Bms\Controller;

/**
 * Class ConfigSetController
 * @package Bms\Controller
 * 系统配置值 控制器
 */
class ConfigSetController extends BmsBaseController {

    /**
     * 首页关联数据
     */
    function getIndexRelation() {
        $this->assign('groups',C('CONFIG_GROUP_LIST'));
    }
}