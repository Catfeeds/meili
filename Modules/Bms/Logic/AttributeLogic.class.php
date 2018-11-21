<?php
namespace Bms\Logic;

/**
 * Class AttributeLogic
 * @package Bms\Logic
 * 属性逻辑层
 */
class AttributeLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        $param['where']['attr.type_id'] = $request['type_id'];
        //排序
        $param['order'] = 'attr.id DESC';
        //返回数据
        return D('Attribute')->getList($param);
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     */
    protected function processData($data = array(), $request = array()) {
        return $data;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['attr.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //别名
        $param['alias'] = 'attr';
        $row = D('Attribute')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //返回数据
        return $row;
    }
}