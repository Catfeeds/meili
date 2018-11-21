<?php
namespace Bms\Logic;

/**
 * Class SendLogLogic
 * @package Bms\Logic
 * 发信记录逻辑层
 */
class SendLogLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     */
    function getList($request = array()) {
        //排序条件
        $param['order'] = 'send_log.id DESC';
        //返回数据
        return D('SendLog')->getList($param);
    }
}