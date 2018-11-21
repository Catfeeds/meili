<?php
namespace FrontC\Model;

/**
 * Class OrderInfoModel
 * @package FrontC\Model
 * 订单数据层
 * 黑暗中的武者
 */
class OrderInfoModel extends FrontBaseModel {

    /**
     * @var array
     * 自动验证规则
     * 黑暗中的武者
     */
    protected $_validate = array();

    /**
     * @var array
     * 自动完成规则
     * 黑暗中的武者
     */
    protected $_auto = array();

    /**
     * @param array $param
     * @return array
     * 基本列表
     * 黑暗中的武者
     */
    public function getList($param = array()) {
        //数据总数
        $total  = $this->alias('oi')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'oi');
        //获取数据
        $list   = $this->alias('oi')->field($param['field'])->where($param['special_where'])->join($param['join'])->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list, 'order_id'), 'page'=>$Page->show());
    }

    /**
     * @param array $param
     * @return array|mixed
     * 订单详情
     */
    function findRow($param = array()) {
        return $this->alias('oi')
                    ->field('oi.id order_id,oi.order_sn,oi.consignee,oi.province_name,oi.city_name,oi.area_name,oi.address,oi.mobile,
                    oi.coupon_amounts,oi.goods_amounts,oi.pay_amounts,oi.refund_amounts,oi.remark,oi.logistics_number,oi.create_time,
                    oi.payment,oi.status,oi.is_comm')
                    ->where($param['where'])
                    ->join(array())
                    ->find();
    }
}