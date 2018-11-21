<?php
namespace Bms\Logic;

/**
 * Class FeedbackLogic
 * @package Bms\Logic
 * 意见反馈 逻辑层
 */
class FeedbackLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'create_time DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //查询的字段
        $param['field']     = 'id,contact,content,ip,create_time,finish_time,status';
        //返回数据
        return D('Feedback')->getList($param);
    }

    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 修改字段成功后执行
     */
    protected function afterSetField($result = 0, $request = array()) {
        M('Feedback')->where(array('id'=>$request['ids']))->data(array('finish_time'=>time()))->save();
        return true;
    }
}