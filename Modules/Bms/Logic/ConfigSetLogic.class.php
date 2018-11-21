<?php
namespace Bms\Logic;

/**
 * Class ConfigLogLogic
 * @package Bms\Logic
 * 系统配置值 逻辑层
 */
class ConfigSetLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     */
    function getList($request = array()) {
        //配置分组
        $where['config_group'] = empty($request['config_group']) ? 1 : $request['config_group'];
        //获取数据
        $list = D('Config')->where($where)->select();
        //获取图片类型的图片信息
        foreach($list as &$conf) {
            //图片类型
            if($conf['type'] == 6) {
                $conf['value'] = api('File/getFiles', array($conf['value']));
            }
        }
        return array('list'=>$list);
    }

    /**
     * @param array $request
     * @return bool|mixed|void
     * 批量更新
     */
    function update($request = array()) {
        if($request['config'] && is_array($request['config'])){
            foreach ($request['config'] as $name => $value) {
                D('Config')->where(array('name'=>$name))->data(array('value'=>$value))->save();
            }
        }
        S('Config_Cache',null);
        $this->setLogicInfo('保存成功！'); return true;
    }
}