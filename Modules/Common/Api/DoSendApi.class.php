<?php
namespace Common\Api;
use Think\Exception;

/**
 * Class DoSendApi
 * @package Common\Api
 * 信息发送接口
 *  邮件
 *  短信
 *  站内信
 *  推送
 *  ...
 */
class DoSendApi {

    /**
     * @param $param
     * @return bool
     * 发送短信
     */
    public static function sms($param) {
        //加载配置
        C(load_config(CONF_PATH . 'send' . CONF_EXT));
        //发信参数
        $options = [
            'username'      => C('SMS_ACCOUNT'),
            'password'      => C('SMS_PASSWORD'),
            //'needStatus'   => C(''),
            //'port'         => C(''),
            //'sendTime'     => ',
            'phone'         => $param['receiver'],
            'msg'           => $param['content'],
            'sign'          => C('SMS_SIGN'),
            'driver'        => C('SMS_DRIVER'),
        ];
        //根据驱动类型初始化相关类
        $class = empty($options['driver']) ? '\\Common\\Api\\Send\\Driver\\Juchen' : '\\Common\\Api\\Send\\Driver\\' . ucwords(strtolower($options['driver']));
        //判断类是否存在
        if(class_exists($class)) {
            try {
                $Sms = $class::instance($options);
            } catch(Exception $e) {
                return $e->getMessage();
            }
            return $Sms->send();
        } else {
            return '未找到发信驱动！'; //类没有定义
        }
    }

    /**
     * @param $param
     * @return bool|string
     * @throws \Exception
     * @throws \Think\Exception
     * @throws \Vendor\PHPMailer\Exception
     * 发送邮件  $param = array(
     *                      'receiver' //接收者
     *                      'content'  //内容
     *                      'subject'  //标题
     *                      'attachment' //附件
     *              );
     */
    public static function email($param) {
        C(load_config(CONF_PATH . 'send' . CONF_EXT)); //加载配置
        //验证邮箱格式
        if(!preg_match(C('EMAIL'), $param['receiver'])) {
            return '邮箱格式不正确！';
        }
        //从Vendor/PHPMailer目录导入class.phpmailer.php类文件
        vendor('PHPMailer.class#phpmailer');
        //初始化PHPMailer对象
        $mail             = new \Vendor\PHPMailer\PHPMailer(true);
        //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->CharSet    = 'UTF-8';
        // 设定使用SMTP服务
        $mail->IsSMTP();
        // 启用 SMTP 验证功能
        $mail->SMTPAuth   = true;
        // 关闭SMTP调试功能
        $mail->SMTPDebug  = 0;
        // 使用安全协议
        //$mail->SMTPSecure = 'ssl';
        // SMTP 服务器地址
        $mail->Host       = C('SMTP_HOST');
        // SMTP服务器的端口号
        $mail->Port       = C('SMTP_PORT');
        // SMTP服务器用户名
        $mail->Username   = C('SMTP_USER');
        // SMTP服务器密码
        $mail->Password   = C('SMTP_PASS');
        //回复地址   为空则回复地址为发送地址
        $replyEmail       = C('REPLY_EMAIL')    ? C('REPLY_EMAIL') : C('FROM_EMAIL');
        //回复名称   为空则回复名称为发送名称
        $replyName        = C('REPLY_NAME')     ? C('REPLY_NAME')  : C('FROM_NAME');
        //添加回复地址
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->From       = C('FROM_EMAIL');
        $mail->FromName   = C('FROM_NAME');
        //添加发送地址
        $mail->AddAddress($param['receiver'], $param['receiver']);
        //邮件标题
        $mail->Subject    = $param['subject'];
        //纯文本内容
        $mail->AltBody    = "";
        //设置多少个字符串就换行
        $mail->WordWrap   = 80;
        //先上传到本地 添加附件  本地文件  相对路径
        if(is_array($param['attachment'])){
            foreach ($param['attachment'] as $file){
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        //邮件主体
        $mail->MsgHTML($param['content']);
        //是否支持html
        $mail->IsHTML(true);

        return $mail->Send() ? true : $mail->ErrorInfo;
    }

    /**
     * @param $param
     * @return bool|string
     * 发送站内信
     */
    public static function siteMsg($param) {
        //判断是否数字ID
        if(!is_numeric($param['receiver'])) {
            return '接收ID非法！';
        }
        if(empty($param['content'])) {
            return '发信内容为空！';
        }
        //站内信参数
        $data = array(
            'user_id'       => $param['receiver'],      //接收者ID
            'user_type'     => empty($param['user_type']) ? 1 : $param['user_type'],      //用户类型
            'model'         => I('request.model'),  //模型名称  以便多种类型用户分表存储是判别
            'admin_id'      => empty($param['sender']) ? 0 : $param['sender'],  //发送者ID
            'subject'       => empty($param['subject']) ? '' : $param['subject'], //标题
            'content'       => $param['content'], //发送内容
            'create_time'   => time(),   //发送时间
        );
        //添加
        if(!M('SiteMessage')->add($data)) {
            return '系统繁忙！';
        }
        return true;
    }

    /**
     * @param $param
     * @return bool|string
     * APP推送信息
     */
    public static function push($param) {
        //加载配置
        C(load_config(CONF_PATH . 'send' . CONF_EXT));
        //引入自动加载函数
        vendor('jpush.autoload');
        //初始化推送类  根据用户类型获取响应应用的秘钥
        try {
            $client = new \JPush\Client(C('JPUSH_APP_KEY_' . $param['user_type'] . ''), C('JPUSH_MASTER_SECRET_' . $param['user_type'] . ''));
        } catch (\InvalidArgumentException $e) {
            return $e->getMessage();
        }
        //处理接收者
        $receivers = explode(',', $param['receiver']);
        foreach($receivers as $res) {
            $receiver_array[] = ''.$res.'';
        }
        //执行推送  获取异常  设置参数
        try {
            $response = $client->push()
                ->setPlatform(array('ios', 'android')) //接收平台
                ->addAlias($receiver_array) //设备别名  接收者  一次推送最多 1000 个，每一个 alias 的长度限制为 40 字节
                //->addTag(array('1', '2'))  //设备标签  接收者 一次推送最多 20 个， 每一个 tag 的长度限制为 40 字节
                //->addRegistrationId(array()) //注册ID  接收者 设备标识。一次推送最多 1000 个。
                //->setNotificationAlert('Hi, JPush') //所有平台的消息提示
                //IOS平台消息设置
                ->iosNotification($param['content'], array(
                    'sound'             => 'default', //声音类型  如果无此字段，则此消息无声音提示
                    'badge'             => 2, //应用角标  如果不填，表示不改变角标数字；否则把角标数字改为指定的数字；为 0 表示清除。JPush 官方 API Library(SDK) 会默认填充badge值为"+1"
                    'content-available' => false, //推送唤醒  推送的时候携带"content-available":true 说明是 Background Remote Notification，如果不携带此字段则是普通的Remote Notification。
                    'category'          => 'jiguang', //IOS8才支持。设置APNs payload中的"category"字段值
                    'extras'            => array(  //这里自定义 Key/value 信息，以供业务使用
                        'target_rule'   => $param['target_rule'],
                        'param'         => $param['param'],
                    ),
                ))
                //安卓消息设置
                ->androidNotification($param['content'], array(
                    'title'     => $param['subject'], //通知标题  如果指定了，则通知里原来展示 App名称的地方，将展示成这个字段。
                    //'build_id'  => 2, //通知栏样式ID  Android SDK 可设置通知栏样式，这里根据样式 ID 来指定该使用哪套样式。
                    'extras'    => array( //这里自定义 JSON 格式的 Key/Value 信息，以供业务使用
                        'target_rule'   => $param['target_rule'],
                        'param'         => $param['param'],
                    ),
                ))
                //公共消息设置
//                ->message('message content', array(
//                    'title'           => 'hello jpush',
//                    'content_type'    => 'text',
//                    'extras'          => array(
//                        'key' => 'value'
//                    ),
//                ))
                ->options(array(
//                    'sendno' => 100, //推送序号 纯粹用来作为 API 调用标识，API 返回时被原样返回，以方便 API 调用方匹配请求与返回。
                    'time_to_live'      => 3600, //离线消息保留时长(秒) 推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。默认 86400 （1 天），最长 10 天。设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到。
                    'apns_production'   => false, //APNs是否生产环境 True 表示推送生产环境，False 表示要推送开发环境；如果不指定则为推送生产环境。JPush 官方 API LIbrary (SDK) 默认设置为推送 “开发环境”。
//                    'big_push_duration' => 100 //定速推送时长(分钟) 又名缓慢推送，把原本尽可能快的推送速度，降低下来，给定的n分钟内，均匀地向这次推送的目标用户推送。最大值为1400.未设置则不是定速推送
                ))
                ->send();
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            //M('Feedback')->data(array('content'=>$e.message))->add();
            return $e.message;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            //M('Feedback')->data(array('content'=>$e.message))->add();
            return $e.message;
        }

        if($response['http_code'] != 200) {
            return '系统繁忙！';
        }
        //TODO 站内信同步发送

        return true;
    }
}