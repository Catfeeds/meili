<?php
namespace Bms\Controller;
use Think\Controller;

/**
 * Class RefundCallbackController
 * @package Bms\Controller
 */
class RefundCallbackController extends Controller {

    /**
     * 退款回调
     * "refund_status":"REFUND_SUCCESS",
     * "discount":"0.00",
     * "payment_type":"1",
     * "subject":"\u8d2d\u4e70\u8ba2\u5355",
     * "trade_no":"2016120921001004020229438294",
     * "buyer_email":"18883278380",
     * "gmt_create":"2016-12-09 09:08:37",
     * "notify_type":"trade_status_sync",
     * "quantity":"1",
     * "out_trade_no":"48-A14812457098685-28-1-1",
     * "gmt_refund":"2016-12-15 15:18:59.676",
     * "seller_id":"2088121900317297",
     * "notify_time":"2016-12-15 15:19:00",
     * "body":"\u8d2d\u4e70\u8ba2\u5355",
     * "trade_status":"TRADE_CLOSED",
     * "is_total_fee_adjust":"N",
     * "total_fee":"0.01",
     * "gmt_payment":"2016-12-09 09:08:38",
     * "seller_email":"2534876634@qq.com",
     * "gmt_close":"2016-12-15 15:18:59",
     * "price":"0.01",
     * "buyer_id":"2088802912519023",
     * "notify_id":"e17065ce135d4ed61205815a00ba854g5m",
     * "use_coupon":"N",
     * "sign_type":"RSA",
     * "sign":""
     *
     *
     * "sign":"bbf1947671efadc630561890e7fc23ce",
     * "result_details":"2016121521001004980245355969^0.01^SUCCESS",
     * "notify_time":"2016-12-15 18:01:56",
     * "sign_type":"MD5",
     * "notify_type":"batch_refund_notify",
     * "notify_id":"114a32f9dc51a191a0bdfefa1355076iuu",
     * "batch_no":"201612154618037184",
     * "success_num":"1"
     */
    function aliRefundCallback() {
        M('Feedback')->data(array('content'=>json_encode($_POST)))->add();
        D('OrderInfo', 'Logic')->aliRefundCallback($_POST);
    }

    function checkStatus() {
        $order = M('OrderInfo')->where(array('id'=>I('request.order_id')))->field('status')->find();
        if($order['status'] != 8) {
            $this->error('失败！');
        }
        $this->success('成功！');
    }
}