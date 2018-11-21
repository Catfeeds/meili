<?php
namespace Wap\Controller;

/**
 * Class ReservationController
 * @package Wap\Controller
 * 家装服务预约控制器
 */
class ReservationController extends WapBaseController {

   	protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //判断登陆
		cookie('__forward__', U('' . CONTROLLER_NAME . '/' . ACTION_NAME . '', $_REQUEST));
        $this->checkLogin();
    }
	
	/**
     * 提交预约
     * 详细描述：
     * 特别注意：
     * POST参数：*province_name(省) *city_name(市) *area_name(区) *address_detail(详细地址) *acreage(面积) *contacts(联系人) *mobile(电话)
     */
    function submitR() {
        if(!IS_POST) {
            if(I('request.type') != 2 && I('request.type') != 1)
                redirect(U('System/error404'));
            $this->display('submitR');
        } else {
            $_REQUEST['model'] = 'FrontC/Reservation';	
			if(!empty($_REQUEST['master_mobile'])) {
			   if(!preg_match("/^1[34578]\d{9}$/", trim($_REQUEST['master_mobile']))){
			      $this->error('指定师傅手机号码不正确！');
			   }
			   else {
			      $master = M('Master')->where(array('mobile'=>trim($_REQUEST['master_mobile'])))->field('id')->find();
				  if($master)
					  $_REQUEST['ms_id'] = $master['id'];
				  else
                      $this->error('指定师傅不存在！');
			   }
			}
            $result = D('FrontC/Reservation', 'Logic')->update(I('request.'),true);
            if (!$result)
                $this->error(D('FrontC/Reservation', 'Logic')->getLogicInfo());
            else
			{
				 if(I('request.type') == 1)
                   $this->success(D('FrontC/Reservation', 'Logic')->getLogicInfo());
				 else 
					 {
						 $id = $result;
						 $result = D('FrontC/Flow', 'Logic')->submitRepairOrder(I('request.'));
						 if ($result === false)
						 $this->error(D('FrontC/Flow', 'Logic')->getLogicInfo());
						 else
						 {
							$result=M("Reservation")->where(array('id'=>$id))->save(array('order_sn'=>$result['order_sn']));
							
							if (!$result)
							$this->error(D('FrontC/Reservation', 'Logic')->getLogicInfo());
							else
							$this->success(D('FrontC/Reservation', 'Logic')->getLogicInfo(),U('Center/index'));
						 }
					 }
		   }
        }
    }
}