<?php
namespace Common\Api;

/**
 * Class SystemApi
 * @package Common\Api
 * 获取文件信息的接口
 */
class FileApi {

    /**
     * @param string $ids
     * @param array $fields
     * @return array
     * 获取文件列表 并排序
     */
    public static function getFiles($ids = '', $fields = array()) {
        //没有ID返回空数组
        if(empty($ids)) {
            return array();
        }
        //判断ids是否存在‘,’ 如果存在则是多图  不存在全部按单图处理
        $param['where']['file.id'] = (false !== strpos($ids, ',')) ? array('IN', $ids) : $ids;
        //如果存在指定file表字段 清空关联条件 设置字段名称
        if(!empty($fields)) {
            //清空关联条件
            $param['join'] = array();
            //设置字段名称
            foreach($fields as $value) {
                $param['field'][] = 'file.' . $value;
                //存在thumb_prefix 则获取缩略图
                if($value == 'thumb_prefix') {
                    $param['field'][] = 'file.ext'; //文件扩展名
                    $param['field'][] = 'file.thumb_suffix'; //文件后缀名
                }
            }
        }
        //TODO  生成缩略图路径
        $files = D('File','Service')->select($param);
        //多图情况  按照ids排序
        return (false !== strpos($ids, ',')) ? sort_by_array(explode(',', $ids), $files['list'], 'id') : $files['list'];
    }
}