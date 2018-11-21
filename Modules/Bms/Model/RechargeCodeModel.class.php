<?php
namespace Bms\Model;

/**
 * Class RechargeCodeModel
 * @package Bms\Model
 * 充值码数据层
 */
class RechargeCodeModel extends BmsBaseModel {

    /**
     * @param array $param  综合条件参数
     * @return array
     * 获取列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('rec_code')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'rec_code');
        //获取数据
        $list  = $this->alias('rec_code')
            ->field('rec_code.id,rec_code.user_id,rec_code.user_type,rec_code.rec_card_id,rec_code.face_value,rec_code.code,rec_code.create_time,
            rec_code.status,rec_code.recharge_time,rec_code.recharger_id,rec_code.recharger_type,m1.account user_account,m1.id user_id,
            m2.account recharger_account,m2.id recharger_id')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'member m1 ON m1.id = rec_code.user_id',
                'LEFT JOIN '.C('DB_PREFIX').'member m2 ON m2.id = rec_code.recharger_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}