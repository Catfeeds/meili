<?php
namespace Bms\Logic;

/**
 * Class AdvertLogic
 * @package Bms\Logic
 * 广告逻辑层
 */
class AdvertLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'id DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //查询的字段
        $param['field']     = 'id,position,description,start_time,end_time,sort,status,target_rule';
        //返回数据
        return D('Advert')->getList($param);
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //获取数据
        $row = D('Advert')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取广告图
        $row['picture'] = api('File/getFiles', array($row['picture']));
        //返回数据
        return $row;
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(), $request = array()) {
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time']   = strtotime($data['end_time'] . ' 23:59:59');
        if($request['position'] == 3) {
            if(empty($data['target_rule'])) {
                $this->setLogicInfo('请选择跳转！'); return false;
            }
            if(empty($data['target_rule']) && $data['target_rule'] != 1) {
                $this->setLogicInfo('参数错误！'); return false;
            }
        }
        return $data;
    }

    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 新增、更新、修改状态、删除后执行
     */
    protected function afterAll($result = 0, $request = array()) {
        //清缓存
        S('Advert_Cache1', null);
        S('Advert_Cache2', null);
        S('Advert_Cache3', null);
        return true;
    }
}