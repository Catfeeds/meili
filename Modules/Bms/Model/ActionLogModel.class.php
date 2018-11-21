<?php
namespace Bms\Model;

/**
 * Class ActionLogModel
 * @package Bms\Model
 * 行为日志 模型
 */
class ActionLogModel extends BmsBaseModel {

    /**
     * @param array $param  综合条件参数
     * @return array
     * 获取列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('act_log')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //关联条件
        $join   = array(
            'LEFT JOIN '.C('DB_PREFIX').'action act ON act.id = act_log.act_id',
        );
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'act_log');
        //获取数据
        $list  = $this->alias('act_log')
                      ->field('act_log.id,act_log.create_time,act_log.record_model,act.unique_code,act.name,admin.account')
                      ->where($param['special_where'])
                      ->join(array_merge($join, array(
                          'LEFT JOIN '.C('DB_PREFIX').'administrator admin ON admin.id = act_log.admin_id',
                      )))
                      ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}