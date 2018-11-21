<?php
namespace FrontC\Model;

/**
 * Class RechargeCardModel
 * @package FrontC\Model
 * 商城充值卡数据
 */
class RechargeCardModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total = $this->alias('rec_card')->where($param['where'])->count();
        //创建分页对象
        $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'rec_card');
        //获取数据
        $list = $this->alias('rec_card')
            ->field('rec_card.id rec_card_id,rec_card.name,rec_card.face_value,rec_card.sales_price,rec_card.bg_picture')
            ->where($param['special_where'])
            ->select();
        //返回记录 根据ID顺序排序
        return array('list' => sort_by_array($param['ids_for_sort'], $list, 'rec_card_id'), 'page' => $Page->show());
    }
}