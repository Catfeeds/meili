<?php
namespace Api\Controller;

/**
 * Class OrderPayController
 * @package Api\Controller
 * 支付控制器
 */
class OrderPayController extends ApiBaseController {

    /**
     * 支付宝回调方法
     * 详细描述：
     * 特别注意：
     * POST参数：
     */
    function aliCallback() {
        //APP配置
        define('ALI_PAY_CONFIG', 'API');
        D('FrontC/Pay', 'Logic')->aliPay();
    }

    /**
     * 微信回调方法
     * 详细描述：
     * 特别注意：
     * POST参数：
     */
    function wxCallback() {
        //APP配置
        define('WX_PAY_CONFIG', 'Api');
        D('FrontC/Pay', 'Logic')->wxPay();
    }
}