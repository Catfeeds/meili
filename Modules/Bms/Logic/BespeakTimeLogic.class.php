<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/26
 * Time: 17:15
 */

namespace Bms\Logic;


class BespeakTimeLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //条件
        $param['where'] = array();
        //返回数据
        return D('BespeakTime')->getList($param);
    }
    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['bespeaktime.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        $row = D('BespeakTime')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
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
        $data['package_desc'] = $_POST['package_desc'];
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time']   = strtotime($data['end_time']);
        return $data;
    }

    /**
     * @param $result
     * @param array $request
     * @return boolean
     * 更新后执行
     */
    protected function afterUpdate($result = 0, $request = array()) {
        //判断是新增商品还是修改
        //$goods_id = empty($request['id']) ? $result : $request['id'];
        //更新商品属性
        //$this->updateGoodsAttr($goods_id);
        return true;
    }
}