<?php
namespace Bms\Controller;

/**
 * Class SendTemplateController
 * @package Bms\Controller
 * 发信模板控制器
 */
class SendTemplateController extends BmsBaseController {

    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        $this->assign('types',C('SEND_TEMPLATE_TYPES'));
        //跳转规则
        $this->assign('target_rules',D('SendTemplate', 'Logic')->getPushTargetRules());
    }

    /**
     * 新添时关联数据
     */
    function getAddRelation() {
        $this->assign('types',C('SEND_TEMPLATE_TYPES'));
        //跳转规则
        $this->assign('target_rules',D('SendTemplate', 'Logic')->getPushTargetRules());
    }
}