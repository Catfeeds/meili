<?php
namespace Wap\Controller;

/**
 * Class PassportController
 * @package Wap\Controller
 * 登陆 注册 找回密码 控制器
 */
class PassportController extends WapBaseController {

    /**
     * 用户登陆
     * 详细描述：
     * 特别注意：存在第三方登陆，第三方登陆判断openid是否已绑定账号
     * POST参数：(*account(登陆账号) *password(登陆密码)) or (*openid(第三方openid))
     */
    function login() {
        if(!IS_POST) {
            if(M_ID)
                redirect(U('Center/index'));
            $this->display('login');
        } else {
            $result = D('FrontC/Passport', 'Logic')->doLogin(I('request.'));
            if(!$result)
                $this->error(D('FrontC/Passport', 'Logic')->getLogicInfo());
            else
                $this->success('登陆成功！', cookie('__forward__') == null ? U('Center/index') : cookie('__forward__'));
        }
    }

    /**
     * 用户注册页面控制
     */
    function register() {
        if(M_ID)
            redirect(U('Center/index'));
        $interconnect = cookie('__interconnect__');
        $this->assign('interconnect', $interconnect);
        if(I('request.step') == 1) {
            $this->display('register_1');
        } else {
            $this->display('register_2');
        }
    }

    /**
     * 用户注册
     * 详细描述：
     * 特别注意：存在第三方登陆绑定账号注册
     * POST参数：*account(登陆账号) *password(登陆密码) *re_password(确认密码)  openid(第三方openid 可选)
     */
    function doRegister() {
        $result = D('FrontC/Passport', 'Logic')->doRegister(I('request.'));
        if(!$result)
            $this->error(D('FrontC/Passport', 'Logic')->getLogicInfo());
        else
            $this->success('注册成功！', cookie('__forward__') == null ? U('Center/index') : cookie('__forward__'));
    }

    /**
     * 用户找回密码页面控制
     */
    function findPass() {
        if(M_ID)
            redirect(U('Center/index'));
        if(I('request.step') == 1) {
            $this->display('findPass_1');
        } else {
            $this->display('findPass_2');
        }
    }

    /**
     * 用户找回密码
     * 详细描述：
     * 特别注意：
     * POST参数：*account(登陆账号) *password(新密码) *re_password(确认新密码)
     */
    function doFindPass() {
        $result = D('FrontC/Passport', 'Logic')->doFindPass(I('request.'));
        if(!$result)
            $this->error(D('FrontC/Passport', 'Logic')->getLogicInfo());
        else
            $this->success('密码已重置，请重新登陆！', U('Passport/login'));
    }
}