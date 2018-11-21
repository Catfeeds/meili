<?php
namespace Dock\Order;

/**
 * Class Order
 * @package Dock\Order
 * 订单
 */
class Order
{
    /**
     * @var null
     * 本类对象
     */
    public static $instance = null;

    /**
     * @var string
     * 订单提交接口地址
     */
    protected $url = 'http://120.26.209.43:9000/Api/Order/PlaceOrder';

    /**
     * @param array $options
     * 构造方法
     */
    public function __construct($options = [])
    {

    }

    /**
     * @param array $options
     * @return null|static
     * 单例初始化
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * @param null $order
     * @return bool
     * @throws \Exception
     * 提交订单
     */
    public function placeOrder($order = null)
    {
        if (empty($order)) {
            throw new \Exception('未找到订单对象！');
        }
        //curl访问接口，提交json格式订单数据
        //var_dump($order->order2json($order));
        $content = $this->_postCurl($order->order2json($order), C('DOCK_URL'));
        //记录日志
        \Think\Storage::append(__DIR__ . '/log.txt', $content . "\r");
        //处理返回值
        return \Dock\Response::handle($content);
    }

    /**
     * @param $data
     * @param $url
     * @param int $second
     * @throws \Exception
     * 以post方式提交data到对应的接口url
     */
    private function _postCurl($data, $url, $second = 30)
    {
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_TIMEOUT, $second); //设置超时
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        curl_setopt($ch, CURLOPT_URL, $url); //访问地址
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8')); //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, TRUE); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //提交的数据
        $response = curl_exec($ch); //运行curl
        //返回结果
        if ($response) {
            curl_close($ch);
            return $response;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new \Exception('curl出错，错误码：' . $error);
        }
    }
}