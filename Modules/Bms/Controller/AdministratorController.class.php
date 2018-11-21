<?php
namespace Bms\Controller;

/**
 * Class AdministratorController
 * @package Bms\Controller
 * 管理员控制器
 */
class AdministratorController extends BmsBaseController {

    /**
     * 新增时 关联数据
     */
    protected function getAddRelation() {
        //获取管理员组列表
        $groups = self::$logicObject->getGroupList(I('request.'));
        $this->assign('groups',$groups);
    }

    /**
     * 修改时 关联数据
     */
    protected function getUpdateRelation() {
        //获取管理员组列表
        $groups = self::$logicObject->getGroupList(I('request.'));
        $this->assign('groups',$groups);
    }

    /**
     * 修改密码
     */
    function rePass() {
        if(!IS_POST) {
            $this->assign('breadcrumb_nav', L('_REPASS_BREADCRUMB_NAV_'));
            $this->display('rePass');
        } else {
            $result = self::$logicObject->rePass(I('request.'));
            if ($result) {
                $this->success(self::$logicObject->getLogicInfo(), cookie('__forward__'));
            } else {
                $this->error(self::$logicObject->getLogicInfo());
            }
        }
    }
}
