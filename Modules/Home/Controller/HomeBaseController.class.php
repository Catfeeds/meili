<?php
namespace Home\Controller;
use FrontC\Controller\FrontBaseController;

/**
 * Class HomeBaseController
 * @package Home\Controller
 * PC前台控制器父类
 */
class HomeBaseController extends FrontBaseController {

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();

    }

    /**
     * 验证登陆
     * 黑暗中的武者
     */
    protected function checkLogin() {
        //判断账号是否为空
        if(session('M_ID') == null) {
            if(IS_AJAX)
                $this->error('请先登录！', U('Login/index'));
            else
                redirect(U('Login/index'));
        }
        //判断账号是否禁用
        if(M('Member')->where(array('id'=>session('M_ID')))->getField('status') != 1) {
            session(null);
            $this->error('您的账号未开通或以禁用，如有疑问请联系管理员！', '/login');
        }
    }

    /**
     * 访问的方法不存在 调用
     */
    protected function _empty() {
        redirect('/e404');
    }
}