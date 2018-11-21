<?php
namespace Common\Logic;
use Common\Base\BaseLogic;

/**
 * Class PluginsLogic
 * @package Common\Logic
 * 插件 逻辑层
 */
class PluginsLogic extends BaseLogic {

    /**
     * @param null $plugins
     * @param null $controller
     * @param null $action
     * @return bool
     * 执行插件控制器中的方法
     * 用于调度各个扩展的URL访问需求
     */
    function execute($plugins = null, $controller = null, $action = null) {
        if(C('URL_CASE_INSENSITIVE')) {
            $plugins       =   ucfirst(parse_name($plugins, 1));
            $controller    =   parse_name($controller,1);
        }

        if(!empty($plugins) && !empty($controller) && !empty($action)){
            A("Plugin://{$plugins}/{$controller}")->$action();
        } else {
            $this->setLogicInfo('没有指定插件名称，控制器或操作！'); return false;
        }
    }
}