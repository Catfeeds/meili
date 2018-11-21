<?php
namespace Bms\Model;

/**
 * Class UserBankcardModel
 * @package Bms\Model
 * 用户银行卡
 */
class UserBankcardModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array ();

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array();

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total  = $this->alias('u_card')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'u_card');
        //获取数据
        $list   = $this->alias('u_card')
            ->field('u_card.id,u_card.user_id,u_card.open_name,u_card.card_number,u_card.card_type,u_card.bank_name,u_card.bank_logo,
            u_card.create_time,m.account,m.nickname')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = u_card.user_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}