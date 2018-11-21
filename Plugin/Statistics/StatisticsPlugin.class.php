<?php
namespace Plugin\Statistics;
use Common\Controller\Plugin;

/**
 * Class StatisticsPlugin
 * @package Plugin\Statistics
 * 统计插件
 */
class StatisticsPlugin extends Plugin {

    /**
     * @var array
     * 插件信息
     */
    public $info = array(
        'name'          =>  'Statistics',
        'title'         =>  '基本统计插件',
        'description'   =>  '基本的柱形图、线形图、饼状图',
        'status'        =>  1,
        'author'        =>  '黑暗中的武者',
        'version'       =>  '0.1'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的statistics钩子方法
    public function statistics($param){
        //参数
        $this->assign('plugins_param', $param);
        //配置
        $this->assign('plugins_config', $this->getConfig());
        $this->display('widget');
    }
}