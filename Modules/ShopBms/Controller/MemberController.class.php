<?php
namespace ShopBms\Controller;

/**
 * Class MemberController
 * @package Bms\Controller
 * 会员控制器
 */
class MemberController extends BmsBaseController {

    /**
     * 导出excel文件
     */
    function export() {
        $this->checkRule(self::$rule);
        $result = D('Member','Logic')->export(I('post.'));
        if(!$result) {
            $this->error(D('Member','Logic')->getLogicInfo());
        }
    }

    /**
     * 导入数据
     */
    function import() {
        $this->checkRule(self::$rule);
        if(!IS_POST) {
            $this->assign('breadcrumb_nav', L('_IMP_BREADCRUMB_NAV_'));
            $this->display('import');
        } else {
            $result = D('Member','Logic')->import(I('post.'));
            if($result) {
                $this->success('数据导入成功！', cookie('__forward__'));
            } else {
                $this->error(D('Member','Logic')->getLogicInfo());
            }
        }
    }

    /**
     * 调整方法
     */
    function adjust() {
        $this->checkRule(self::$rule);
        if(!IS_POST) {
            $this->assign('breadcrumb_nav', L('_ADJUST_BREADCRUMB_NAV_'));
            $this->assign('info', M('Member')->where(array('id'=>I('request.ids')))->field('balance,integral')->find());
            $this->display('adjust');
        } else {
            $result = D('MsC/Adjust','Logic')->doAdjust(I('request.'));
            if(!$result)
                $this->error(D('MsC/Adjust','Logic')->getLogicInfo());
            else
                $this->success('操作成功！', cookie('__forward__'));
        }
    }
}