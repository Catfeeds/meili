<?php
namespace Wap\Controller;

/**
 * Class SmsVerifyController
 * @package Wap\Controller
 * 短信验证码控制器
 * 一、根据账号、标识获取验证码
 * 二、验证验证码是否存在或者过期
 */
class SmsVerifyController extends WapBaseController {

    /**
     * 获取短信验证码
     * 参数 account  unique_code
     */
    function getSmsVerify() {
        $result = api('SmsVerify/getSmsVerify', array(I('request.account'), I('request.unique_code')));
        if($result === true) {
            $this->success('发送成功！');
        } else {
            $this->error($result);
        }
    }

    /**
     * 验证短信验证码
     * 参数 account verify unique_code
     */
    function checkSmsVerify() {
        //验证短信验证码
        $result = api('SmsVerify/checkSmsVerify', array(I('request.account'), I('request.verify'), I('request.unique_code')));
        if($result === true) {
            $this->success('验证通过！');
        } else {
            $this->error($result);
        }
    }
}