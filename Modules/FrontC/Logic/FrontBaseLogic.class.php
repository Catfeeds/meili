<?php
namespace FrontC\Logic;
use Common\Base\BaseLogic;

/**
 * Class FrontBaseLogic
 * @package FrontC\Logic
 * 前端集体逻辑层父类
 *  Api
 *  Home
 *  Wap
 */
class FrontBaseLogic extends BaseLogic {

    /**
     * @param string $info
     * @param bool $flag
     * @param array $return
     * @return array|bool|string
     * 设置提示信息
     */
    protected function setLogicInfo($info = '', $flag = false, $return = array()) {
        $this->logicInfo = $info;
        if($flag == false)
            return false;
        if($flag == true && !empty($return))
            return $return;
        return true;
    }
}