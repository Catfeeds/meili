<?php
namespace FrontC\Service;
use Common\Base\BaseService;

/**
 * Class FrontBaseService
 * @package FrontC\Service
 * 前端集体服务层父类
 *  Api
 *  Home
 *  Wap
 */
class FrontBaseService extends BaseService {

    /**
     * @param string $info
     * @param bool $flag
     * @return string|void
     * 设置信息
     */
    protected function setServiceInfo($info = '', $flag = false) {
        $this->serviceInfo = $info; return $flag;
    }
}