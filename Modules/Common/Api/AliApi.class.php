<?php
namespace Common\Api;

/**
 * Class AliApi
 * @package Common\Api
 * 支付宝相关
 * 黑暗中的武者
 */
class AliApi {

    /**
     * @param string $out_trade_no 本站订单号 *
     * @param int $total_fee 支付金额 *
     * @param string $notify_url 异步回调 *
     * @param string $subject 订单名称 *
     * @param string $body 订单描述
     * @param string $show_url 需展示的商品或者地址
     * @return string
     * APP参数签名
     * 黑暗中的武者
     */
    public static function sign($out_trade_no = '', $total_fee = 0, $notify_url = '', $subject = '', $body = '', $show_url = '') {
        //判断参数是否合法 必填项是否为空
        if(empty($out_trade_no) || empty($total_fee) || empty($subject) || empty($notify_url) || empty($body))
            return false;
        //引入参数处理方法
        vendor('Pay.AliPay.JSDZPC.lib.alipay_core#function');
        //引入配置参数
        self::config2C(); $alipay_config = C('ALIPAY_CONFIG_API');
        //构造要请求的参数数组
        $parameter = array(
            'notify_url'        => '"' . $notify_url . '"',    //服务器异步回调通知地址
            'service'           => '"mobile.securitypay.pay"',                      //服务类型
            'partner'           => '"' . trim($alipay_config['partner']) . '"',     //合作身份者id
            'payment_type'      => '"1"',                                           //支付类型不能修改
            'seller_id'         => '"' . $alipay_config['seller_email'] . '"',      //收款支付宝账号
            'out_trade_no'      => '"' . get_vc(2,2) . '-' . $out_trade_no . '"',   //本站订单号 必填
            'subject'           => '"' . $subject . '"',                            //订单名称 必填
            'total_fee'         => '"' . $total_fee . '"',                          //付款金额 必填
            'body'              => '"' . $body . '"',                               //订单描述
            '_input_charset'    => '"' . trim(strtolower($alipay_config['input_charset'])) . '"', //字符编码格式
            'it_b_pay'          => '"30m"',                                         //支付过期时间
        );
        //除去待签名参数数组中的空值和签名参数
        $para_filter    = paraFilter($parameter);
        //$para_sort = argSort($para_filter);
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $para_str       = createLinkstring($para_filter);
        //$prestr = '&notify_url=http://www.toocms.com&_input_charset=utf-8&body=呵呵&out_trade_no=1463723452265162&partner=2088121900317297&payment_type=1&seller_id=2088121900317297&service=mobile.securitypay.pay&subject=呵呵&total_fee=1';
        return array('link_string'=>$para_str, 'private'=>$alipay_config['private']);
    }

    /**
     * @param string $out_trade_no
     * @param int $total_fee
     * @param string $notify_url
     * @param string $subject
     * @param string $body
     * @param string $show_url
     * @return array
     */
    function newSign($out_trade_no = '', $total_fee = 0, $notify_url = '', $subject = '', $body = '', $show_url = '') {
        Vendor("Pay.AliPay.alipay2.AopSdk");
        $aop = new \AopClient;
        $aop->gatewayUrl         = "https://openapi.alipay.com/gateway.do";
        $aop->appId              = "2017070307629148";
        $aop->rsaPrivateKey      = 'MIIEowIBAAKCAQEA5lOTGdUmKwr0PyyQQXGj1Yo0+zdRt28s9fXohWRe8AhX3jphIDF/sdYR29tCIUnC2+YhSz2KZsNRm6qgESBx50piDZu2ae27Fua7GQEHG0CpJBat1uUr18s4vxqeZ5LbGSPSOAOdTbLf0q6UN1+nKmmDW80ugxPVCMrQNdJRzdwYc4/EueupaylLjO7raFtkNho72IisGo43P8U6iE7WSynnnXeG3vyB1JkQx9Rw7DG6ZNVKLM0Gygo052G2n+6MZNldkfhGLShrhYfIxx3WSvBq1WczXiDJMxkyAtol1+WoquY1vsJs8DRUjmClXS2MUjE5KrUSQ75uGucf9p8FEQIDAQABAoIBAEmM5qyZJFcaaGTEFkzPvUGzoLeYMsAhAHqKzHXMV4TPJUAR5HIjXGOtuIY8viFjLJaMJpWgUVH/jU/obLULZ1Q0rJsr3lR1Vgk6JQUXDu3k+B5OH7U/+YAvpS9hkLQCcXTVu4unm9P0CwV8Olh0cy1YBFqMaw3wh1cHbPXC89yIkSQuEvKmqn9impmO8dvGR5hkHWLpCzehtY2b988VVA8Zm3xxSt/qn5ogKKO+xnCGBFG+9RSCyfoFnwVOyZLhGyhUVIHa1vT4sTs1ySiomZwUUP73zXyzrWt4C76OWg6py2B+S7xbsSc2HHEI0eRd2+p244YySVwlQs3FF41c8AECgYEA+PiByqHg0Zv4fRF7G05yTFTKm/9RDJOz6rH/4Vw7Ko7ejuLN4mPexwZYxutYCMU5CUwUnxFkCPgPj7DaSy5wLOs0DI3zVdwxMmABhbt+HGTOl4e892HsowmhE6RdmEpAiF8FGTG3HeFLO98bVyhDNgOvDpMw2rxREUlhWGTjwykCgYEA7NRP2NAYlARED4H9dYxOby/YwKSvnyCHi2NIBaktE2mIrDodax7dijWX78hulPr6ZN3/NwDkUA+iqDX3+LtYO1j3jpdnRwWeSoSO0fBlxATipOC83va1l0w9grEm6ySxVWfxyzKIAxV54i/9PseC/oXczwf7znKAjkxWWb22l6kCgYEAh7pyh5VYiu0MuqIdCvXpOdO/4Ot/s+uR2hDP/nvZhYn9qsfaleD8QmQjYc5LX/yk63yegVlpv7n8QcmYOARJAzP2XCHG1rgD2gKc0ds1FSWfutw1GGg6KWfGeH7Sx4MzSyUCEooX2iJIcYtfzFQW0AuSE9AKgjTHvTTT7OyTfoECgYB0xMXNt+S2blgEcWo/3/r4NVYgvdJdmhNatYvYRq6T6K/bgxfoLiK7N2t/bYqgaBK3UwG34/euRddEKr/l4rFBKb99jcb9LJb8VNl6R5ZVjLcW5jwZjvi/7XZSjvgKbmAFJSgBsRuAscETteeeYY6D4gqaBWyxQKGEB713p+N0aQKBgADMaj6ICoi3m0s7rwzWJSc6T4/WugTMRw6Y5NVs3V10LiVAW19oo60qjsaXIU0wVO1OSGzYBHC32Scf03OP2xWzOOLMkxrPWwH0kBT05gsGgS1VhWo792qu8/3wL+ti3TK0mpPgX1hZthGzF87oolDljU3OU/iFkrILqFCeV1Vw';
        $aop->format             = "json";
        $aop->charset            = "utf-8";
        $aop->signType           = "RSA2";
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5lOTGdUmKwr0PyyQQXGj1Yo0+zdRt28s9fXohWRe8AhX3jphIDF/sdYR29tCIUnC2+YhSz2KZsNRm6qgESBx50piDZu2ae27Fua7GQEHG0CpJBat1uUr18s4vxqeZ5LbGSPSOAOdTbLf0q6UN1+nKmmDW80ugxPVCMrQNdJRzdwYc4/EueupaylLjO7raFtkNho72IisGo43P8U6iE7WSynnnXeG3vyB1JkQx9Rw7DG6ZNVKLM0Gygo052G2n+6MZNldkfhGLShrhYfIxx3WSvBq1WczXiDJMxkyAtol1+WoquY1vsJs8DRUjmClXS2MUjE5KrUSQ75uGucf9p8FEQIDAQAB';
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new \AlipayTradeAppPayRequest();

        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        // $bizcontent = [
        //     "body"            => "App支付",
        //     "subject"         => "App支付",
        //     "out_trade_no"    => $out_trade_no,
        //     "timeout_express" => "10m",
        //     "total_amount"    => $total_fee,
        //     "project_code"    => "QUICK_MSECURITY_PAY"
        // ];

        // $bizcontent = json_encode($bizcontent);

        $bizcontent = "{\"body\":\"{$body}\","
            . "\"subject\": \"{$subject}\","
            . "\"out_trade_no\": \"{$out_trade_no}\","
            . "\"timeout_express\": \"10m\","
            . "\"total_amount\": \"{$total_fee}\","
            . "\"product_code\":\"QUICK_MSECURITY_PAY\""
            . "}";

        // $bizcontent = '{"body":"App支付","subject": "App支付","out_trade_no": "{$out_trade_no}","timeout_express": "30m","total_amount": "{$total_fee}","product_code":"QUICK_MSECURITY_PAY"}';

        $request->setNotifyUrl($notify_url);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        // echo htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
        return [
            "sign" => $response,
            "sn" => $out_trade_no
        ];
    }

    /**
     * @param string $out_trade_no 本站订单号 *
     * @param int $total_fee 支付金额 *
     * @param string $notify_url 异步回调 *
     * @param string $return_url 页面同步回调 *
     * @param string $subject 订单名称 *
     * @param string $body 订单描述
     * @param string $show_url 需展示的商品或者地址
     * @return string
     * 网站支付 参数提交跳转
     * 黑暗中的武者
     */
    public static function submit($out_trade_no = '', $total_fee = 0, $notify_url = '', $return_url = '', $subject = '', $body = '', $show_url = '') {
        //判断参数是否合法 必填项是否为空
        if(empty($out_trade_no) || empty($total_fee) || empty($return_url) || empty($notify_url) || empty($body) || empty($subject))
            return false;
        //引入支付宝各接口请求提交类
        vendor('Pay.AliPay.JSDZPC.lib.alipay_submit#class');
        //引入配置参数
        self::config2C(); $alipay_config = C('ALIPAY_CONFIG_HOME');
        //初始化该类
        $Submit = new \AlipaySubmit($alipay_config);
        //构造要请求的参数数组
        $parameter = array(
            "service"            => "create_direct_pay_by_user",
            "partner"            => trim($alipay_config['partner']),
            "payment_type"       => "1",
            "notify_url"         => $notify_url,
            "return_url"         => $return_url, //页面跳转同步通知页面路径
            "seller_email"       => $alipay_config['seller_email'],
            "out_trade_no"       => $out_trade_no,
            "subject"            => $subject,
            "total_fee"          => $total_fee,
            "body"               => $body,
            "show_url"           => $show_url,
            "anti_phishing_key"  => '', //防钓鱼时间戳 若要使用请调用类文件submit中的query_timestamp函数 有DONDocument类支持
            "exter_invoke_ip"    => get_client_ip(), //客户端的IP地址
            "_input_charset"     => trim(strtolower($alipay_config['input_charset'])),
        );
        //var_dump($parameter);exit;
        //调用提交方法
        $html_text = $Submit->buildRequestForm($parameter, "post", "支付跳转中...");
        echo $html_text;
    }

    /**
     * @param string $trade_no
     * @param float $refund_fee
     * @param string $notify_url
     * 退款操作
     */
    public static function refund($trade_no = '', $refund_fee = 0.0, $notify_url = '') {
        header('Content-type:text/html; charset=utf-8');
        //判断参数是否合法 必填项是否为空
        if(empty($trade_no) || empty($refund_fee) || empty($notify_url))
            return false;
        //引入支付宝各接口请求提交类
        vendor('Pay.AliPay.JSDZPC.lib.alipay_submit#class');
        //引入配置参数
        self::config2C(); C('ALIPAY_CONFIG_API.sign_type', strtoupper('MD5')); $alipay_config = C('ALIPAY_CONFIG_API');
        //初始化该类
        $Submit = new \AlipaySubmit($alipay_config);
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"           => "refund_fastpay_by_platform_pwd",
            "partner"           => trim($alipay_config['partner']),
            "notify_url"	    => $notify_url,     //服务器异步通知页面路径
            "seller_email"	    => $alipay_config['seller_email'],   //卖家支付宝帐户
            "refund_date"	    => date('Y-m-d H:i:s',time()),    //退款当天日期 必填，格式：年[4位]-月[2位]-日[2位] 小时[2位 24小时制]:分[2位]:秒[2位]，如：2007-10-01 13:13:13
            "batch_no"	        => date('Ymd',time()).get_vc(10,2),   //批次号 必填，格式：当天日期[8位]+序列号[3至24位]，如：201008010000001
            "batch_num"	        => '1',  //退款笔数 必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
            "detail_data"	    => '' . $trade_no . '^' . $refund_fee . '^用户退款',    //退款详细数据 交易退款数据集的格式为：原付款支付宝交易号^退款总金额^退款理由；
            "_input_charset"	=> trim($alipay_config['input_charset'])
        );
        //建立请求
        $html_text = $Submit->buildRequestForm($parameter, "get", "正在跳转...");
        echo $html_text;
    }

    /**
     * @param int $flag
     * 加载配置信息到 C
     */
    public static function config2C($flag = 0) {
        C(load_config(CONF_PATH . 'ali' . CONF_EXT));
    }
}