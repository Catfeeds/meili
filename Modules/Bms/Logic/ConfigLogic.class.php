<?php
namespace Bms\Logic;

/**
 * Class ConfigLogLogic
 * @package Bms\Logic
 * 系统配置 逻辑层
 */
class ConfigLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     */
    function getList($request = array()) {
        //按配置名称查询
        if(!empty($request['name'])) {
            $param['where']['name'] = $request['name'];
        }
        //按配置分组查询
        if(!empty($request['config_group'])) {
            //按配置名称
            $param['where']['config_group'] = $request['config_group'];
        }
        //排序条件
        $param['order']     = 'create_time DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //查询的字段
        $param['field']     = 'id,name,title,config_group,type,status';
        //返回数据
        return D('Config')->getList($param);
    }

    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 新增、更新、修改状态、删除后执行
     */
    protected function afterAll($result = 0, $request = array()) {
        S('Config_Cache', null);
        return true;
    }

    /**
     * @param array $request
     * @return boolean
     * 彻底删除前执行 将数据存入回收站
     * array(array('Comment','m_id','child'=>array('aaa','bb')),array())
     */
    protected function beforeRemove($request = array()) {
        if(api('Recycle/recovery',array($request,'Config','title'))) {
            return true;
        }
        $this->setLogicInfo('删除失败！');  return false;
    }
}