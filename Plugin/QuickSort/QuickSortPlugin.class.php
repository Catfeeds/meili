<?php
namespace Plugin\QuickSort;
use Common\Controller\Plugin;

/**
 * 列表头部快速排序插件
 * @author 黑暗中的武者
 */

class QuickSortPlugin extends Plugin {

    /**
     * @var array
     * 插件信息
     */
    public $info = array(
        'name'          => 'QuickSort',
        'title'         => '快速排序',
        'description'   => '列表头部快速排序插件',
        'status'        => 1,
        'author'        => '黑暗中的武者',
        'version'       => '0.1'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的quickSort钩子方法
    public function quickSort($param){
        //参数
        $this->assign('plugins_param', $param);
        //配置
        $this->assign('plugins_config', $this->getConfig());
        $this->display('widget');
    }
}