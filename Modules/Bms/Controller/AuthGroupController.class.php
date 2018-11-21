<?php
namespace Bms\Controller;

/**
 * Class AuthGroupController
 * @package Bms\Controller
 * 管理员分组 权限控制器
 */
class AuthGroupController extends BmsBaseController {

    /**
     * 访问授权列表
     */
    function access() {
        //权限验证
        $this->checkRule(self::$rule);
        $list = self::$logicObject->getAccess(I('get.'));
        $this->assign('list',$list['all_rules']);
        $this->assign('rules',$list['have_rules']);
        $this->assign('breadcrumb_nav', L('_ACCESS_BREADCRUMB_NAV_'));
        $this->display('access');
    }
}
