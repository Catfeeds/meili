<?php
namespace FrontC\Logic;

/**
 * Class PayLogic
 * @package FrontC\Logic
 * 支付相关操作逻辑层
 * 黑暗中的武者
 */
class PayLogic extends FrontBaseLogic {

    /**
     * @param array $request
     * @return string
     * 进入支付页面获取相关信息
     */
    function pay($request = array()) {
        //参数判空
        if(empty($request['order_id'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //获取订单信息
        $order_info = M('OrderInfo')->where(array('id'=>$request['order_id']))->field('id order_id,order_sn,pay_amounts,status')->find();
        //获取用户余额
        $balance=M('Member')->field(array('m_id'=>$request['m_id']))->getField('balance');
        $order_info['balance']=(string)$balance;
        if(empty($order_info))
            return $this->setLogicInfo('订单信息不存在！', false);
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(1), $order_info)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        return $order_info;
    }

    /**
     * @param array $request
     * @return string
     * 确认支付
     */
    function doPay($request = array()) {
        //参数判空
        if(empty($request['order_id']) || empty($request['payment']))
            return $this->setLogicInfo('参数错误！', false);
        //获取订单信息
        $order_info = M('OrderInfo')->where(array('id'=>$request['order_id']))->field('id order_id,order_sn,m_id,pay_amounts,status')->find();
        //未查询到订单信息
        if(empty($order_info))
            return $this->setLogicInfo('未查到订单信息！', false);
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(1), $order_info)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        //订单信息串  //订单号-订单ID-支付方式-订单类型(1购买 2充值 3购卡)
        $out_trade_no = $order_info['order_sn'] . '-' . $request['order_id'] . '-' . $request['payment'] . '-1';
        //1支付宝 2微信 3余额
        if($request['payment'] == 1 || $request['payment'] == 2) {
            return $this->_onlinePay($request['payment'], $order_info, $out_trade_no);
        } else {
            //余额支付 获取信息
            $user_info = M('Member')->where(array('id'=>$request['m_id']))->field('balance,pay_pass')->find();
            return $this->_balancePay($request, $order_info, $user_info, $out_trade_no);
        }
    }
    /**
     * @param array $request
     * @return string
     * 服务商品确认支付
     */
    function doPaySer($request = array()) {
        //参数判空
        if(empty($request['order_id']) || empty($request['payment']))
            return $this->setLogicInfo('参数错误！', false);
        //获取订单信息
        $order_info = M('OrderInfoSer')->where(array('id'=>$request['order_id']))->field('id order_id,order_sn,m_id,pay_amounts,status')->find();
        //未查询到订单信息
        if(empty($order_info))
            return $this->setLogicInfo('未查到订单信息！', false);
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfoSer', 'Service')->checkStatus(0, array(1), $order_info)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        //订单信息串  //订单号-订单ID-支付方式-订单类型(1购买 2充值 3购卡)
        $out_trade_no = $order_info['order_sn'] . '-' . $request['order_id'] . '-' . $request['payment'] . '-1';
        //1支付宝 2微信 3余额
        if($request['payment'] == 1 || $request['payment'] == 2) {
            return $this->_onlinePay($request['payment'], $order_info, $out_trade_no);
        } else {
            //余额支付 获取信息
            $user_info = M('Member')->where(array('id'=>$request['m_id']))->field('balance,pay_pass')->find();
            return $this->_balancePaySer($request, $order_info, $user_info, $out_trade_no);
        }
    }
    /**
     * 团购支付
     * @param array $request
     * @return string
     * 服务商品确认支付
     */
    function doPayGroup($request = array()) {
        //参数判空
        if(empty($request['order_id']) || empty($request['payment']))
            return $this->setLogicInfo('参数错误！', false);
        //获取订单信息
        $order_info = M('OrderInfoSer')->where(array('id'=>$request['order_id']))->field('id order_id,order_sn,m_id,pay_amounts,status')->find();
        //未查询到订单信息
        if(empty($order_info))
            return $this->setLogicInfo('未查到订单信息！', false);
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfoSer', 'Service')->checkStatus(0, array(1), $order_info)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        //订单信息串  //订单号-订单ID-支付方式-订单类型(1购买 2充值 3购卡)
        $out_trade_no = $order_info['order_sn'] . '-' . $request['order_id'] . '-' . $request['payment'] . '-1';
        //1支付宝 2微信 3余额
        if($request['payment'] == 1 || $request['payment'] == 2) {
            return $this->_onlinePay($request['payment'], $order_info, $out_trade_no);
        } else {
            //余额支付 获取信息
            $user_info = M('Member')->where(array('id'=>$request['m_id']))->field('balance,pay_pass')->find();
            return $this->_balancePayGroup($request, $order_info, $user_info, $out_trade_no);
        }
    }
    /**
     * @param $payment
     * @param $order_info
     * @param $out_trade_no
     * @return bool|mixed
     * 第三方支付处理  判断是APP还是网站
     */
    private function _onlinePay($payment, $order_info, $out_trade_no) {
        //添加第三方支付流水
        //如果还没有记录则添加  添加 用户第三方支付流水记录 trend--1(购买) order_type--1(购买订单)
        if(!M('CashTurnover')->where(array('order_id'=>$order_info['order_id'], 'trend'=>1, 'payment'=>$payment))->count())
            D('FrontC/Finance', 'Service')->addCashTurnover($order_info['m_id'],$order_info['order_id'],$payment,1,$order_info['pay_amounts'],$order_info['order_sn']);
        //支付宝
        if($payment == 1) {
            if(TERMINAL == 'api')  //APP端调用
                $result = api('Ali/newSign', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_ALI_API_NOTIFY_URL'), '购买订单', '购买订单'));
            elseif(TERMINAL == 'home') //网站端调用
                $result = api('Ali/submit', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_ALI_HOME_NOTIFY_URL'), C('ORDER_ALI_HOME_RETURN_URL'), '购买订单', '购买订单'));
        }
        //微信支付
        elseif($payment == 2) {
            if(TERMINAL == 'api') //APP端调用
                $result = api('WeChat/sign', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_WX_API_NOTIFY_URL'), '购买订单', '购买订单'));
            elseif(TERMINAL == 'home') //网站端调用
                $result = api('WeChat/code', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_WX_HOME_NOTIFY_URL'), '购买订单', '购买订单'));
            elseif(TERMINAL == 'wap') //微信版端调用
                $result = api('WeChat/jsPay', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_WX_WAP_NOTIFY_URL'), '购买订单', '购买订单'));
        }
        if(!$result) {
            return $this->setLogicInfo('签名错误！', false);
        }
        return $result;
    }

    /**
     * @param $request
     * @param $order
     * @param $user_info
     * @param $out_trade_no
     * @return bool
     * 余额支付
     * 黑暗中的武者
     */
    private function _balancePayGroup($request, $order, $user_info, $out_trade_no) {
        //判断余额是否够用
        if($user_info['balance'] < $order['pay_amounts']) {
            return $this->setLogicInfo('余额不足！', false);
        }
        //判断是否已设置支付密码
        if(empty($user_info['pay_pass']))
            return $this->setLogicInfo('未设置支付密码，为保证安全请先去个人信息中设置支付密码！', false);
        //判断支付密码
        if(MD5($request['pay_pass']) != $user_info['pay_pass']) {
            return $this->setLogicInfo('支付密码不正确！', false);
        }
        //如果还没有记录则添加  添加 用户余额支付流水记录 symbol 2(减), trend 1(支付预约订单)
        if(!M('BalanceTurnover')->where(array('order_id'=>$request['order_id'],'order_type'=>1,'trend'=>1))->count())
            D('FrontC/Finance', 'Service')->addBalanceTurnover($request['m_id'],1,2,1,$order['pay_amounts'],$user_info['balance'],0,$order['order_id'],$order['order_sn'],1);

        //支付成功 减用户余额 添加余额变动记录
        if(!M('Member')->where(array('id'=>$request['m_id']))->setDec('balance', $order['pay_amounts'])) {
            //支付失败后操作
            $this->errorPayDo(array('out_trade_no'=>$out_trade_no, 'msg'=>'扣除用户余额失败！'));
            return $this->setLogicInfo('系统繁忙，支付失败！', false);
        }
        //支付成功后操作
        $this->successPayDoGroup(array('out_trade_no'=>$out_trade_no));

        return true;
    }

    /**
     * @param $request
     * @param $order
     * @param $user_info
     * @param $out_trade_no
     * @return bool
     * 余额支付
     * 黑暗中的武者
     */
    private function _balancePaySer($request, $order, $user_info, $out_trade_no) {
        //判断余额是否够用
        if($user_info['balance'] < $order['pay_amounts']) {
            return $this->setLogicInfo('余额不足！', false);
        }
        //判断是否已设置支付密码
        if(empty($user_info['pay_pass']))
            return $this->setLogicInfo('未设置支付密码，为保证安全请先去个人信息中设置支付密码！', false);
        //判断支付密码
        if(MD5($request['pay_pass']) != $user_info['pay_pass']) {
            return $this->setLogicInfo('支付密码不正确！', false);
        }
        //如果还没有记录则添加  添加 用户余额支付流水记录 symbol 2(减), trend 1(支付预约订单)
        if(!M('BalanceTurnover')->where(array('order_id'=>$request['order_id'],'order_type'=>1,'trend'=>1))->count())
            D('FrontC/Finance', 'Service')->addBalanceTurnover($request['m_id'],1,2,1,$order['pay_amounts'],$user_info['balance'],0,$order['order_id'],$order['order_sn'],1);

        //支付成功 减用户余额 添加余额变动记录
        if(!M('Member')->where(array('id'=>$request['m_id']))->setDec('balance', $order['pay_amounts'])) {
            //支付失败后操作
            $this->errorPayDo(array('out_trade_no'=>$out_trade_no, 'msg'=>'扣除用户余额失败！'));
            return $this->setLogicInfo('系统繁忙，支付失败！', false);
        }
        //支付成功后操作
        $this->successPayDoSer(array('out_trade_no'=>$out_trade_no));

        return true;
    }

    /**
     * @param $request
     * @param $order
     * @param $user_info
     * @param $out_trade_no
     * @return bool
     * 余额支付
     * 黑暗中的武者
     */
    private function _balancePay($request, $order, $user_info, $out_trade_no) {
        //判断余额是否够用
        if($user_info['balance'] < $order['pay_amounts']) {
            return $this->setLogicInfo('余额不足！', false);
        }
        //判断是否已设置支付密码
        if(empty($user_info['pay_pass']))
            return $this->setLogicInfo('未设置支付密码，为保证安全请先去个人信息中设置支付密码！', false);
        //判断支付密码
        if(MD5($request['pay_pass']) != $user_info['pay_pass']) {
            return $this->setLogicInfo('支付密码不正确！', false);
        }
        //如果还没有记录则添加  添加 用户余额支付流水记录 symbol 2(减), trend 1(支付预约订单)
        if(!M('BalanceTurnover')->where(array('order_id'=>$request['order_id'],'order_type'=>1,'trend'=>1))->count())
            D('FrontC/Finance', 'Service')->addBalanceTurnover($request['m_id'],1,2,1,$order['pay_amounts'],$user_info['balance'],0,$order['order_id'],$order['order_sn'],1);

        //支付成功 减用户余额 添加余额变动记录
        if(!M('Member')->where(array('id'=>$request['m_id']))->setDec('balance', $order['pay_amounts'])) {
            //支付失败后操作
            $this->errorPayDo(array('out_trade_no'=>$out_trade_no, 'msg'=>'扣除用户余额失败！'));
            return $this->setLogicInfo('系统繁忙，支付失败！', false);
        }
        //支付成功后操作
        $this->successPayDo(array('out_trade_no'=>$out_trade_no));

        return true;
    }

    /**
     * @param array $request
     * @return string
     * 确认支付
     */
    function doBRPay($request = array()) {
        //参数判空
        if(empty($request['rec_order_id']) || empty($request['payment']))
            return $this->setLogicInfo('参数错误！', false);
        //获取订单信息
        $order_info = M('RechargeOrder')->where(array('id'=>$request['rec_order_id']))->field('id rec_order_id,order_type,order_sn,user_id,pay_amounts,status')->find();
        //未查询到订单信息
        if(empty($order_info))
            return $this->setLogicInfo('未查到订单信息！', false);
        //验证实时状态 是否可进行此操作
        if($order_info['status'] == 1)
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        //订单信息串  //订单号-订单ID-支付方式-订单类型(1购买 2充值 3购卡)
        $out_trade_no = $order_info['order_sn'] . '-' . $order_info['rec_order_id'] . '-' . $request['payment'] . '-' . $order_info['order_type'];
        //1支付宝 2微信
        if($request['payment'] == 1 || $request['payment'] == 2) {
            return $this->_onlineBRPay($request['payment'], $order_info, $out_trade_no);
        } else {
            return $this->setLogicInfo('未发现支付方式！', false);
        }
    }

    /**
     * @param $payment
     * @param $order_info
     * @param $out_trade_no
     * @return bool|mixed
     * 第三方支付处理  判断是APP还是网站
     */
    private function _onlineBRPay($payment, $order_info, $out_trade_no) {
        //添加第三方支付流水
        //如果还没有记录则添加  添加 用户第三方支付流水记录 trend--1(购买) order_type--1(购买订单)
        if(!M('CashTurnover')->where(array('order_id'=>$order_info['rec_order_id'], 'trend'=>$order_info['order_type'], 'payment'=>$payment))->count())
            D('FrontC/Finance', 'Service')->addCashTurnover($order_info['user_id'],$order_info['rec_order_id'],$payment,$order_info['order_type'],$order_info['pay_amounts'],$order_info['order_sn'],$order_info['order_type']);
        //支付宝
        if($payment == 1) {
            if(TERMINAL == 'api')  //APP端调用
                $result = api('Ali/newSign', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_ALI_API_NOTIFY_URL'), '充值订单', '充值订单'));
            elseif(TERMINAL == 'home') //网站端调用
                $result = api('Ali/submit', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_ALI_HOME_NOTIFY_URL'), C('ORDER_ALI_HOME_RETURN_URL'), '充值订单', '充值订单'));
        }
        //微信支付
        elseif($payment == 2) {
            if(TERMINAL == 'api') //APP端调用
                $result = api('WeChat/sign', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_WX_API_NOTIFY_URL'), '充值订单', '充值订单'));
            elseif(TERMINAL == 'home') //网站端调用
                $result = api('WeChat/code', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_WX_HOME_NOTIFY_URL'), '充值订单', '充值订单'));
            elseif(TERMINAL == 'wap') //微信版端调用
                $result = api('WeChat/jsPay', array($out_trade_no, $order_info['pay_amounts'], C('ORDER_WX_WAP_NOTIFY_URL'), '充值订单', '充值订单'));
        }
        if(!$result) {
            return $this->setLogicInfo('签名错误！', false);
        }
        return $result;
    }

    /**
     * 支付宝支付
     * 黑暗中的武者
     */
    function aliPay() {
        //引入参数处理方法
        vendor('Pay.AliPay.JSDZPC.lib.alipay_notify#class');
        //加载配置
        api('Ali/config2C', array()); $alipay_config = C('ALIPAY_CONFIG_'.ALI_PAY_CONFIG.'');
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        if(!empty($_POST))
            $verify_result  = $alipayNotify->verifyNotify(); //异步验证
        if(!empty($_GET))
            $verify_result  = $alipayNotify->verifyReturn(); //跳转验证
        M('Feedback')->add(array('content'=>json_encode($_REQUEST)));
        if(TERMINAL == 'api')
            $verify_result = true;
        //判断是否验证成功
        if($verify_result) { //验证成功
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            /*"order_code": "15090604451164","discount":"0.00","payment_type":"1","subject":"\u9884\u7ea6\u8ba2\u5355","trade_no":"2016081021001004980297973602",
            "buyer_email":"133***930","gmt_create":"2016-08-10 18:33:53","notify_type":"trade_status_sync","quantity":"1",
            "out_trade_no":"06-Y14688932235686676-12-1-1","seller_id":"208***381781","notify_time":"2016-08-10 18:38:25",
            "body":"\u9884\u7ea6\u8ba2\u5355","trade_status":"TRADE_SUCCESS","is_total_fee_adjust":"N","total_fee":"0.01",
            "gmt_payment":"2016-08-10 18:34:34","seller_email":"zjm***com","price":"0.01","buyer_id":"208****00983",
            "notify_id":"9d95a5eeb53a8***540f6db6aa0dnka","use_coupon":"N","sign_type":"MD5","sign":"46f5192afbb85****7039ef9a4e"*/
            if($_REQUEST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_REQUEST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                $param['out_trade_no']      = $_REQUEST['out_trade_no'];   //本站订单号
                $param['turnover_number']   = $_REQUEST['trade_no'];       //支付宝交易号
                //支付成功后操作
                $this->successPayDo($param);
                //网站页面回调跳转
                if(TERMINAL == 'home' && !empty($_GET))
                    redirect('/pay_success/支付成功');
            }
            echo "success";		//请不要修改或删除
        } else {
            //支付失败后操作
            $this->errorPayDo(array('out_trade_no'=>$_REQUEST['out_trade_no'], 'msg'=>'通知结果验证失败！', 'notify'=>json_encode($_REQUEST)));
            //网站页面回调跳转
            if(TERMINAL == 'home' && !empty($_GET))
                redirect('/pay_error/支付失败/通知结果验证失败');
            echo "fail"; //不能删除 修改
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    /**
     * 微信支付
     * 黑暗中的武者
     * <xml>
     *      <appid><![CDATA[wx9a****9c3fb]]></appid>
     *      <attach><![CDATA[test]]></attach>
     *      <bank_type><![CDATA[CFT]]></bank_type>
     *      <cash_fee><![CDATA[1]]></cash_fee>
     *      <fee_type><![CDATA[CNY]]></fee_type>
     *      <is_subscribe><![CDATA[Y]]></is_subscribe>
     *      <mch_id><![CDATA[133****401]]></mch_id>
     *      <nonce_str><![CDATA[whij3o8l****5z7gm4ev58]]></nonce_str>
     *      <openid><![CDATA[ooeMku****jV31sgOpo]]></openid>
     *      <out_trade_no><![CDATA[20160810102917]]></out_trade_no>
     *      <result_code><![CDATA[SUCCESS]]></result_code>
     *      <return_code><![CDATA[SUCCESS]]></return_code>
     *      <sign><![CDATA[5A06635****5BEEEA2A63]]></sign>
     *      <time_end><![CDATA[20160810102841]]></time_end>
     *      <total_fee>1</total_fee>
     *      <trade_type><![CDATA[NATIVE]]></trade_type>
     *      <transaction_id><![CDATA[4001592001201608100999909722]]></transaction_id>
     * </xml>
     */
    function wxPay() {
        //引入参数处理方法
        vendor('Pay.WxPay.lib.WxPay#Data');
        //获取通知的数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        M('Feedback')->add(array('content'=>empty($xml) ? '1' : ''));
        //如果返回成功则验证签名 并将xml数据转化成数组
        try {
            $cdata = \WxPayResults::Init($xml);
        } catch (\WxPayException $e) {
            //将通知数据xml转化成数组
            $cdata = xml2array($xml);
            //支付失败后操作
            $this->errorPayDo(array('out_trade_no'=>$cdata['out_trade_no'], 'msg'=>$e->errorMessage(), 'notify'=>$xml));
            echo 'fail'; exit;//不能删除
        }
        //验证是否支付成功
        if($cdata['result_code'] == 'SUCCESS') {
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //请务必判断请求时的total_fee、mch_id与通知时获取的total_fee、mch_id为一致的
            //如果有做过处理，不执行商户的业务程序
            $param['out_trade_no']      = $cdata['out_trade_no'];   //本站订单号
            $param['turnover_number']   = $cdata['transaction_id']; //交易流水号
            //支付成功后操作
            $this->successPayDo($param);
            echo 'success'; //不能删除
        } else {
            //支付失败后操作
            $this->errorPayDo(array('out_trade_no'=>$cdata['out_trade_no'], 'msg'=>'支付失败！', 'notify'=>$xml));
            echo 'fail'; //不能删除
        }
    }

    /**
     * @param array $param
     * @return string
     * 支付成功后操作
     * 修改订单状态  更新支付时间  修改余额流水记录或者线上资金流水记录
     * 黑暗中的武者
     */
    function successPayDo($param = array()) {
        //订单号-订单ID-支付方式-订单类型(1购买商品订单 2充值订单 3购卡订单)
        $out_trade_no = explode('-', $param['out_trade_no']);
        $order_type = array_pop($out_trade_no); //订单类型(1购买商品订单 2充值订单 3购卡订单)
        $payment    = array_pop($out_trade_no); //支付方式
        $order_id   = array_pop($out_trade_no); //订单ID
        $order_sn   = array_pop($out_trade_no); //订单号
        $turnover_number = $param['turnover_number']; //第三方流水号

        if($order_type == 1)
            return $this->_orderType_1($order_type,$payment,$order_id,$order_sn,$turnover_number);
        if($order_type == 2 || $order_type == 3)
            return $this->_orderType_2_3($order_type,$payment,$order_id,$order_sn,$turnover_number);
    }
    /**
     * @param array $param
     * @return string
     * 支付成功后操作
     * 修改订单状态  更新支付时间  修改余额流水记录或者线上资金流水记录
     * 黑暗中的武者
     */
    function successPayDoSer($param = array()) {
        //订单号-订单ID-支付方式-订单类型(1购买商品订单 2充值订单 3购卡订单)
        $out_trade_no = explode('-', $param['out_trade_no']);
        $order_type = array_pop($out_trade_no); //订单类型(1购买商品订单 2充值订单 3购卡订单)
        $payment    = array_pop($out_trade_no); //支付方式
        $order_id   = array_pop($out_trade_no); //订单ID
        $order_sn   = array_pop($out_trade_no); //订单号
        $turnover_number = $param['turnover_number']; //第三方流水号

        if($order_type == 1)
            return $this->_orderTypeSer_1($order_type,$payment,$order_id,$order_sn,$turnover_number);
        if($order_type == 2 || $order_type == 3)
            //暂时没有充值
            return false;
//            return $this->_orderTypeSer_2_3($order_type,$payment,$order_id,$order_sn,$turnover_number);
    }

    /**
     * @param array $param
     * @return string
     * 支付成功后操作
     * 修改订单状态  更新支付时间  修改余额流水记录或者线上资金流水记录
     * 黑暗中的武者
     */
    function successPayDoGroup($param = array()) {
        //订单号-订单ID-支付方式-订单类型(1购买商品订单 2充值订单 3购卡订单)
        $out_trade_no = explode('-', $param['out_trade_no']);
        $order_type = array_pop($out_trade_no); //订单类型(1购买商品订单 2充值订单 3购卡订单)
        $payment    = array_pop($out_trade_no); //支付方式
        $order_id   = array_pop($out_trade_no); //订单ID
        $order_sn   = array_pop($out_trade_no); //订单号
        $turnover_number = $param['turnover_number']; //第三方流水号

        if($order_type == 1)
            return $this->_orderTypeGroup_1($order_type,$payment,$order_id,$order_sn,$turnover_number);
        if($order_type == 2 || $order_type == 3)
            //暂时没有充值
            return false;
//            return $this->_orderTypeSer_2_3($order_type,$payment,$order_id,$order_sn,$turnover_number);
    }


    /**
     * @param $order_type
     * @param $payment
     * @param $order_id
     * @param $order_sn
     * @param $turnover_number
     * @return bool
     * 订单1类型后续处理
     */
    private function _orderTypeGroup_1($order_type,$payment,$order_id,$order_sn,$turnover_number) {
        //获取订单信息
        $order_info = M('OrderInfoSer')->where(array('id'=>$order_id))->field('id order_id,order_sn,consignee,mobile,order_amounts,pay_amounts,status,flag,flag_id')->find();
        //根据订单状态 判断业务逻辑是否已经执行过  执行过则不再进行处理
        if($order_info['status'] != 1)
            return false;
        //判断请求时的total_fee、seller_id/mch_id与通知时获取的total_fee、seller_id/mch_id是否一致

        //未执行过 进行业务处理
        $data['status']         = 2; //订单状态  2已支付
        $data['payment']        = $payment; //支付方式
        $data['pay_status']     = 1; //支付状态
        $data['pay_time']       = NOW_TIME; //支付时间
        //修改订单相关信息
        M('OrderInfoSer')->where(array('id'=>$order_id))->data($data)->save();
        //判断支付方式
        if($payment == 1 || $payment == 2){
            //修改用户第三方支付流水记录status为1 成功支付  第三方流水号
            $ct_data['status']          = 1;
            $ct_data['turnover_number'] = $turnover_number;
            M('CashTurnover')->where(array('order_id'=>$order_id, 'trend'=>1, 'payment'=>$payment))->data($ct_data)->save();
        }
        if($payment == 3) {
            $bt_data['status'] = 1;
            M('BalanceTurnover')->where(array('order_id'=>$order_id,'order_type'=>1,'trend'=>1))->data($bt_data)->save();
        }
        //提交订单到业务系统
//        D('FrontC/OrderInfo','Service')->dockOrder(array_merge($order_info, array('payment'=>$payment)));
        //团购逻辑处理 status 0未支付 1进行中 2已成团 3团失败
        //判断该团是否人数已满 若是满了则状态改为2拼团成功 未满则为1拼团中
        if($order_info['flag'] == 3){
            //获取该团成团限制人数
            $group_info=M('ActivityGroupList')->field('group_sn,group_service_id')->find($order_info['flag_id']);
            $service_info=M('ActivityGroupService')->field('people_limit')->find($group_info['group_service_id']);
            //获取开团中的该团数量
            $count=M('ActivityGroupList')->where(array('group_sn'=>$group_info['group_sn'],'status'=>1))->count();
            if($count < $service_info['people_limit']){
                //该团人数未满 状态改为1
                $dta['id']=$order_info['flag_id'];
                $dta['status']=1;
                M('ActivityGroupList')->data($dta)->save();
            }
            //再次判断该团人数
            $count_c=M('ActivityGroupList')->where(array('group_sn'=>$group_info['group_sn'],'status'=>1))->count();
            if ($count_c == $service_info['people_limit']){
                //该团人数已满 该团所有用户记录状态改为2
                $groups=M('ActivityGroupList')->where(array('group_sn'=>$group_info['group_sn'],'status'=>1))->field('id')->select();
                foreach ($groups as $k=>$v){
                    $data_g['id']=$v['id'];
                    $data_g['status']=2;
                    M('ActivityGroupList')->data($data_g)->save();
                }
            }
        }
        return true;
    }

    /**
     * @param $order_type
     * @param $payment
     * @param $order_id
     * @param $order_sn
     * @param $turnover_number
     * @return bool
     * 订单1类型后续处理
     */
    private function _orderTypeSer_1($order_type,$payment,$order_id,$order_sn,$turnover_number) {
        //获取订单信息
        $order_info = M('OrderInfoSer')->where(array('id'=>$order_id))->field('id order_id,order_sn,consignee,mobile,order_amounts,pay_amounts,status,flag')->find();
        //根据订单状态 判断业务逻辑是否已经执行过  执行过则不再进行处理
        if($order_info['status'] != 1)
            return false;
        //判断请求时的total_fee、seller_id/mch_id与通知时获取的total_fee、seller_id/mch_id是否一致

        //未执行过 进行业务处理
        $data['status']         = 2; //订单状态  2已支付
        $data['payment']        = $payment; //支付方式
        $data['pay_status']     = 1; //支付状态
        $data['pay_time']       = NOW_TIME; //支付时间
        //修改订单相关信息
        M('OrderInfoSer')->where(array('id'=>$order_id))->data($data)->save();
        //判断支付方式
        if($payment == 1 || $payment == 2){
            //修改用户第三方支付流水记录status为1 成功支付  第三方流水号
            $ct_data['status']          = 1;
            $ct_data['turnover_number'] = $turnover_number;
            M('CashTurnover')->where(array('order_id'=>$order_id, 'trend'=>1, 'payment'=>$payment))->data($ct_data)->save();
        }
        if($payment == 3) {
            $bt_data['status'] = 1;
            M('BalanceTurnover')->where(array('order_id'=>$order_id,'order_type'=>1,'trend'=>1))->data($bt_data)->save();
        }
        //提交订单到业务系统
//        D('FrontC/OrderInfo','Service')->dockOrder(array_merge($order_info, array('payment'=>$payment)));
        return true;
    }
    /**
     * @param $order_type
     * @param $payment
     * @param $order_id
     * @param $order_sn
     * @param $turnover_number
     * @return bool
     * 订单1类型后续处理
     */
    private function _orderType_1($order_type,$payment,$order_id,$order_sn,$turnover_number) {
        //获取订单信息
        $order_info = M('OrderInfo')->where(array('id'=>$order_id))->field('id order_id,order_sn,consignee,province_name,city_name,area_name,address,mobile,order_amounts,pay_amounts,status')->find();
        //根据订单状态 判断业务逻辑是否已经执行过  执行过则不再进行处理
        if($order_info['status'] != 1)
            return false;
        //判断请求时的total_fee、seller_id/mch_id与通知时获取的total_fee、seller_id/mch_id是否一致

        //未执行过 进行业务处理
        $data['status']         = 2; //订单状态  2已支付
        $data['payment']        = $payment; //支付方式
        $data['pay_status']     = 1; //支付状态
        $data['pay_time']       = NOW_TIME; //支付时间
        //修改订单相关信息
        M('OrderInfo')->where(array('id'=>$order_id))->data($data)->save();
        //判断支付方式
        if($payment == 1 || $payment == 2){
            //修改用户第三方支付流水记录status为1 成功支付  第三方流水号
            $ct_data['status']          = 1;
            $ct_data['turnover_number'] = $turnover_number;
            M('CashTurnover')->where(array('order_id'=>$order_id, 'trend'=>1, 'payment'=>$payment))->data($ct_data)->save();
        }
        if($payment == 3) {
            $bt_data['status'] = 1;
            M('BalanceTurnover')->where(array('order_id'=>$order_id,'order_type'=>1,'trend'=>1))->data($bt_data)->save();
        }
        //提交订单到业务系统
        D('FrontC/OrderInfo','Service')->dockOrder(array_merge($order_info, array('payment'=>$payment)));
        return true;
    }


    /**
     * @param $order_type
     * @param $payment
     * @param $order_id
     * @param $order_sn
     * @param $turnover_number
     * @return bool
     * 订单2、3类型后续处理
     */
    private function _orderType_2_3($order_type,$payment,$order_id,$order_sn,$turnover_number) {
        //获取订单信息
        $order_info = M('RechargeOrder')->where(array('id'=>$order_id))->field('user_id,order_type,rec_card_id,recharge_amounts,give_amounts,pay_amounts,status')->find();
        //根据订单状态 判断业务逻辑是否已经执行过  执行过则不再进行处理
        if($order_info['status'] == 1)
            return false;
        //判断请求时的total_fee、seller_id/mch_id与通知时获取的total_fee、seller_id/mch_id是否一致

        //订单状态改变
        $data['status']         = 1; //订单状态  1已支付
        $data['payment']        = $payment; //支付方式
        $data['pay_time']       = NOW_TIME; //支付时间
        //修改订单相关信息
        M('RechargeOrder')->where(array('id'=>$order_id))->data($data)->save();
        //现金记录状态改变
        //修改用户第三方支付流水记录status为1 成功支付  第三方流水号
        $ct_data['status']          = 1;
        $ct_data['turnover_number'] = $turnover_number;
        M('CashTurnover')->where(array('order_id'=>$order_id, 'trend'=>$order_info['order_type'], 'payment'=>$payment))->data($ct_data)->save();
        //根据订单类型执行不同的操作
        if($order_info['order_type'] == 2) {
            //获取用户信息
            $user_info = M('Member')->where(array('id'=>$order_info['user_id']))->field('balance')->find();
            //用户余额增加
            M('Member')->where(array('id'=>$order_info['user_id']))->setInc('balance', $order_info['recharge_amounts']);
            //用户余额记录
            D('FrontC/Finance', 'Service')->addBalanceTurnover($order_info['user_id'],1,1,2,$order_info['recharge_amounts'],$user_info['balance'],1,$order_id,$order_sn,2);
        }
        if($order_info['order_type'] == 3) {
            $card = M('RechargeCard')->where(array('id'=>$order_info['rec_card_id']))->field('name,bg_picture')->find();
            //用户充值码添加
            $cd_data = array(
                'user_id'       => $order_info['user_id'],
                'rec_card_id'   => $order_info['rec_card_id'],
                'name'          => $card['name'],
                'face_value'    => $order_info['recharge_amounts'],
                'sales_price'   => $order_info['pay_amounts'],
                'bg_picture'    => $card['bg_picture'],
                'diff_amounts'  => $order_info['recharge_amounts']-$order_info['pay_amounts'],
                'code'          => com_create_guid(),
                'create_time'   => NOW_TIME
            );
            M('RechargeCode')->data($cd_data)->add();
        }
        return true;
    }

    /**
     * @param array $param
     * 支付失败后操作
     * 黑暗中的武者
     */
    function errorPayDo($param = array()) {
        //订单号-订单ID-支付方式-订单类型(1购买订单 2充值订单)
        $out_trade_no = explode('-', $param['out_trade_no']);
        $order_type = array_pop($out_trade_no); //订单类型(1购买订单 2充值订单)
        $payment    = array_pop($out_trade_no); //支付方式
        $order_id   = array_pop($out_trade_no); //订单ID
        $order_sn   = array_pop($out_trade_no); //订单号

        /***预约订单支付失败后需处理的业务逻辑 start***/
        /*if($payment == 3) {
            //删除用户余额支付流水
            M('BalanceTurnover')->where(array('order_id'=>$order_id, 'trend'=>1))->delete();
        } else {
            //删除用户第三方支付流水
            M('CashTurnover')->where(array('order_id'=>$order_id, 'trend'=>1, 'payment'=>$payment))->delete();
        }*/
        //添加支付失败原因
        $data['order_id']       = $order_id;
        $data['order_sn']       = $order_sn;
        $data['order_type']     = $order_type;
        $data['trend']          = 1;
        $data['payment']        = $payment;
        $data['error_msg']      = $param['msg'];
        $data['notify']         = empty($param['notify']) ? '' : $param['notify'];
        $data['create_time']    = time();
        M('NotifyError')->data($data)->add();
        /***预约订单支付失败后需处理的业务逻辑 end***/
    }

    /**
     * @param array $request
     * @return bool
     * app同步回调操作
     */
    function appCallbackSer($request = array()) {
        $order = M('OrderInfoSer')->where(array('id'=>$request['order_id']))->field('status')->find();
        if($order['status'] < 2) {
            return $this->setLogicInfo('支付失败！', false);
        }
        return $this->setLogicInfo('支付成功！', true);
    }

    /**
     * @param array $request
     * @return bool
     * app同步回调操作
     */
    function appCallbackGroup($request = array()) {
        $order = M('OrderInfoSer')->where(array('id'=>$request['order_id']))->field('status')->find();
        if($order['status'] < 2) {
            return $this->setLogicInfo('支付失败！', false);
        }
        return $this->setLogicInfo('支付成功！', true);
    }

    /**
     * @param array $request
     * @return bool
     * app同步回调操作
     */
    function appCallback($request = array()) {
        $order = M('OrderInfo')->where(array('id'=>$request['order_id']))->field('status')->find();
        if($order['status'] < 2) {
            return $this->setLogicInfo('支付失败！', false);
        }
        return $this->setLogicInfo('支付成功！', true);
    }
}