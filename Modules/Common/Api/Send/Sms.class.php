<?php
namespace Common\Api\Send;
use Think\Exception;

/**
 * Class Sms
 * @package Common\Api\Send
 * 发信类
 */
abstract class Sms {

    /**
     * @var null
     * 当前类对象
     */
    protected static $instance  = NULL;

    /**
     * @var string
     * 短信用户账号
     */
    protected $username     = '';

    /**
     * @var string
     * 短信用户密码
     */
    protected $password     = '';

    /**
     * @var string
     * 接收短信的手机号码 多个用逗号隔开
     */
    protected $phone        = '';

    /**
     * @var string
     * 发信内容
     */
    protected $msg          = '';

    /**
     * @var string
     * 签名【】
     */
    protected $sign         = '';

    /**
     * @param array $options
     * @throws Exception
     * 构造方法
     */
    public function __construct($options = []) {
        //参数判断
        if(empty($options['username']) || empty($options['password'])) {
            throw new Exception('用户名和密码错误！');
        } if(empty($options['phone'])) {
            throw new Exception('未找到发信对象！');
        } if(empty($options['msg'])) {
            throw new Exception('发信内容非法！');
        }
        $this->username     = $options['username'];
        $this->password     = $options['password'];
        $this->phone        = $options['phone'];
        $this->msg          = $options['msg'];
        $this->sign         = '【'.$options['sign'].'】';
        //子类初始化
        if(method_exists($this, '_initialize'))
            $this->_initialize($options);
    }

    /**
     * @param array $options
     * @return mixed
     */
    public static function instance($options = []) {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * @return mixed
     * 发送短信
     */
    abstract function send();
}