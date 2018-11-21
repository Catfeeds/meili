<?php
namespace FrontC\Service;

/**
 * Class FlowService
 * @package FrontC\Service
 * 下单流程服务层
 */
class FlowService extends FrontBaseService {

    /**
     * @param int $m_cpn_id
     * @param array $coupon
     * @return bool
     * 判断优惠券是否可用
     */
    function checkCoupon($m_cpn_id = 0, $coupon = array()) {
        if(empty($m_cpn_id) && empty($coupon))
            return $this->setServiceInfo('未正确选择优惠券！', false);
        //优惠券信息是传参 还是 即时获取
        if(empty($coupon)) {
            $coupon = M('MemberCoupon')->where(array('id'=>$m_cpn_id))->field('status')->find();
        }
        if($coupon['status'] > 0)
            return $this->setServiceInfo('优惠券已被使用！', false);
        return true;
    }

    /**
     * @param int $m_id
     * @param int $amounts
     * @return int
     * 获取可用的积分数量和抵扣金额
     */
    function getItgDed($m_id = 0, $amounts = 0) {
        //存在每单可用积分抵扣金额比例的情况--根据支付金额计算可以使用积分抵扣的金额, 格式化为小于计算值的最大整数--floor((C('ENABLE_INTEGRAL_PRO') / 100) * $amounts)
        //计算抵扣这些金额需要多少积分
        $need_integral = $amounts * C('INTEGRAL_DED_PRO');
        //获取用户的积分
        $info = M('Member')->where(array('id'=>$m_id))->field('integral')->find();
        //判断用户当前积分是否大于所积分  如果大于则可使用积分为$need_integral所需积分 如果小于则可使用积分为当前积分
        if($info['integral'] <= $need_integral)
            $available_integral = $info['integral'] <= 0 ? 0 : $info['integral'];
        else
            $available_integral = $need_integral;
        //可以使用积分和可抵扣金额
        $info['available_integral']     = $available_integral; //本单做多可用积分
        $info['integral_ded_amounts']   = D('FrontC/Finance', 'Service')->getDed($available_integral); //可抵扣金额 四舍五入坳留一位小数
        return $info;
    }

    /**
     * @param int $flag
     * @return string
     * 生成订单号
     * 黑暗中的武者，baoyuan
     */
    function createSn($flag = 0) {
        switch($flag) {
            case 0 : return 'A' . time() . get_vc(4, 2); break;
			case 1 : return 'W' . time() . get_vc(4, 2); break;
			case 3 : return 'S' . time() . get_vc(4, 2); break;
			case 4 : return 'B' . time() . get_vc(4, 2); break;
			case 5 : return 'G' . time() . get_vc(4, 2); break;
        }
    }
}