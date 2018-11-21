<?php
// +----------------------------------------------------------------------
// | 总后台公共文件
// | 主要定义总后台公共函数库
// +----------------------------------------------------------------------
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
    $admin = session('admin');
    if (empty($admin)) {
        return 0;
    } else {
        return session('admin_sign') == data_auth_sign($admin) ? $admin['a_id'] : 0;
    }
}

/**
 * @param null $a_id
 * @return boolean true-超管理员，false-非超管理员
 * 是否是超级管理员
 */
function is_administrator($a_id = null) {
    $a_id = is_null($a_id) ? is_login() : $a_id;
    return $a_id && (intval($a_id) === C('USER_ADMINISTRATOR'));
}

/**
 * @param string $type
 * @return mixed
 * 获取属性类型信息
 */
function get_attribute_type($type = '') {
    // TODO 可以加入系统配置
    static $_type = array(
        'num'       =>  array('数字','int(10) UNSIGNED NOT NULL'),
        'string'    =>  array('字符串','varchar(255) NOT NULL'),
        'textarea'  =>  array('文本框','text NOT NULL'),
        'datetime'  =>  array('时间','int(10) NOT NULL'),
        'bool'      =>  array('布尔','tinyint(2) NOT NULL'),
        'select'    =>  array('枚举','char(50) NOT NULL'),
    	'radio'		=>	array('单选','char(10) NOT NULL'),
    	'checkbox'	=>	array('多选','varchar(100) NOT NULL'),
    	'editor'    =>  array('编辑器','text NOT NULL'),
    	'picture'   =>  array('上传图片','int(10) UNSIGNED NOT NULL'),
    	'file'    	=>  array('上传附件','int(10) UNSIGNED NOT NULL'),
    );
    return $type?$_type[$type][0]:$_type;
}

/**
 * @param string $table
 * @return string
 * 获取表的中文名称
 */
function get_table_name($table = '') {
    switch ($table) {
        case 'Action'           : return    '行为表';       break;
        case 'ActionLog'        : return    '行为日志表';    break;
        case 'Administrator'    : return    '管理员表';    break;
        default                 : return    '';             break;
    }
}

/**
 * @param $status
 * @return string
 * 获取插件状态名称
 */
function get_plugins_status_title($status) {
    switch ($status) {
        case 1       : return    '启用';    break;
        case 9       : return    '损坏';    break;
        case null    : return    '未安装';  break;
        case 0       : return    '禁用';    break;
        default      : return    '';       break;
    }
}

/**
 * @param $status
 * @return string
 * 获取发送状态
 */
function get_send_status($status) {
    switch ($status) {
        case 0       : return    '失败';    break;
        case 1       : return    '成功';    break;
        default      : return    '';       break;
    }
}

/**
 * @param $type
 * @return string
 * 获取发送类型图标
 */
function get_send_icon($type) {
    switch ($type) {
        case 1       : return    'fa-comment';    break;
        case 2       : return    'fa-envelope';    break;
        case 3       : return    'fa-envelope-square';    break;
        case 4       : return    'fa-send';    break;
        default      : return    '';       break;
    }
}

/**
 * @param $cate
 * @return string
 * 模板类别
 */
function get_send_cate($cate) {
    switch ($cate) {
        case 1       : return    '系统模板';    break;
        case 2       : return    '自定义';    break;
        default      : return    '';       break;
    }
}

/**
 * @param string $model
 * @return string
 * 获取用户类型
 */
function get_user_type($model = '') {
    switch ($model) {
        case 'Member': return 1; break;
        default : return    ''; break;
    }
}