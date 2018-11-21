<?php
// +----------------------------------------------------------------------
// | API端共用方法
// +----------------------------------------------------------------------
/**
 * @param string $flag
 * @param string $message
 * @param array $data
 * 接口返回格式
 */
function api_response($flag = '', $message = '', $data = array()) {
    print json_encode(array(
        'flag'      => $flag,
        'message'   => $message,
        'data'      => $data,
    ));
    exit;
}

/**
 * @param array $list
 * @return array
 * 将null值转化成空字符串
 */
function null2str($list = array()) {
    if(empty($list))
        return array();
    //判断数组维度
    if(array_dimension($list) == 1) {
        foreach ($list as $k => $v) {
            $list[$k] = $v === null ? '' : $v;
        }
    } else {
        foreach ($list as &$row) {
            foreach ($row as $k => $v) {
                $row[$k] = $v === null ? '' : $v;
            }
        }
    }
    return $list;
}