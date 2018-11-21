<?php
namespace Bms\Model;

/**
 * Class OrderInfoModel
 * @package Bms\Model
 * 订单管理 数据层
 */
class OrderInfoModel extends BmsBaseModel {

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
        $total  = $this->alias('oi')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'oi');
        //获取数据
        $list   = $this->alias('oi')
            ->field('oi.id,oi.order_sn,oi.m_id,oi.consignee,oi.province_name,oi.city_name,oi.area_name,oi.address,oi.mobile,oi.integral_ded_amounts,
            oi.coupon_amounts,oi.goods_amounts,oi.order_amounts,oi.pay_amounts,oi.logistics_number,oi.create_time,oi.payment,oi.pay_status,oi.pay_time,
            oi.status,oi.before_status,sp.name shop_name')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'shop sp ON sp.id = oi.shop_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }

    /**
     * @param array $param
     * @return array|mixed
     *
     */
    function findRow($param = array()) {
        return $this->alias('oi')
            ->field('oi.id,oi.order_sn,oi.consignee,oi.province_name,oi.city_name,oi.area_name,oi.address,oi.mobile,oi.integral_ded_amounts,
                    oi.coupon_amounts,oi.goods_amounts,oi.pay_amounts,oi.refund_amounts,oi.remark,oi.logistics_number,oi.create_time,oi.payment,oi.pay_status,oi.pay_time,
                    oi.delivery_time,oi.receiving_time,oi.apply_refund_time,oi.refund_reason,oi.cancel_refund_time,oi.finish_refund_time,cancel_order_time,refund_reason,
                    oi.status,oi.before_status,m.nickname,m.account,cash.turnover_number,cash.module,sp.name shop_name,
                    sp.address shop_address,sp.contacts shop_contacts,sp.mobile shop_mobile')
            ->where($param['where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = oi.m_id',
                'LEFT JOIN ' . C('DB_PREFIX') . 'cash_turnover cash ON cash.order_id=oi.id AND cash.payment=oi.payment AND cash.status=1',
                'LEFT JOIN ' . C('DB_PREFIX') . 'shop sp ON sp.id = oi.shop_id',
            ))
            ->find();
    }
}