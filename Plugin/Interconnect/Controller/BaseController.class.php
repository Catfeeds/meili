<?php
namespace Plugin\Interconnect\Controller;
use Think\Controller;

/**
 * Class BaseController
 * @package Plugin\Interconnect\Controller
 * 互联登录基类 子类必须实现 login  callback方法
 */
abstract class BaseController extends Controller {

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() { }

    /**
     * @param $apps
     * @param $callback
     * @return mixed
     * 登陆跳转抽象方法  每个之类必须实现
     */
    abstract function login($apps, $callback);

    /**
     * @param $apps
     * @param $callback
     * @return mixed
     * 回调函数 每个之类必须实现
     */
    abstract function callback($apps, $callback);

    /**
     * @param $url
     * @param $method
     * @param $params
     * @param bool $multi
     * @return string
     * 网络请求中转
     */
    protected function oauthRequest($url, $method, $params, $multi = false) {
        switch ($method) {
            case 'GET':
                $url = $url . '?' . http_build_query($params);
                return $this->_http($url, 'GET');
            default:
                $headers = array();
                if (!$multi && (is_array($params) || is_object($params)) ) {
                    $body = http_build_query($params);
                } else {
                    $body       = self::build_http_query_multi($params);
                    $headers[]  = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
                }
                return $this->_http($url, $method, $body, $headers);
        }
    }

    /**
     * @param $url
     * @param $method
     * @param null $params
     * @param array $headers
     * @return mixed
     * 网络请求
     */
    private function _http($url, $method, $params = null, $headers = array()) {
        $ci = curl_init();
        //Curl 设置
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
        //设置版本
        if (version_compare(phpversion(), '5.4.0', '<')) {
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
        } else {
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
        }
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($params)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                }
                break;
            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    $url = "{$url}?{$params}";
                }
        }

        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

        $response = curl_exec($ci);
        curl_close ($ci);

        return $response;
    }
}