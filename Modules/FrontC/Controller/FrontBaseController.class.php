<?php
namespace FrontC\Controller;
use Common\Base\BaseController;

/**
 * Class FrontBaseController
 * @package FrontC\Controller
 * 前端集体控制器父类
 *  Api
 *  Home
 *  Wap
 */
class FrontBaseController extends BaseController {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
    }
}