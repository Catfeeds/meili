<?php
namespace FrontC\Service;

/**
 * Class FinanceService
 * @package FrontC\Service
 * 财务相关数据服务层
 * 积分、余额、优惠券等
 */
class FinanceService extends FrontBaseService {

    /**
     * @param int $m_id
     * @param int $symbol
     * @param int $trend
     * @param int $number
     * @param string $reason
     * @param int $order_id
     * @param array $callback  回调函数 array(Object,function) call_user_func
     * @return bool
     * 添加积分记录操作
     */
    function addItgLog($m_id = 0, $symbol = 0, $trend = 0, $number = 0, $reason = '', $order_id = 0, $callback = array()) {
        //参数判断
        if(empty($m_id) || empty($symbol) || empty($trend)) {
            return $this->setServiceInfo('参数错误！', false);
        }
        //获取用户积分
        $info = M('Member')->where(array('id'=>$m_id))->field('integral')->find();
        //变动后积分
        $after_number = $symbol == 1 ? $info['integral'] + $number : $info['integral'] - $number;
        //提交参数
        $data = array(
            'm_id'          => $m_id,
            'symbol'        => $symbol,
            'trend'         => $trend,
            'number'        => empty($number) ? 0 : $number,
            'create_time'   => NOW_TIME,
            'before_number' => $info['integral'],
            'after_number'  => $after_number,
            'reason'        => empty($reason) ? '' : $reason,
            'order_id'      => empty($order_id) ? 0 : $order_id,
        );
        //添加记录
        if(!M('IntegralLog')->data($data)->add()) {
            return $this->setServiceInfo('添加积分记录失败！', false);
        }
        //操作用户积分
        if($symbol == 1) {
            M('Member')->where(array('id'=>$m_id))->setInc('integral', $number);
        }
        if($symbol == 2) {
            M('Member')->where(array('id'=>$m_id))->setDec('integral', $number);
        }
        return $this->setServiceInfo('操作成功！', true);
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     * 积分记录列表
     */
    function integralLogs($custom_param, $extra = array()) {
        //排序
        $param['order'] = 'itg_log.id DESC';
        //每页数量
        $param['page_size'] = 12;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/IntegralLog')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        foreach($list as &$value) {
            $value['symbol_name'] = $this->symbol2str($value['symbol']);
            $value['trend_name']  = $this->trend2str($value['trend']);
            $value['create_time'] = timestamp2date($value['create_time']);
        }
        return $list;
    }

    /**
     * @param int $symbol
     * @return string
     * 符号/记录标题转化字符串
     */
    function symbol2str($symbol = 0) {
        switch($symbol) {
            case 1: return '+'; break;
            case 2: return '-'; break;
            default: return ''; break;
        }
    }
    function trend2str($trend) {
        switch($trend) {
            case 1: return '签到赠'; break;
            case 2: return '成单赠'; break;
            case 3: return '发帖赠'; break;
            case 4: return '评价赠'; break;
            case 5: return '下单抵扣'; break;
            case 6: return '后台调整'; break;
            default: return ''; break;
        }
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return mixed
     * 余额明细
     */
    function balanceTurnover($custom_param, $extra = array()) {
        $param['where']['bal_t.status'] = 1;
        //排序
        $param['order'] = 'bal_t.id DESC';
        //每页数量
        $param['page_size'] = 12;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/BalanceTurnover')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        foreach($list as &$value) {
            $value['symbol_name'] = $this->symbol2str($value['symbol']);
            $value['trend_name']  = $this->bt_trend2str($value['trend']);
            $value['create_time'] = timestamp2date($value['create_time']);
        }
        return $list;
    }
    function bt_trend2str($trend) {
        switch($trend) {
            case 1: return '余额支付订单'; break;
            case 2: return '在线充值'; break;
            case 3: return '充值卡充值'; break;
            case 4: return '邀请收益'; break;
            case 5: return '提现'; break;
            case 9: return '余额退款'; break;
            case 10: return '后台调整'; break;
            default: return ''; break;
        }
    }

    /**
     * @param int $integral_number
     * @return int
     * 根据积分数量获取可抵用金额 四舍五入保留两位小数
     */
    function getDed($integral_number = 0) {
        //四舍五入保留一位小数
        return sprintf("%.2f", $integral_number / C('INTEGRAL_DED_PRO'));
    }

    /**
     * @param int $m_id 用户ID
     * @param string $unique_code 优惠券标识
     * @return bool
     * 赠送优惠券
     */
    function giveCoupon($m_id = 0, $unique_code = '') {
        //判空
        if(empty($m_id) || empty($unique_code))
            return $this->setServiceInfo('参数错误！', false);
        //获取优惠券信息
        $coupon = M('Coupon')->where(array('unique_code'=>$unique_code))->field('goods_cate_id,face_value,use_condition,effective_date,valid_term,status')->find();
        //判断优惠券状态
        if(!$coupon || $coupon['status'] == 0)
            return $this->setServiceInfo('该优惠券已禁用！', false);
        //给用户添加优惠券
        $data = array(
            'unique_code'   => $unique_code,
            'm_id'          => $m_id,
            'goods_cate_id' => $coupon['goods_cate_id'],
            'face_value'    => $coupon['face_value'],
            'use_condition' => $coupon['use_condition'],
            'effective_date'=> $coupon['effective_date'],
            'invalid_date'  => empty($coupon['effective_date']) ? NOW_TIME + $coupon['valid_term'] * 86400 : $coupon['effective_date'] + $coupon['valid_term'] * 86400,
            'create_time'   => NOW_TIME,
        );
        //给用户添加优惠券
        if(!M('MemberCoupon')->data($data)->add())
            return $this->setServiceInfo('系统繁忙，稍后重试！', false);
       return true;
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     * 用户优惠券列表
     */
    function memCoupons($custom_param, $extra = array()) {
        //排序
        $param['order'] = 'm_cpn.status ASC,m_cpn.invalid_date ASC';
        //每页数量
        $param['page_size'] = 12;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);

        //调用数据模型层方法获取数据
        $result = D('FrontC/MemberCoupon')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        array_walk($list, 'FrontC\Service\FinanceService::couponDataFactory', $extra);
        return $list;
    }

    /**
     * @param $value
     * @param $key
     * @param $extra
     * 优惠券信息数据加工
     */
    public static function couponDataFactory(&$value, $key, $extra) {
        //可用分类
        if(empty($value['goods_cate_id'])) {
            $value['can_use'] = '全场通用';
        } else {
            //TODO 商品分类
            $value['can_use'] = M('goods_category')->where(array('id'=>$value['goods_cate_id']))->getField('name');
        }
        //是否过期
        if($value['status'] == 0 && $value['invalid_date'] < NOW_TIME)
            $value['status'] = 2;
        //过期时间
        $value['invalid_date'] = timestamp2date($value['invalid_date'], 'Y-m-d');
        //生效时间
        $value['effective_date'] = timestamp2date($value['effective_date'], 'Y-m-d');
        //使用描述
        $value['desc'] = '在线支付专享';
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     * 用户充值卡列表
     */
    function memRecCards($custom_param, $extra = array()) {
        //状态 未充值
        $param['where']['rec_code.status'] = 0;
        //排序
        $param['order'] = 'rec_code.id DESC';
        //每页数量
        $param['page_size'] = 12;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/RechargeCode')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        foreach($list as &$value) {
            $value['bg_picture'] = api('File/getFiles', array($value['bg_picture'], array('abs_url')))[0]['abs_url'];
        }
        //array_walk($list, 'FrontC\Service\FinanceService::cardDataFactory', $extra);
        return $list;
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     * 提现记录
     */
    function wdLogs($custom_param, $extra = array()) {
        //状态 未充值
        //$param['where']['wd.status'] = 0;
        //排序
        $param['order'] = 'wd.id DESC';
        //每页数量
        $param['page_size'] = 12;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/Withdraw')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        foreach($list as &$value) {
            $value['create_time'] = timestamp2date($value['create_time']);
            $value['title'] = '提现到' . format_card_number($value['card_number'],2);
            unset($value['card_number']);
        }
        //array_walk($list, 'FrontC\Service\FinanceService::cardDataFactory', $extra);
        return $list;
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     * 商城充值卡列表
     */
    function recCards($custom_param, $extra = array()) {
        $param['where']['status'] = 1;
        //排序
        $param['order'] = 'rec_card.sort DESC,rec_card.id DESC';
        //每页数量
        $param['page_size'] = 12;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/RechargeCard')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        array_walk($list, 'FrontC\Service\FinanceService::cardDataFactory', $extra);
        return $list;
    }

    /**
     * @param $value
     * @param $key
     * @param $extra
     * 充值卡信息数据加工
     */
    public static function cardDataFactory(&$value, $key, $extra) {
        $value['bg_picture'] = api('File/getFiles', array($value['bg_picture'], array('abs_url')))[0]['abs_url'];
    }

    /**
     * @param int $m_id
     * @param array $goods_cate_id_array
     * @param int $amounts 汇总金额 判断是否到达可用金额条件
     * @return array
     * 获取可用优惠券
     */
    function getAvailableCoupon($m_id = 0, $goods_cate_id_array = array(), $amounts = 0) {
        //参数判空
        if(empty($m_id))
            return array();
        //用户ID
        $param['where']['m_cpn.m_id']             = $m_id;
        //可用条件满足
        $param['where']['m_cpn.use_condition']    = array('exp', '<=' . $amounts . '');
        //未使用状态
        $param['where']['m_cpn.status'] = 0;
        //使用时间区间满足
        $param['where']['m_cpn.effective_date']   = array('exp', '<=' . NOW_TIME . '');
        $param['where']['m_cpn.invalid_date']     = array('exp', '>=' . NOW_TIME . '');
        //可用分类满足 通用
        if(empty($goods_cate_id_array)) {
            $param['where']['m_cpn.goods_cate_id'] = 0;
        } else {
            $param['where']['(m_cpn.goods_cate_id'] = array('exp', '=0 OR m_cpn.goods_cate_id IN (' . implode(',', $goods_cate_id_array) . '))');
        }
        //每页条数 不分页
        $param['page_size'] = 0;
        //查询优惠券
        $coupons = $this->memCoupons($param);
        if(!$coupons)
            return array();
        return $coupons;
    }

    /**
     * @param int $m_id
     * @param int $payment
     * @param int $trend
     * @param int $amounts
     * @param int $order_id
     * @param string $order_sn
     * @param int $order_type
     * @return bool
     * 添加第三方支付记录
     */
    function addCashTurnover($m_id = 0, $order_id = 0, $payment = 0, $trend = 0, $amounts = 0, $order_sn = '', $order_type = 1) {
        //参数判断
        if(empty($m_id) || empty($order_id) || empty($payment) || empty($trend) || empty($amounts)) {
            return $this->setServiceInfo('参数错误！', false);
        }
        //创建添加数据
        $data = array(
            'm_id'          => $m_id,
            'payment'       => $payment,
            'trend'         => $trend,
            'amounts'       => $amounts,
            'order_id'      => $order_id,
            'order_sn'      => $order_sn,
            'order_type'    => $order_type,
            'create_time'   => NOW_TIME,
            'module'        => TERMINAL,
        );
        //添加
        if(!M('CashTurnover')->data($data)->add())
            return false;
        return true;
    }

    /**
     * @param int $user_id
     * @param int $symbol
     * @param int $amounts
     * @param int $order_id
     * @param int $before_amounts
     * @param string $order_sn
     * @param int $trend
     * @param int $status
     * @param int $order_type
     * @param int $user_type
     * @return bool
     * 添加余额记录
     */
    function addBalanceTurnover($user_id = 0, $user_type = 1, $symbol = 0, $trend = 0, $amounts = 0, $before_amounts = 0, $status = 0, $order_id = 0, $order_sn = '', $order_type = 0, $reason = '') {
        //参数判断
        if(empty($user_id) || empty($symbol) || empty($amounts) || empty($trend) || empty($before_amounts)) {
            return $this->setServiceInfo('参数错误！', false);
        }
        //创建添加数据
        $data = array(
            'user_id'       => $user_id,
            'user_type'     => $user_type,
            'symbol'        => $symbol,
            'trend'         => $trend,
            'amounts'       => $amounts,
            'order_id'      => $order_id,
            'order_sn'      => $order_sn,
            'order_type'    => $order_type,
            'create_time'   => NOW_TIME,
            'status'        => $status,
            'before_amounts' => $before_amounts,
            'reason'        => $reason,
        );
        if($data['symbol'] == 1)  //1加2减
            $data['after_amounts'] = $before_amounts + $amounts; //改变后金额
        else
            $data['after_amounts'] = $before_amounts - $amounts; //改变后金额
        //添加
        if(!M('BalanceTurnover')->data($data)->add())
            return false;
        return true;
    }

    /**
     * @param int $amounts
     * @return int
     * 计算邀请获利
     */
    function getInviteProfit($amounts = 0) {
        if(empty($amounts))
            return 0;
        if(false === strpos(C('PROFIT_FEE'), '%')) {  //不存在%
            return sprintf("%.2f", C('PROFIT_FEE')); //固定金额
        } else {
            return sprintf("%.2f", $amounts * (floatval(C('PROFIT_FEE')) / 100)); //比例金额
        }
    }
    /**
     * @param int $user_id
     * @param int $user_type
     * @param int $symbol  1--加  2--减
     * @param int $platform
     * @param int $trend
     * @param int $amounts
     * @param int $status
     * @param int $order_id
     * @param string $order_sn
     * @param int $refund_order_record
     * @return bool
     * 添加第三方交易记录
     * 新增参数 $refund_order_record 申请退款记录的ID---dengjie
     */
    public function addTradeRecords($user_id = 0, $user_type = 1, $symbol = 0, $platform = 0, $trend = 0, $amounts = 0, $status = 0, $order_id = 0, $order_sn = '', $reason = '',$refund_order_record = 0) {
        //参数判断
        if(empty($user_id) || empty($platform) || empty($trend) || empty($amounts) || empty($order_id)) {
            return $this->setServiceInfo('参数错误！', false);
        }
        //创建添加数据
        $data = [
            'home_trade_no' => $this->getHomeTradeNo(1),
            'user_id'       => $user_id,
            'user_type'     => $user_type,
            'symbol'        => $symbol,
            'platform'      => $platform,
            'trend'         => $trend,
            'amounts'       => $amounts,
            'status'        => $status,
            'order_id'      => $order_id,
            'order_sn'      => $order_sn,
            'create_time'   => NOW_TIME,
            'terminal'      => TERMINAL,
            'refund_order_record' =>$refund_order_record,
        ];
        //添加
        if(!M('FinanceTradeRecords')->data($data)->add()) {
            return false;
        }
        return true;
    }
    /**
     * @param int $type
     * @return string
     * 本站交易号
     */
    public function getHomeTradeNo($type = 0) {
        switch($type) {
            case 1: return 'T' . date('YmdHis') . get_vc(4, 2); break;
            case 2: return 'B' . date('YmdHis') . get_vc(4, 2); break;
            case 3: return 'I' . date('YmdHis') . get_vc(4, 2); break;
            case 4: return 'S' . date('YmdHis') . get_vc(4, 2); break;
            default : return ''; break;
        }
    }

    /**
     * @param int $user_id
     * @param int $symbol  1--加  2--减
     * @param int $amounts
     * @param int $order_id
     * @param int $before_amounts
     * @param string $order_sn
     * @param int $trend
     * @param int $status
     * @param int $user_type
     * @return bool
     * 添加余额收支记录
     */
    public function addBalanceRecords($user_id = 0, $user_type = 1, $symbol = 0, $trend = 0, $amounts = 0, $before_amounts = 0, $status = 0, $order_id = 0, $order_sn = '', $brokerage = 0, $reason = '', $order_refund_id = 0) {
        //参数判断
        if(empty($user_id) || empty($symbol) || empty($amounts) || empty($trend)) {
            return $this->setServiceInfo('参数错误！', false);
        }
        //创建添加数据
        $data = [
            'home_trade_no'     => $this->getHomeTradeNo(2),
            'user_id'           => $user_id,
            'user_type'         => $user_type,
            'symbol'            => $symbol,
            'trend'             => $trend,
            'amounts'           => $amounts,
            'brokerage'         => $brokerage,
            'order_id'          => $order_id,
            'order_sn'          => $order_sn,
            'create_time'       => NOW_TIME,
            'status'            => $status,
            'before_amounts'    => $before_amounts,
            'reason'            => $reason,
            'order_refund_id'   => $order_refund_id,
        ];
        if($data['symbol'] == 1)  //1--加  2--减
            $data['after_amounts'] = $before_amounts + $amounts; //改变后金额
        else
            $data['after_amounts'] = $before_amounts - $amounts; //改变后金额
        //添加
        if(!M('FinanceBalanceRecords')->data($data)->add()) {
            return false;
        }
        return true;
    }
}