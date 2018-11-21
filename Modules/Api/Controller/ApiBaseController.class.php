<?php
namespace Api\Controller;
use FrontC\Controller\FrontBaseController;

/**
 * Class ApiBaseController
 * @package Api\Controller
 * APP接口端 控制器父类
 */
class ApiBaseController extends FrontBaseController {

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //验证token
        //$this->_checkToken();
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //字符编码
        header('Content-type:text/html; charset=utf-8');
    }

    /**
     * 验证令牌信息
     */
    private function _checkToken() {
        $token = explode('|',I('request.token'));
        //判断时间和加密字符  时间在5分钟误差内
        if(!($token[0] > (time() - 300) && $token[0] < (time() + 300) && $token[1] == MD5('SX2016'))) {
            api_response(false, '令牌信息错误！');
        }
    }

    /**
     * 验证登陆
     * 黑暗中的武者
     */
    protected function checkLogin() {
        //判断账号是否为空
        if(empty($_REQUEST['m_id']))
            api_response('error', '请先登录！');
        //判断账号是否禁用
        if(M('Member')->where(array('id'=>I('request.m_id')))->getField('status') != 1)
            api_response('error', '您的账号未开通或以禁用，如有疑问请联系管理员！');
    }
}