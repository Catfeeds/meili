<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/12
 * Time: 21:27
 */

namespace Bms\Controller;


class ActivitiesController extends BmsBaseController
{
    function getUpdateRelation() {
        $this->assign('target_rules',C('TARGET_RULES_C'));
    }

    function getAddRelation() {
        $this->assign('target_rules',C('TARGET_RULES_C'));
    }
}