<?php
namespace Bms\Logic;

/**
 * Class BankLogic
 * @package Bms\Logic
 * 银行信息 逻辑层
 */
class BankLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        $param['where'] = array();
        //返回数据
        $result = D('Bank')->getList($param);

        return $result;
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
        $row = D('Bank')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取图片
        $row['logo'] = api('File/getFiles', array($row['logo']));
        //返回数据
        return $row;
    }

    /**
     * @param $result
     * @param array $request
     * @return boolean
     * 更新后执行
     */
    protected function afterAll($result = 0, $request = array()) {
        //更新缓存
        S('Bank_Cache', D('Bank')->getCacheList());
        return true;
    }
}