<?php
namespace Common\Base;
use Think\Controller;

/**
 * Class BaseController
 * @package Common\Base
 * 所有模块 控制器父类
 *          总后台
 *          分后台
 *          Api
 *          Home
 *          Wap
 */
class BaseController extends Controller {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //读取站点配置  先读取缓存
        $config = S('Config_Cache');
        //判断配置缓存中是否有数据  如果没有数据库中获取
        if(!$config) {
            $config = D('Common/Config')->parseList();
            //设置为缓存
            S('Config_Cache',$config);
        }
        //添加配置到 C函数
        C($config);
    }

    /**
     * @param null $plugins
     * @param null $controller
     * @param null $action
     * @return bool
     * 用于调度各个扩展插件的URL访问需求
     */
    function execute($plugins = null, $controller = null, $action = null) {
        $result = D('Common/Plugins', 'Logic')->execute($plugins, $controller, $action);
        if(false === $result) {
            $this->error(D('Plugins','Logic')->getLogicInfo());
        }
    }

    /**
     * 访问的方法不存在 调用
     */
    protected function _empty() {
        echo 'no function error';
    }
}