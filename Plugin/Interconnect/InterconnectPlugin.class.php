<?php
namespace Plugin\Interconnect;
use Common\Controller\Plugin;

/**
 * Class InterconnectPlugin
 * @package Plugin\Interconnect
 */
class InterconnectPlugin extends Plugin {

    /**
     * @var array
     * 插件信息
     */
    public $info = array(
        'name'          =>  'Interconnect',
        'title'         =>  '第三方互联登陆',
        'description'   =>  '使用QQ、新浪微博、腾讯微博等登陆',
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

    //
    public function interconnect($param){
        //参数
        $this->assign('plugins_param', $param);
        //配置
        $this->assign('plugins_config', $this->getConfig());

        $this->display('widget');
    }
}