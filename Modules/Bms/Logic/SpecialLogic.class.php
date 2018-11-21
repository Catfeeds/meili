<?php
namespace Bms\Logic;

/**
 * Class SpecialLogic
 * @package Bms\Logic
 * 商品专题逻辑层
 */
class SpecialLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //查询条件
        $param['where'] = array();
        //返回数据
        return D('Special')->getList($param);
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(),$request = array()) {
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
        $row = D('Special')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        return $row;
    }

    /**
     * @param array $request
     * @return array
     * 获取专题商品列表
     */
    public function getSpeGoods($request = array()) {
        //查询条件
        $param['where']['spe_id'] = $request['spe_id'];
        //排序
        $param['order'] = 'spe_goods.id DESC';
        //返回数据
        return D('SpecialGoods')->getList($param);
    }

    /**
     * @param array $request
     * @return bool
     * 添加专题商品
     */
    public function addSpeGoods($request = array()) {
        //没有专题ID
        if(empty($request['spe_id'])) {
            $this->setLogicInfo('参数错误！'); return false;
        }
        if(empty($request['goods_ids'])) {
            $this->setLogicInfo('请选择商品！'); return false;
        }

        foreach($request['goods_ids'] as $goods_id) {
            $data[] = array('spe_id'=>$request['spe_id'],'goods_id'=>$goods_id);
        }

        if(!M('SpecialGoods')->addAll($data)) {
            $this->setLogicInfo('添加失败！'); return false;
        }
        $this->setLogicInfo('添加成功！'); return true;
    }
}