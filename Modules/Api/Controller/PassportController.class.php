<?php

namespace Api\Controller;

/**
 * Class PassportController
 * @package Api\Controller
 * 登陆 注册 找回密码 控制器
 */
class PassportController extends ApiBaseController
{

    /**
     * 用户小程序登陆注册一体
     * 详细描述：
     * 特别注意：存在第三方登陆，第三方登陆判断openid是否已绑定账号
     * POST参数：微信小程序code--临时登录凭证
     */
    function loginRegister()
    {
        $result = D('FrontC/Passport', 'Logic')->autoLogin(I('request.'));
        if (false === $result)
            api_response('error', D('FrontC/Passport', 'Logic')->getLogicInfo());
        else
            api_response('success', '登陆成功！', $result);
    }

    /**
     * 用户登陆
     * 详细描述：
     * 特别注意：存在第三方登陆，第三方登陆判断openid是否已绑定账号
     * POST参数：(*account(登陆账号) *password(登陆密码)) or (*openid(第三方openid) *platform(平台))
     */
    function login()
    {
        $result = D('FrontC/Passport', 'Logic')->doLogin(I('request.'));
        //var_dump(D('FrontC/Passport', 'Logic')->getLogicInfo());
        if (false === $result)
            api_response('error', D('FrontC/Passport', 'Logic')->getLogicInfo());
        else
            api_response('success', '登陆成功！', $result);
    }

    /**
     * 用户注册
     * 详细描述：
     * 特别注意：存在第三方登陆绑定账号注册
     * POST参数：*account(登陆账号) *password(登陆密码) *re_password(确认密码) code(邀请码) openid(第三方openid 可选) platform(平台类型)
     */
    function register()
    {
        $result = D('FrontC/Passport', 'Logic')->doRegister(I('request.'));
        //var_dump(D('FrontC/Passport', 'Logic')->getLogicInfo());
        if (false === $result)
            api_response('error', D('FrontC/Passport', 'Logic')->getLogicInfo());
        else
            api_response('success', '注册成功！', $result);
    }

    /**
     * 用户找回密码
     * 详细描述：
     * 特别注意：
     * POST参数：*account(登陆账号) *password(新密码) *re_password(确认新密码)
     */
    function findPass()
    {
        $result = D('FrontC/Passport', 'Logic')->doFindPass(I('request.'));
        //var_dump(D('FrontC/Passport', 'Logic')->getLogicInfo());
        if (false === $result)
            api_response('error', D('FrontC/Passport', 'Logic')->getLogicInfo());
        else
            api_response('success', D('FrontC/Passport', 'Logic')->getLogicInfo());
    }
}