<?php
namespace Bms\Logic;

/**
 * Class ActionLogic
 * @package Bms\Logic
 * 行为信息 逻辑层
 */
class ActionLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'create_time DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //返回数据
        return D('Action')->getList($param);
    }

    /**
     * @param array $request
     * @return boolean
     * 彻底删除前执行 将数据存入回收站
     */
    protected function beforeRemove($request = array()) {
        if(api('Recycle/recovery',array($request,'Action','name'))) {
            return true;
        }
        $this->setLogicInfo('删除失败！');  return false;
    }
}