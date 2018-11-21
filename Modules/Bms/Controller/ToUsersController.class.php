<?php
namespace Bms\Controller;

/**
 * Class ToUsersController
 * @package Bms\Controller
 * 给用户发送信息、优惠券等 批量发送 控制器
 */
class ToUsersController extends BmsBaseController {

    /**
     * 发送页面
     */
    function send() {
        if(empty($_REQUEST['model'])) {
            $this->error('参数错误！');
        }
        if(!empty($_REQUEST['where'])) {
            //获取接收者信息
            $this->assign('receivers', D('ToUsers','Logic')->getReceivers(array('p'=>1)));
        }
        //模板信息
        $this->assign('templates',D('SendTemplate','Logic')->getSendTemplate());
        //信息类型
        $this->assign('types',C('SEND_TEMPLATE_TYPES'));
        //接收规则
        $this->assign('receive_rules',$this->_getReceiveRules());
        //跳转规则
        $this->assign('target_rules',D('SendTemplate', 'Logic')->getPushTargetRules());

        $this->assign('breadcrumb_nav', L('_SEND_'));
        $this->display('send');
    }

    /**
     * 赠送优惠券
     */
    function give() {
        if(empty($_REQUEST['model'])) {
            $this->error('参数错误！');
        }
        if(!empty($_REQUEST['where'])) {
            //获取接收者信息
            $this->assign('receivers', D('ToUsers','Logic')->getReceivers(array('p'=>1)));
        }
        //优惠券模板
        $this->assign('templates',D('Coupon','Logic')->getSendTemplate());
        //商品分类
        $this->assign('select', D('GoodsCategory','Logic')->getSelect('goods_cate_id',0));
        //接收规则
        $this->assign('receive_rules',$this->_getReceiveRules());

        $this->assign('breadcrumb_nav', L('_GIVE_'));
        $this->display('give');
    }

    /**
     * 给用户
     */
    function toUsers() {
        $this->ajaxReturn(D('ToUsers','Logic')->toUsers(I('request.')), Json);
    }

    /**
     * 获取接收者列表
     * 查询、div滚动执行
     */
    function getReceivers() {
        $this->ajaxReturn(D('ToUsers','Logic')->getReceivers(I('request.')), JSON);
    }

    /**
     * @return mixed
     * 接收规则
     */
    private function _getReceiveRules() {
        return C('RECEIVE_RULE');
    }
}