<?php
namespace FrontC\Model;

/**
 * Class RechargeCodeModel
 * @package FrontC\Model
 * 充值码数据
 */
class RechargeCodeModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('rec_code')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'rec_code');
        //获取数据
        $list   = $this->alias('rec_code')
                ->field('rec_code.id rec_code_id,rec_code.name,rec_code.face_value,rec_code.bg_picture')
                ->where($param['special_where'])
//                ->join(array(
//                    'INNER JOIN ' . C('DB_PREFIX') . 'member m ON m.id = g_comm.m_id',
//                ))
                ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list, 'rec_code_id'), 'page'=>$Page->show());
    }
}