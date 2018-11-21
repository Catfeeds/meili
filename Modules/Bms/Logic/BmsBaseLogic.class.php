<?php
namespace Bms\Logic;
use MsC\Logic\MscBaseLogic;

/**
 * Class BmsBaseLogic
 * @package Bms\Logic
 *
 */
class BmsBaseLogic extends MscBaseLogic {

    /**
     * @param array $request
     * @return array
     * 返回列表
     */
    public function getList($request = array()) {
        return array();
    }

    /**
     * @param array $request
     * @return boolean
     * @return array
     * 返回一行数据
     */
    public function findRow($request = array()) {
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }

        $row = D($request['model'])->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        return $row;
    }
}