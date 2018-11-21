<?php
namespace Bms\Logic;

/**
 * Class CouponLogic
 * @package Bms\Logic
 * 优惠券逻辑层
 */
class CouponLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'id DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //返回数据
        $result = D('Coupon')->getList($param);
        //处理数据
        foreach($result['list'] as &$value) {
            $value['cate_name'] = M('GoodsCategory')->where(array('id'=>$value['goods_cate_id']))->getField('name');
        }

        return $result;
    }

    /**
     * @return mixed
     * 获取给用户发送时 使用的模板列表
     */
    function getSendTemplate() {
        return D('Coupon')->where(array('status'=>1))->field('id,unique_code,name,face_value,effective_date,valid_term,status')->select();
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(),$request = array()) {
        //处理生效日期
        $data['effective_date'] = strtotime($data['effective_date']);
        return $data;
    }
}