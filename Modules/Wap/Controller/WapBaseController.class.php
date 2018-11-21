<?php
namespace Wap\Controller;
use FrontC\Controller\FrontBaseController;

/**
 * Class WapBaseController
 * @package Wap\Controller
 * 控制器父类
 */
class WapBaseController extends FrontBaseController {

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //获取当前登录的ID
        define('M_ID', is_login());
        if(!M_ID) {
            //openid
        }
        //设置为传递参数
        $_REQUEST['m_id'] = M_ID;

    }

    /**
     * 验证登陆
     * 黑暗中的武者
     */
    protected function checkLogin() {
        //判断账号是否为空
        if(!M_ID) {
            if(IS_AJAX)
                $this->error('请先登录！', U('Passport/login'));
            else
                redirect(U('Passport/login'));
        }
        //判断账号是否禁用
        if(M('Member')->where(array('id'=>M_ID))->getField('status') != 1) {
            session(null);
            $this->error('您的账号未开通或以禁用，如有疑问请联系管理员！', U('Passport/login'));
        }
    }

    /**
     * 访问的方法不存在 调用
     */
    protected function _empty() {
        redirect('/e404');
    }
}