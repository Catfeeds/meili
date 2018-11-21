<?php
namespace Bms\Controller;

/**
 * Class SectionController
 * @package Bms\Controller
 * 首页版块控制器
 */
class SectionController extends BmsBaseController {

    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        //跳转规则
        $this->assign('target_rules',C('TARGET_RULES_C'));
    }

    /**
     * 新添时关联数据
     */
    function getAddRelation() {
        //跳转规则
        $this->assign('target_rules',C('TARGET_RULES_C'));
    }
}