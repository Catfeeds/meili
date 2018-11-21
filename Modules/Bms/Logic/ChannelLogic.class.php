<?php
namespace Bms\Logic;

/**
 * Class ChannelLogic
 * @package Bms\Logic
 * 首页栏目逻辑层
 */
class ChannelLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //排序条件
        $param['where'] = array();
        //返回数据
        return D('Channel')->getList($param);
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(),$request = array()) {
        if(empty($request['id']))
            $data['sort'] = 9999;
        return $data;
    }

    /**
     * @param array $request
     * @return mixed
     * 详情
     */
    function findRow($request = array()) {
        //参数判断
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //获取数据
        $row = D('Channel')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //图标
        $row['icon'] = api('File/getFiles',array($row['icon']));

        return $row;
    }

    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 新增、更新、修改状态、删除后执行
     */
    protected function afterAll($result = 0, $request = array()) {
        //清缓存
        S('Channel_Cache', null);
        return true;
    }
}