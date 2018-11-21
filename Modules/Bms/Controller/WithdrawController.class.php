<?php
namespace Bms\Controller;

/**
 * Class WithdrawController
 * @package Bms\Controller
 * 提现管理控制器
 */
class WithdrawController extends BmsBaseController {

    /**
     * 导出excel文件
     */
    function export() {
        $this->checkRule(self::$rule);
        $result = D('Withdraw','Logic')->export(I('post.'));
        if(!$result) {
            $this->error(D('Withdraw','Logic')->getLogicInfo());
        }
    }

    /**
     * 导入数据
     */
    function import() {
        $this->checkRule(self::$rule);
        if(!IS_POST) {
            $this->display('import');
        } else {
            $result = D('Withdraw','Logic')->import(I('post.'));
            if($result) {
                $this->success('数据导入成功！', cookie('__forward__'));
            } else {
                $this->error(D('Withdraw','Logic')->getLogicError());
            }
        }
    }
}