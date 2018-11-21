<?php
namespace Api\Controller;

/**
 * Class VerifyController
 * @package Api\Controller
 * 验证码控制器
 * 一、根据账号、标识获取验证码
 * 二、验证验证码是否存在或者过期
 */
class VerifyController extends ApiBaseController {

    /**
     * 获取验证码
     * 参数 account  unique_code
     */
    function getVerify() {
        $result = api('Verify/getVerify', array(I('request.account'), I('request.unique_code')));
        if($result === true) {
            api_response('success', '发送成功！');
        } else {
            api_response('error', $result);
        }
    }

    /**
     * 验证验证码
     * 参数 account verify unique_code
     */
    function checkVerify() {
        //验证短信验证码
        $result = api('Verify/checkVerify', array(I('request.account'), I('request.verify'), I('request.unique_code')));
        if($result === true) {
            api_response('success', '验证通过！');
        } else {
            api_response('error', $result);
        }
    }
}