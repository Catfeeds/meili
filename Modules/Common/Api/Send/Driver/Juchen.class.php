<?php
namespace Common\Api\Send\Driver;

/**
 * Class Juchen
 * @package Common\Api\Send
 * 巨辰短信服务平台
 */
class Juchen extends \Common\Api\Send\Sms{

    /**
     * @var string
     * 是否需要状态回推,值为true或false,回推地址在后台设置
     */
    protected $needStatus   = 'true';

    /**
     * @var string
     * 扩展码，用户定义扩展码(不能超过三位)
     */
    protected $port         = '';

    /**
     * @var string
     * 发送时间( 格式1900-01-01 00:00:00)
     */
    protected $sendTime     = '';

    /**
     * @param $options
     * 初始化
     */
    public function _initialize($options) {
        //$this->needStatus   = $options['need_status'];
        //$this->port         = $options['port'];
        //$this->sendTime     = $options['send_time'];
    }

    /**
     * @return mixed
     * 发送短信
     */
    public function send() {
        //所有线上公开促发短信发送的接入，比如公开的验证码、注册等必须加以判断每个号码的发送限制、ip发送短信限制
        $post_data = "username=".$this->username."&passwd=".$this->password."&phone=".$this->phone."&msg=".urlencode($this->msg.$this->sign)."&needstatus=".$this->needStatus."&port=".$this->port."&sendtime=".$this->sendTime."";
        //php5.4或php6 curl版本的curl数据格式为数组   你们接入时要注意
        //$post_data = array("username"="账号","passwd"="密码","phone"="手机号码1,号码2,号码3"."msg"="您好,你的验证码:8888【企业宝】","needstatus"="true","port"='',"sendtime"='');
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, "http://www.qybor.com:8500/shortMessage");
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        $resultObj = json_decode($file_contents);
        if($resultObj->respcode == 0) {
            return true;
        } else {
            return "发送失败，失败原因：" . $resultObj->respdesc;
        }
    }

//    public static function sms1($param) {
//        C(load_config(CONF_PATH . 'send' . CONF_EXT)); //加载配置
//        //短信接口相关参数
//        $sms_account    = C('SMS_ACCOUNT');     //用户账号
//        $sms_password   = C('SMS_PASSWORD');    //用户密码
//        $sms_phone      = $param['receiver'];   //手机号
//        $sms_template   = $param['content'];    //发信内容
//        $need_status    = true;                 //是否需要状态报告 取值true或false
//        //$sms_template   = $sms_template; //处理信息的编码
//        $sms_url        = "http://120.24.167.205/msg/HttpSendSM?"; //发送的网址
//        $sms_url       .= "account=$sms_account&pswd=$sms_password&mobile=$sms_phone&msg=$sms_template&needstatus=$need_status";
//        //发送短信
//        //获取返回信息 处理返回信息
//        $sms_response   = explode(",", file_get_contents($sms_url));
//        //判断返回信息
//        if($sms_response[1] != 0)
//            return '发送失败！-' . $sms_response[1];
//        else
//            return true;
//    }
}