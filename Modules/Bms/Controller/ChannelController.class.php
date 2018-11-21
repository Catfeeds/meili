<?php
namespace Bms\Controller;

/**
 * Class ChannelController
 * @package Bms\Controller
 * 首页栏目控制器
 */
class ChannelController extends BmsBaseController {

    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        //跳转规则
        $this->assign('target_rules',C('TARGET_RULES'));
    }

    /**
     * 新添时关联数据
     */
    function getAddRelation() {
        //跳转规则
        $this->assign('target_rules',C('TARGET_RULES'));
    }
}