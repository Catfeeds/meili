<?php
namespace Common\Api;

/**
 * Class BaseApi
 * @package Common\Api
 * 功能接口基类
 */
class BaseApi {

    /**
     * @var string
     * 标准信息返回格式
     */
    protected static $_response = array('info' => '', 'status' => 0, 'data' => array(), 'url' => '');

    /**
     * 构造函数
     * @access public
     */
    public function __construct() {
        //初始化执行
        if(method_exists($this, '_initialize'))
            $this->_initialize();
    }

    /**
     * @param string $info
     * @param int $status
     * @param array $data
     * @param string $url
     * @return array
     * 设置返回信息
     */
    protected static function response($info = '', $status = 0, $data = array(), $url = '') {
        return array_merge(self::$_response, array(
            'info'      => $info,
            'status'    => $status,
            'data'      => $data,
            'url'       => $url,
        ));
    }
}