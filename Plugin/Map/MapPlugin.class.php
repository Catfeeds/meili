<?php
namespace Plugin\Map;
use Common\Controller\Plugin;

/**
 * Class MapPlugin
 * @package Plugin\Map
 * 地图相关插件
 */
class MapPlugin extends Plugin {

    /**
     * @var array
     * 插件基本信息
     */
    public $info = array (
        'name'          =>  'Map',
        'title'         =>  '地图标注',
        'description'   =>  '地图标注',
        'status'        =>  1,
        'author'        =>  '黑暗中的武者',
        'version'       =>  '0.1'
    );

    public function install() {
        return true;
    }

    public function uninstall() {
        return true;
    }

    //实现的upload钩子方法
    public function map($param) {
        $config = $this->getConfig();
        //参数
        $this->assign('plugins_param', $param);
        //配置
        $this->assign('plugins_config', $config);

        if($config['platform'] == 1) {
            $this->display('baidu');
        } elseif($config['platform'] == 2) {
            $this->display('gaode');
        }
    }
}