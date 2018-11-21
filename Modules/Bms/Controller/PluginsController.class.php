<?php
namespace Bms\Controller;

/**
 * Class PluginsController
 * @package Bms\Controller
 * 插件控制器
 */
class PluginsController extends BmsBaseController {

    /**
     * 插件安装
     */
    function install() {
        $this->checkRule(self::$rule);
        $result = D('Plugins','Logic')->install(I('request.'));
        if($result) {
            $this->success(D('Plugins','Logic')->getLogicInfo());
        } else {
            $this->error(D('Plugins','Logic')->getLogicInfo());
        }
    }

    /**
     * 卸载插件
     */
    function uninstall() {
        $this->checkRule(self::$rule);
        $result = D('Plugins','Logic')->uninstall(I('request.'));
        if($result) {
            $this->success(D('Plugins','Logic')->getLogicInfo());
        } else {
            $this->error(D('Plugins','Logic')->getLogicInfo());
        }
    }

    /**
     * 进入配置页面
     */
    function config() {
        $this->checkRule(self::$rule);
        $result = D('Plugins','Logic')->config(I('request.'));
        if($result) {
            $this->assign('data',$result);
            $this->assign('breadcrumb_nav', L('_CONFIG_BREADCRUMB_NAV_'));
            $this->display('config');
        } else {
            $this->error(D('Plugins','Logic')->getLogicInfo());
        }
    }
}
