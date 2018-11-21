<?php
namespace Bms\Model;

/**
 * Class SendLogModel
 * @package Bms\Model
 * 发信记录模型
 */
class SendLogModel extends BmsBaseModel {

    /**
     * @param array $param  综合条件参数
     * @return array
     * 获取列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('send_log')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //关联条件
        $join   = array(
            'LEFT JOIN '.C('DB_PREFIX').'administrator admin ON admin.id = send_log.sender',
        );
        //生成ID查询条件
        $param = $this->specialSearch($param,$Page,'send_log');
        //获取数据
        $list = $this->alias('send_log')
                     ->field('send_log.*,admin.account,send_temp.unique_code')
                     ->where($param['special_where'])
                     ->join(array_merge($join, array(
                         'LEFT JOIN '.C('DB_PREFIX').'send_template send_temp ON send_temp.id = send_log.template_id',
                     )))
                     ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}