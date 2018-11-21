<?php
namespace Bms\Model;

/**
 * Class AuthRuleModel
 * @package Bms\Model
 * 权限规则 模型
 */
class AuthRuleModel extends BmsBaseModel {

    /**
     * 检验类型
     */
    const RULE_URL  = 1;
    const RULE_MAIN = 2;

    /**
     * @param $param
     * @return mixed
     */
    function getList($param = array()) {
        return $this->field('id,name,title,parent_id')->where($param['where'])->select();
    }
}