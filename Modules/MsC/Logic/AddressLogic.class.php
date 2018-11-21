<?php
namespace MsC\Logic;

/**
 * Class AddressLogic
 * @package MsC\Logic
 * 用户地址逻辑层
 */
class AddressLogic extends MscBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //别名
        $param['alias'] = 'adr';
        //ID查询
        if(!empty($request['m_id'])) {
            $param['where']['adr.m_id'] = $request['m_id'];
        }
        //排序条件
        $param['order']     = 'adr.id DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //返回数据
        return D('MsC/Address')->getList($param);
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //参数判断
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //查询的字段
        $param['field'] = 'id,contacts,mobile,city_id,area_id,circle_id,address';
        //获取数据
        $row = D('MsC/Address')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        return $row;
    }
}