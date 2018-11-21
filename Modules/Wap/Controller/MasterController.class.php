<?php
namespace Wap\Controller;

/**
 * Class MasterController
 * @package Wap\Controller
 * 家装服务预约控制器
 */
class MasterController extends WapBaseController {

	protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //判断登陆
		cookie('__forward__', U('' . CONTROLLER_NAME . '/' . ACTION_NAME . '', $_REQUEST));
        $this->checkLogin();
    }

    function apply() {
        if(!IS_POST) {
			$param['where']['m_id'] = $_REQUEST['m_id'];
            $master = D('FrontC/Master')->findRow($param);
			if(!$master)
            $this->display('apply');
			else {
			$this->assign('status', $master['status']);
			$this->display('applied');
			}
        } else {
            $_REQUEST['model'] = 'FrontC/Master';
			if(empty($_REQUEST['referee']))
               $_REQUEST['referee'] = 13000000000;
            $result = D('FrontC/Master', 'Logic')->update(I('request.'));
            if (!$result)
                $this->error(D('FrontC/Master', 'Logic')->getLogicInfo());
            else
                $this->success(D('FrontC/Master', 'Logic')->getLogicInfo(),U('Master/Apply'));
        }
    }
}