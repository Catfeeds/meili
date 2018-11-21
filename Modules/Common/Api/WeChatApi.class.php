<?php
namespace Common\Api;

/**
 * Class WeChatApi
 * @package Common\Api
 * 微信相关
 * 黑暗中的武者
 */
class WeChatApi extends BaseApi {

    /**
     * @param string $out_trade_no 本站订单号 *
     * @param int $total_fee 总金额 *
     * @param string $notify_url 异步回调地址*
     * @param string $subject 订单标题
     * @param string $body 订单描述*
     * @param string $show_url
     * @return array
     * @throws \WxPayException
     * APP参数签名
     * 黑暗中的武者
     */
    public static function sign($out_trade_no = '', $total_fee = 0, $notify_url = '', $subject = '', $body = '', $show_url = '') {
        //判断参数是否合法 必填项是否为空
        if(empty($out_trade_no) || empty($total_fee) || empty($body) || empty($notify_url))
            return false;
        //APP配置
        define('WX_PAY_CONFIG', ucfirst(TERMINAL));
        //引入工具类
        vendor('Pay.WxPay.lib.WxPay#Api');
        //创建统一下单输入对象
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body); //商品或支付单简要描述
        $input->SetOut_trade_no(get_vc(2,2) . '-' . $out_trade_no); //商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号  get_vc 防止订单号重复
        $input->SetTotal_fee($total_fee * 100); //设置订单总金额，只能为整数，详见支付金额
        $input->SetNotify_url($notify_url);     //接收微信支付异步通知回调地址
        $input->SetTrade_type("APP");           //支付类型 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        //获取统一支付接口数据
        try {
            $order = \WxPayApi::unifiedOrder($input);
        } catch(\WxPayException $e) {
            return false;
        }
        //预下单返回参数
        if(!array_key_exists("appid", $order) || !array_key_exists("prepay_id", $order) || $order['prepay_id'] == "")
            return false;
        //初始参数
        $para = array(
            'appid'     => $order['appid'],
            'partnerid' => $order['mch_id'],
            'package'   => 'Sign=WXPay',
            'noncestr'  => $order['nonce_str'],
            'timestamp' => time(),
            'prepayid'  => $order['prepay_id'],
        );
        //排序
        ksort($para);
        //& 拼接参数
        $buff = "";
        foreach ($para as $k => $v) {
            if($k != "sign" && $v != "" && !is_array($v))
                $buff .= $k . "=" . $v . "&";
        }
        $buff = trim($buff, "&"). "&key=" . \WxPayConfig::KEY;
        //签名参数
        $para['sign'] = MD5($buff);

        return $para;
    }

    /**
     * @param string $out_trade_no 本站订单号 *
     * @param int $total_fee 总金额 *
     * @param string $notify_url 异步回调地址*
     * @param string $subject 订单标题 *
     * @param string $body 订单描述*
     * @param int $product_id  商品ID *
     * @return array
     * @throws \WxPayException
     * 扫码支付 统一下单
     * 黑暗中的武者
     */
    public static function code($out_trade_no = '', $total_fee = 0, $notify_url = '', $subject = '', $body = '', $product_id = 1) {
        //判断参数是否合法 必填项是否为空
        if(empty($out_trade_no) || empty($total_fee) || empty($body) || empty($notify_url))
            return false;
        //网站配置
        define('WX_PAY_CONFIG', 'Home');
        //引入工具类
        vendor('Pay.WxPay.lib.WxPay#Api');
        //创建统一下单输入对象
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($body); //商品或支付单简要描述
        $input->SetAttach($subject); //设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetOut_trade_no(get_vc(2,2) . '-' . $out_trade_no); //设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee($total_fee * 100); //设置订单总金额，只能为整数，详见支付金额
        $input->SetTime_start(date("YmdHis")); //设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
        $input->SetTime_expire(date("YmdHis", time() + 600)); //设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
        //$input->SetGoods_tag("洗护服务");
        $input->SetNotify_url($notify_url); //设置接收微信支付异步通知回调地址
        $input->SetTrade_type("NATIVE");    //支付类型 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        $input->SetProduct_id($product_id); //设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。 例如：商品ID *
        //生成直接支付url，支付url有效期为2小时,模式二
        vendor('Pay.WxPay.example.WxPay#NativePay');
        $notify = new \NativePay();
        $result = $notify->GetPayUrl($input);
        //失败返回
        /*array(
            'return_code' => 'FAIL',
            'return_msg' => '签名错误'
        );*/
        if($result['return_code'] == 'FAIL')
            return false;
        //统一支付接口返回的数据 unifiedOrder成功返回
        /*array(
            'appid'         => 'wx9a52521fdac9c3fb',
            'code_url'      => 'weixin://wxpay/bizpayurl?pr=5Boqk2j',
            'mch_id'        => '1336729401',
            'nonce_str'     => 'j8uucX9nZiVeJbbk',
            'prepay_id'     => 'wx20160810120442361c902b120120850177',
            'result_code'   => 'SUCCESS',
            'return_code'   => 'SUCCESS',
            'return_msg'    => 'OK',
            'sign'          => '151B2CA6DC245D81ED95D41DF8EA5E4A',
            'trade_type'    => 'NATIVE',
        );*/
        return urlencode($result["code_url"]);
        /*vendor('Pay.WxPay.example.phpqrcode.phpqrcode');
        error_reporting(E_ERROR);
        $url2 = urldecode($result["code_url"]);
        QRcode::png($url2);*/
    }

    /**
     * @param string $out_trade_no
     * @param int $total_fee
     * @param string $notify_url
     * @param string $subject
     * @param string $body
     * @return bool|\json数据
     * @throws \WxPayException
     * 微信版 微信支付
     */
    public static function jsPay($out_trade_no = '', $total_fee = 0, $notify_url = '', $subject = '', $body = '') {
        //判断参数是否合法 必填项是否为空
        if(empty($out_trade_no) || empty($total_fee) || empty($body) || empty($notify_url))
            return false;
        //网站配置
        define('WX_PAY_CONFIG', 'Wap');
        //引入相关类
        vendor('Pay.WxPay.lib.WxPay#Api');
        //初始化日志
        //vendor('Pay.WxPay.example.log');
        //$logHandler= new \Pay\V3\example\CLogFileHandler("../logs/".date('Y-m-d').'.log');
        //$log = \Pay\V3\example\Log::Init($logHandler, 15);
        //获取openid
        $openid = self::getOpenid();
        //创建站内提交下单参数
        $input = new \WxPayUnifiedOrder();
        $input->SetTime_start(date("YmdHis")); //设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
        $input->SetTime_expire(date("YmdHis", time() + 600)); //设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
        $input->SetTrade_type("JSAPI"); //设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        $input->SetOpenid($openid); //设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。
        $input->SetOut_trade_no(get_vc(2,2) . '-' . $out_trade_no); //设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee($total_fee * 100); //设置订单总金额，只能为整数，详见支付金额
        $input->SetBody($subject); //设置商品或支付单简要描述
        $input->SetAttach($body); //设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetGoods_tag($body); //设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
        $input->SetNotify_url($notify_url); //设置接收微信支付异步通知回调地址
        //微信下单参数返回 统一支付接口返回的数据
        try {
            $order = \WxPayApi::unifiedOrder($input);
        } catch(\WxPayException $e) {
            return false;
        }
        if(C('TS') == 1)
            M('Feedback')->add(array('content'=>json_encode($order)));
        //生成失败
        if(empty($order['prepay_id']))
            return false;
        //生成支付参数
        $param = self::getJsApiParam($order);
        return $param;
        //获取共享收货地址js函数参数
        //$editAddress = $jsApi->GetEditAddressParameters();
    }

    /**
     * @param int $transaction_id
     * @param float $refund_fee
     * @param float $total_fee
     * @param string $terminal
     * @param array $callback
     * @return array
     * 微信退款
     */
    public static function refund($transaction_id = 0, $refund_fee = 0.00, $total_fee = 0.00, $terminal = '', $callback = array()) {
        //参数判空
        if(empty($transaction_id) || empty($refund_fee) || empty($total_fee) || empty($terminal))
            return self::response('参数错误！');
        //配置
        define('WX_PAY_CONFIG', ucfirst($terminal));
        //引入工具类
        vendor('Pay.WxPay.lib.WxPay#Api');
        //TODO 记录日志
        //创建退款输入对象
        $input = new \WxPayRefund();
        $input->SetTransaction_id($transaction_id); //设置微信订单号
        $input->SetTotal_fee($total_fee * 100); //设置订单总金额，单位为分，只能为整数，详见支付金额
        $input->SetRefund_fee($refund_fee * 100); //设置退款总金额，订单总金额，单位为分，只能为整数，详见支付金额
        $input->SetOut_refund_no(\WxPayConfig::MCHID.date("YmdHis")); //商户户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔
        $input->SetOp_user_id(\WxPayConfig::MCHID); //设置操作员帐号, 默认为商户号
        //申请退款
        /*  失败返回
         * {
         *  "appid":"wxbddbd138b27f303d",
         *  "err_code":"ERROR", "REFUND_FEE_MISMATCH",
         *  "err_code_des":"可退款的余额不足", "订单金额或退款金额不一致",
         *  "mch_id":"1422513002",
         *  "nonce_str":"0sDxoIpQSGCjyxiA",
         *  "result_code":"FAIL",
         *  "return_code":"SUCCESS",
         *  "return_msg":"OK",
         *  "sign":"0A11C7A57CA4ACF4587172FD0DDC8893"
         * }
         * 成功返回
         * {
         *  "appid":"wxbddbd138b27f303d",
         *  "cash_fee":"1",
         *  "cash_refund_fee":"1",
         *  "coupon_refund_count":"0",
         *  "coupon_refund_fee":"0",
         *  "mch_id":"1422513002",
         *  "nonce_str":"43vDeRNg12Q1bcq3",
         *  "out_refund_no":"142251300220161223150419",
         *  "out_trade_no":"45-A14823756667374-72-2-1",
         *  "refund_channel":[],
         *  "refund_fee":"1",
         *  "refund_id":"2001592001201612230671008913",
         *  "result_code":"SUCCESS",
         *  "return_code":"SUCCESS",
         *  "return_msg":"OK",
         *  "sign":"6C77D9969EE7E2A8F6AC34E503B2F49A",
         *  "total_fee":"1",
         *  "transaction_id":"4001592001201612223575961579"
         * }
         */
        try {
            $result = \WxPayApi::refund($input);
        } catch(\WxPayException $e) {
            return self::response($e->errorMessage());
        }
        if(C('TS') == 1)
            M('Feedback')->add(array('content'=>json_encode($result)));
        //判断退款是否成功
        //1、请求成功
        if($result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            //2、执行成功
            if($result['result_code'] == 'SUCCESS' && !empty($result['transaction_id'])) {
                //执行回调方法
                call_user_func($callback, $result);
            } else {
                return self::response($result['err_code_des']);
            }
        } else {
            return self::response($result['err_code_des']);
        }
    }

    /**
     * @return mixed|\用户的openid
     * 获取微信用户openid
     */
    public static function getOpenid() {
        //获取JSAPI工具类对象
        $jsApi = JsApiPayObject::getInstance();
        //查看session中是否存在
        $openid = session('wxp.openid');
        if(!$openid) {
            $openid = $jsApi->GetOpenid();
            //存到session 不用每次调用都重新生成 切换账号后要清除该session
            session('wxp', array('openid'=>$openid));
        }
        return $openid;
    }

    /**
     * @param array $order
     * @return \json数据
     * 获取JSAPI支付参数
     */
    public static function getJsApiParam($order = array()) {
        //获取JSAPI工具类对象
        $jsApi = JsApiPayObject::getInstance();
        //获取jsapi支付的参数
        try {
            $param = $jsApi->GetJsApiParameters($order);
        } catch(\WxPayException $e) {
            return $e->errorMessage();
        }
        return $param;
    }

    /**
     * @return mixed|string
     * 获取access_token是公众号的全局唯一接口调用凭据，公众号调用各接口时都需使用access_token。开发者需要进行妥善保存。
     * access_token的存储至少要保留512个字符空间。access_token的有效期目前为2个小时，需定时刷新，重复获取将导致上次获取的access_token失效。
     * {"access_token":"ACCESS_TOKEN","expires_in":7200}
     * {"errcode":40013,"errmsg":"invalid appid"}
     * http请求方式: GET https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
     */
    public static function getToken() {
        //获取缓存文件中的token数据
        $token = S('WX_TOKEN');
        //如果不存在 或者 已过期 则重新获取
        if(!$token || $token['expires_in'] <= NOW_TIME) {
            //获取token数据
            $token = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.C('WX_APP_ID').'&secret='.C('WX_SECRET').'');
            //转化为数组
            $token = json_decode($token, 'array');
            //获取失败
            if(empty($token['access_token'])) {
                return '';
            }
            //获取成功
            //记录过期时间
            $token['expires_in'] = NOW_TIME + $token['expires_in'];
            //存入缓存文件
            S('WX_TOKEN', $token);
        }
        return $token['access_token'];
    }

    /**
     * 获取公众号用于调用微信JS接口的临时票据。正常情况下，jsapi_ticket的有效期为7200秒
     *
     * 生成签名之前必须先了解一下jsapi_ticket，jsapi_ticket是公众号用于调用微信JS接口的临时票据。正常情况下，jsapi_ticket的有效期为7200秒，通过access_token来获取。
     * 由于获取jsapi_ticket的api调用次数非常有限，频繁刷新jsapi_ticket会导致api调用受限，影响自身业务，开发者必须在自己的服务全局缓存jsapi_ticket 。
     * 1.参考以下文档获取access_token（有效期7200秒，开发者必须在自己的服务全局缓存access_token）：../15/54ce45d8d30b6bf6758f68d2e95bc627.html
     * 2.用第一步拿到的access_token 采用http GET方式请求获得jsapi_ticket（有效期7200秒，开发者必须在自己的服务全局缓存jsapi_ticket）：
     * https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi
     * 成功返回
     * {
     * "errcode":0,
     *  "errmsg":"ok",
     *  "ticket":"bxLdikRXVbTPdHSM05e5u5sUoXNKd8-41ZO3MhKoyN5OfkWITDGgnr2fwJ0m9E8NYzWKVZvdVtaUgWvsdshFKA",
     *  "expires_in":7200
     * }
     */
    public static function getTicket() {
        //获取缓存文件中的ticket数据
        $ticket = S('WX_TICKET');
        //如果不存在 或者 已过期 则重新获取
        if(!$ticket || $ticket['expires_in'] <= NOW_TIME) {
            //获取token
            $access_token = self::getToken();
            //获取失败
            if (empty($access_token)) {
                return '';
            }
            //得到token  获取临时票据
            $ticket = file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi');
            //转化为数组
            $ticket = json_decode($ticket, 'array');
            //获取失败
            if ($ticket['errcode'] != 0) {
                return '';
            }
            //获取成功
            //记录过期时间
            $ticket['expires_in'] = NOW_TIME + $ticket['expires_in'];
            //存入缓存文件
            S('WX_TICKET', $ticket);
        }
        return $ticket['ticket'];
    }

    /**
     * 签名算法
     * 签名生成规则如下：
     *   参与签名的字段包括noncestr（随机字符串）, 有效的jsapi_ticket, timestamp（时间戳）, url（当前网页的URL，不包含#及其后面部分） 。
     *   对所有待签名参数按照字段名的ASCII 码从小到大排序（字典序）后，使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串string1。
     *   这里需要注意的是所有参数名均为小写字符。对string1作sha1加密，字段名和字段值都采用原始值，不进行URL 转义。
     *   即signature=sha1(string1)。
     *   示例：noncestr=Wm3WZYTPz0wzccnWjsapi_ticket=sM4AOVdWfPE4DxkXGEs8VMCPGGVi4C3VM0P37wVUCFvkVAy_90u5h9nbSlYy3-Sl-HhTdfl2fzFy1AOcHKP7qgtimestamp=1414587457url=http://mp.weixin.qq.com?params=value
     * 步骤1. 对所有待签名参数按照字段名的ASCII 码从小到大排序（字典序）后，使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串string1：
     * jsapi_ticket=sM4AOVdWfPE4DxkXGEs8VMCPGGVi4C3VM0P37wVUCFvkVAy_90u5h9nbSlYy3-Sl-HhTdfl2fzFy1AOcHKP7qg&noncestr=Wm3WZYTPz0wzccnW&timestamp=1414587457&url=http://mp.weixin.qq.com?params=value
     * 步骤2. 对string1进行sha1签名，得到signature：0f9de62fce790f9a083d5c99e95740ceb90c27ed
     * 注意事项
     *   1.签名用的noncestr和timestamp必须与wx.config中的nonceStr和timestamp相同。
     *   2.签名用的url必须是调用JS接口页面的完整URL。
     *   3.出于安全考虑，开发者必须在服务器端实现签名的逻辑。
     * 如出现invalid signature 等错误详见附录5常见错误及解决办法。
     */
    public static function getSign() {
        //获取临时票据
        $jsapi_ticket   = self::getTicket();
        //获取失败
        if(empty($jsapi_ticket)) {
            return array();
        }
        //随机字符串
        $noncestr       = '1313113';
        //当前时间戳
        $timestamp      = NOW_TIME;
        //调用的网址
        $url            = 'http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'';  //使用URL键值对的格式（即key1=value1&key2=value2…）
        //签名字符串
        $sign_str = 'jsapi_ticket=' . $jsapi_ticket . '&noncestr=' . $noncestr . '&timestamp=' . $timestamp . '&url=' . $url . '';
        //返回参数
        return array('noncestr'=>$noncestr, 'timestamp'=>$timestamp, 'sign'=>sha1($sign_str));
    }



    public static function getUserInfo() {
        define('WX_PAY_CONFIG', ucfirst(strtolower(TERMINAL)));
        //获取openid
        $openid = api('WeChat/getOpenid');
        //获取token
        $token = api('WeChat/getToken');
        //获取用户信息
        $info = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$openid.'&lang=zh_CN');
        //转化为数组
        $info = json_decode($info, 'array');
        /*
         * 失败返回格式{"errcode":40013,"errmsg":"invalid appid"}
         * 成功返回
         * {
         *  "subscribe": 1,
         *  "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M",
         *  "nickname": "Band",
         *  "sex": 1,
         *  "language": "zh_CN",
         *  "city": "广州",
         *  "province": "广东",
         *  "country": "中国",
         *  "headimgurl":  "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
         *  "subscribe_time": 1382694957,
         *  "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
         *  "remark": "",
         *  "groupid": 0,
         *  "tagid_list":[128,2]
         * }
         */
        if(!empty($info['errcode'])) {
            return $info['errmsg'];
        }
        return $info;
    }

//    function downloadWeixinFile($url){
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        $package = curl_exec($ch);
//        $httpinfo = curl_getinfo($ch);
//        curl_close($ch);
//        $imageAll = array_merge(array('header' => $httpinfo), array('body' => $package));
//        return $imageAll;
//    }
//    function saveWeixinFile($save_dir, $filename, $filecontent) {
//        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
//            return false;
//        }
//        $local_file = fopen($save_dir.$filename, 'w');
//        if (false !== $local_file){
//            if (false !== fwrite($local_file, $filecontent)) {
//                fclose($local_file);
//            }
//        }
//    }


}

/**
 * Class JsApiPayObject
 * @package Common\Api
 * 获取jsapi实例对象 单例模式
 */
class JsApiPayObject {
    //保存例实例在此属性中
    private static $_instance;
    //构造函数声明为private,防止直接创建对象
    private function __construct() {
        return NULL;
    }
    //单例方法
    public static function getInstance() {
        if(!isset(self::$_instance)) {
            vendor('Pay.WxPay.lib.WxPay#JsApiPay');
            //初始化JS工具类
            self::$_instance = new \JsApiPay();
        }
        return self::$_instance;
    }
    //阻止用户复制对象实例
    public function __clone(){
        trigger_error('Clone is not allow', E_USER_ERROR);
    }
}