<?php
namespace MsC\Controller;
use Common\Base\BaseController;

/**
 * Class MsCBaseController
 * @package MsC\Controller
 * 后台管理(系统后台，其他分管理后台)父控制器
 */
class MsCBaseController extends BaseController {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
    }
}