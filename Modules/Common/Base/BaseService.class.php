<?php
namespace Common\Base;

/**
 * Class BaseService
 * @package Common\Base
 * 公共服务层 基类
 */
class BaseService {

    /**
     * @var string
     * 接收服务层信息
     */
    protected $serviceInfo = '';

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
     * @param array $param
     * @param array $custom_param
     * @return array
     * 处理外部自定义条件
     *      页码
     *      条件
     *      关联
     *      排序
     *      字段
     */
    protected function customParam($param = array(), $custom_param = array()) {
        //别名
        if(isset($custom_param['alias']))
            $param['alias']     = $custom_param['alias'];
        //替换页码条件
        if(isset($custom_param['page_size']))
            $param['page_size'] = $custom_param['page_size'];
        //合并查询条件
        if(isset($custom_param['where']))
            $param['where']     = empty($param['where']) ? $custom_param['where'] : array_merge($param['where'], $custom_param['where']);
        //合并、清空关联条件
        if(isset($custom_param['join']))
            $param['join']      = empty($custom_param['join']) ? array() : array_merge($param['join'], $custom_param['join']);
        //新关联条件
        if(isset($custom_param['new_join']))
            $param['join']      = $custom_param['new_join'];
        //替换排序条件
        if(isset($custom_param['order']))
            $param['order']     = $custom_param['order'];
        //拼接字段条件  数组格式直接替换  字符串格式拼接
        if(isset($custom_param['field'])) {
            $mosaic             = empty($param['field']) ? '' : ','; //拼接符号
            $param['field']     = is_array($custom_param['field']) ? $custom_param['field'] : $param['field'] . $mosaic . $custom_param['field'];
        }
        return $param;
    }

    /**
     * @param string $info
     * @return string
     * 设置信息
     */
    protected function setServiceInfo($info = '') {
        $this->serviceInfo = $info;
    }

    /**
     * @return string
     * 获取信息
     */
    final public function getServiceInfo() {
        return $this->serviceInfo;
    }
}