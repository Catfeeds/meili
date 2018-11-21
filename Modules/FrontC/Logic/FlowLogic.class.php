<?php

namespace FrontC\Logic;

/**
 * Class FlowLogic
 * @package FrontC\Logic
 * 下单流程逻辑层
 */
class FlowLogic extends FrontBaseLogic
{

    /**
     * [商城下单调用、]
     * @param array $request
     * @return array
     * 确认订单
     */
    function confirmOrder($request = array())
    {
        //判断是直接购买还是购物车购买
        //整理订单信息
        if(empty($request['cart_ids'])){
            if(empty($request['goods_id'])){
                return $this->setLogicInfo('请选择收货地址！', false);
            }
            $result = $this->_arrangeOrderInfoOne($request);
        }else{
            $result = $this->_arrangeOrderInfo($request);
        }
        if (empty($result))
            return $this->setLogicInfo($this->getLogicInfo(), false);
        //获取默认地址
        if (empty($request['adr_id']))
            $address = D('FrontC/Address', 'Logic')->getList(array_merge($request, array('default' => 1)));
        else
            $address = D('FrontC/Address', 'Logic')->getList(array_merge($request, array('adr_id' => $request['adr_id'])));
        $result['address'] = empty($address[0]) ? (object)array() : $address[0];
        //$result['address'] = $address[0];
        return $result;
    }

    /**
     * 服务商品确认订单
     * POST参数：
     * "package_id": "套餐ID",
     * "service_id": "服务商品ID",
     * "card_id": "一卡通ID",
     */
    function confirmOrderSer($request = array())
    {
        //整理订单信息 判断用户是购买何种商品 一卡通没有确认订单 直接购买
        if (!empty($request['package_id'])) {
            //套餐
            $param = array('package.id' => $request['package_id'], 'package.status' => 1);
            $result = D('FrontC/Package', 'Model')->getRowSe($param);
        } else {
            //单项服务商品
            $param = array('service.id' => $request['service_id'], 'service.status' => 1);
            $result = D('FrontC/Service', 'Model')->getRowSe($param);
        }
        $result['goods_amounts'] = $result['price'];
        $result['order_amounts'] = $result['price'];
        $result['pay_amounts'] = $result['price'];
        if (empty($result))
            return $this->setLogicInfo($this->getLogicInfo(), false);
        return $result;
    }

    /**
     * @param array $request
     * @return bool
     * 提交订单
     */
    function submitOrder($request = array())
    {
        //未选择地址
        if (empty($request['adr_id']))
            return $this->setLogicInfo('请选择收货地址！', false);
        //获取用户地址
        $address = D('FrontC/Address', 'Logic')->getRow($request);
        if (empty($address))
            return $this->setLogicInfo('收货地址不存在！', false);
        //未选择实体店铺
        if (empty($request['shop_id']))
            return $this->setLogicInfo('请选择实体店铺！', false);
        //整理订单信息
        //判断是直接购买还是从购物车结算
        if(!empty($request['goods_id'])){
            $result = $this->_arrangeOrderInfoOne($request);
        }else{
            $result = $this->_arrangeOrderInfo($request);
        }
        if (empty($result))
            return $this->setLogicInfo($this->getLogicInfo(), false);

        //创建订单数据
        $data = array(
            'order_sn' => D('FrontC/Flow', 'Service')->createSn(),
            'm_id' => $request['m_id'],
            'shop_id' => $request['shop_id'],
            'consignee' => $address['contacts'],
            'province_name' => $address['province_name'],
            'city_name' => $address['city_name'],
            'area_name' => $address['area_name'],
            'address' => $address['address'],
            'mobile' => $address['mobile'],
            //'integral_ded_amounts'  => empty($result['integral_ded_amounts']) ? 0.00 : $result['integral_ded_amounts'],
            'coupon_amounts' => empty($result['coupon_amounts']) ? 0.00 : $result['coupon_amounts'],
            'goods_amounts' => $result['goods_amounts'],
            'order_amounts' => $result['order_amounts'],
            'pay_amounts' => $result['pay_amounts'],
            'remark' => empty($request['remark']) ? '' : $request['remark'],
            'create_time' => NOW_TIME,
        );
        //创建订单
        $order_id = M('OrderInfo')->data($data)->add();
        if (!$order_id)
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        //提交订单成功后执行
        if(!empty($request['goods_id'])){
            $this->_afterSubmitSuccessDoOne($order_id, $request, $result);
        }else{
            $this->_afterSubmitSuccessDo($order_id, $request, $result);
        }

        return array('order_id' => $order_id);
    }

    /**
     * pup
     * [服务商品提交订单调用、]
     * @param array $request
     * @return bool
     * 提交订单
     */
    function submitOrderSer($request = array())
    {
        //整理订单信息
//        $result = $this->_arrangeOrderInfo($request);
        if (empty($request['m_id'])) {
            $this->setLogicInfo('请先登陆哦！');
            return false;
        }
        //判断参数 是否选择实体店
        if (empty($request['shop_id'])) {
            $this->setLogicInfo('请选择实体店哦！');
            return false;
        }
        $shop_param = array('id' => $request['shop_id'], 'status' => 1);
        $shop_info = D('FrontC/Shop', 'Model')->getRow($shop_param);
        //整理订单信息 判断用户是购买何种商品 一卡通没有确认订单 直接购买
        if (!empty($request['card_id'])) {
            //一卡通
            //判断参数
            if (empty($request['type'])) {
                $this->setLogicInfo('参数错误哦！');
                return false;
            }
            $param = array('id' => $request['card_id'], 'status' => 1);
            $result = D('FrontC/Card', 'Model')->getCard($param);
            if ($request['type'] == 1) {
                //季卡
                $goods_name = $result['card_name'] . '(' . $result['m_count'] . '/' . '季卡' . ')';
                $result['goods_name'] = $goods_name;
                $result['goods_amounts'] = $result['m_price'];
                $result['order_amounts'] = $result['m_price'];
                $result['pay_amounts'] = $result['m_price'];
                //订单一卡通标识 flag 0服务商品 1套餐 2一卡通
                $flag = 2;
                $flag_id = $request['card_id'];
            } else {
                //年卡
                $goods_name = $result['card_name'] . '(' . $result['y_count'] . '/' . '年卡' . ')';
                $result['goods_name'] = $goods_name;
                $result['goods_amounts'] = $result['y_price'];
                $result['order_amounts'] = $result['y_price'];
                $result['pay_amounts'] = $result['y_price'];
                $flag = 2;
                $flag_id = $request['card_id'];
            }
            //订单一卡通标识 flag 0服务商品 1套餐 2一卡通
        } elseif (!empty($request['package_id'])) {
            //套餐
            $param = array('package.id' => $request['package_id'], 'package.status' => 1);
            $result = D('FrontC/Package', 'Model')->getRow($param);
            $result['goods_amounts'] = $result['price'];
            $result['order_amounts'] = $result['price'];
            $result['pay_amounts'] = $result['price'];
            $result['name'] = $result['package_name'];
            //订单一卡通标识 flag 0服务商品 1套餐 2一卡通
            $flag = 1;
            $flag_id = $request['package_id'];
        } else {
            //单项服务商品
            $param = array('service.id' => $request['service_id'], 'service.status' => 1);
            $result = D('FrontC/Service', 'Model')->getRow($param);
            $result['goods_amounts'] = $result['price'];
            $result['order_amounts'] = $result['price'];
            $result['pay_amounts'] = $result['price'];
            $result['name'] = $result['service_name'];
            //订单一卡通标识 flag 0服务商品 1套餐 2一卡通
            $flag = 0;
            $flag_id = $request['service_id'];
        }
        //获取用户余额
        $member_param = array('id' => $request['m_id'], 'status' => 1);
        $balance = D('FrontC/Member', 'Model')->getBalance($member_param);
        if (empty($result))
            return $this->setLogicInfo($this->getLogicInfo(), false);
        //创建订单数据
        $order_sn = D('FrontC/Flow', 'Service')->createSn(3);
        $data = array(
            'order_sn' => $order_sn,
            'm_id' => $request['m_id'],
            'shop_id' => empty($request['shop_id']) ? 0 : $request['shop_id'],
            'coupon_amounts' => empty($result['coupon_amounts']) ? 0.00 : $result['coupon_amounts'],
            'goods_amounts' => $result['goods_amounts'],
            'order_amounts' => $result['order_amounts'],
            'pay_amounts' => $result['pay_amounts'],
            'remark' => empty($request['remark']) ? '' : $request['remark'],
            'flag' => empty($flag) ? 0 : $flag,
            'flag_id' => empty($flag_id) ? 0 : $flag_id,
            'create_time' => NOW_TIME,
        );
        //创建订单
        $order_id = M('OrderInfoSer')->data($data)->add();
        if (!$order_id)
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        if (!empty($request['card_id'])) {
            //提交订单成功后执行 一卡通入一卡通表
            $this->_afterSubmitSuccessDoSerCard($order_id, $request, $result);
            return array(
                'order_id' => $order_id,
                'order_sn' => $order_sn,
                'pay_amounts' => $result['pay_amounts'],
                'goods_name' => $result['goods_name'],
                'cover' => $result['cover_url'],
                'shop_name' => $shop_info['name'],
                'balance' => $balance,
            );
        } else {
            //提交订单成功后执行 服务商品入商品表
            $this->_afterSubmitSuccessDoSerGoods($order_id, $request, $result);
            return array(
                'order_id' => $order_id,
                'order_sn' => $order_sn,
                'pay_amounts' => $result['pay_amounts'],
                'goods_name' => $result['name'],
                'cover' => $result['cover'],
                'balance' => $balance,
                'shop_name' => $shop_info['name'],
            );
        }
    }

    /**
     * [服务商品提交订单调用、]
     * @param $order_id
     * @param $request
     * @param $result
     * 一卡通提交订单成功后执行
     */
    private function _afterSubmitSuccessDoSerGoods($order_id, $request, $result)
    {
        //判断商品是套餐还是单个服务商品
        if (!empty($result['package_id'])) {
            //套餐 -- 获取套餐内容 有的套餐是一个商品但有多个数量
            //获取该套餐下的服务商品
            $param = array('packageser.package_id' => $request['package_id']);
            $services = D('FrontC/Package', 'Model')->getPackageServices($param);
            //添加订单商品信息
            foreach ($services as $key => $service) {
                $goods_data[$key]['order_id'] = $order_id;
                $goods_data[$key]['service_id'] = $service['service_id'];
                $goods_data[$key]['service_name'] = $service['service_name'];
                $goods_data[$key]['price'] = $service['price'];
                $goods_data[$key]['number'] = $service['number'];
                $goods_data[$key]['cover'] = $service['cover'];
                $goods_data[$key]['create_time'] = NOW_TIME;
                $goods_data[$key]['m_id'] = $request['m_id'];
            }
            M('OrderService')->addAll($goods_data);
        } else {
            //单个服务商品
            $service_data['order_id'] = $order_id;
            $service_data['m_id'] = $request['m_id'];
            $service_data['service_id'] = $result['service_id'];
            $service_data['service_name'] = $result['service_name'];
            $service_data['price'] = $result['price'];
//            $service_data['service_short_desc']=$result['short_desc'];
            $service_data['cover'] = $result['cover'];
            $service_data['number'] = 1;
            $service_data['create_time'] = NOW_TIME;
            M('OrderService')->data($service_data)->add();
        }
    }

    /**
     * [服务商品提交订单调用、]
     * @param $order_id
     * @param $request
     * @param $result
     * 一卡通提交订单成功后执行
     */
    private function _afterSubmitSuccessDoSerCard($order_id, $request, $result)
    {
        //添加订单商品信息
        if ($request['type'] == 1) {
            //季卡
            $type = 1;
            $price = $result['m_price'];
            $count = $result['m_count'];
        } else {
            //年卡
            $type = 2;
            $price = $result['y_price'];
            $count = $result['y_count'];
        }
        $card_data['order_id'] = $order_id;
        $card_data['card_id'] = $result['card_id'];
        $card_data['shop_id'] = $request['shop_id'];
        $card_data['card_name'] = $result['card_name'];
        $card_data['cover'] = $result['cover'];
        $card_data['type'] = $type;
        $card_data['price'] = $price;
        $card_data['m_price'] = $result['m_price'];
        $card_data['y_price'] = $result['y_price'];
        $card_data['count'] = $count;
        $card_data['m_count'] = $result['m_count'];
        $card_data['y_count'] = $result['y_count'];
        $card_data['font_color'] = $result['font_color'];
        $card_data['start_time'] = $result['start_time'];
        $card_data['end_time'] = $result['end_time'];
        $card_data['update_time'] = NOW_TIME;
        $card_data['create_time'] = NOW_TIME;
        $card_data['card_desc'] = '';
        M('OrderCard')->data($card_data)->add();

    }

    /**
     * [商城提交订单调用]
     * @param $order_id
     * @param $request
     * @param $result
     * 提交订单成功后执行
     */
    private function _afterSubmitSuccessDo($order_id, $request, $result)
    {
        //添加订单商品信息
        foreach ($result['goods_list'] as $key => $goods) {
            $goods_data[$key]['order_id'] = $order_id;
            $goods_data[$key]['goods_id'] = $goods['goods_id'];
            $goods_data[$key]['goods_name'] = $goods['goods_name'];
            $goods_data[$key]['goods_sn'] = $goods['goods_sn'];
            $goods_data[$key]['cover'] = $goods['cover'];
            $goods_data[$key]['product_id'] = $goods['product_id'];
            $goods_data[$key]['number'] = $goods['number'];
            $goods_data[$key]['price'] = $goods['price'];
            $goods_data[$key]['unit'] = $goods['unit'];
            $goods_data[$key]['goods_attr_desc'] = $goods['goods_attr_desc'];
            $goods_data[$key]['goods_attr_ids'] = $goods['goods_attr_ids'];
            //更新商品库存
            D('FrontC/Goods', 'Service')->updStock($goods['goods_id'], $goods['product_id'], $goods['number']);
        }
        M('OrderGoods')->addAll($goods_data);
        //清空购物车
        D('FrontC/Cart', 'Service')->clearCart($request['m_id'], $request['cart_ids']);
        //是否使用积分抵扣 添加积分记录
//        if($request['uer_itg'] == 1) {
//            D('FrontC/Finance', 'Service')->addItgLog($request['m_id'], 2, 5, $result['integral_info']['available_integral'], '', $order_id);
//        }
        //用户优惠券ID  如果大于0 修改优惠券信息
        if ($request['m_cpn_id'] > 0) {
            M('MemberCoupon')->where(array('id' => $request['m_cpn_id']))->data(array('use_time' => NOW_TIME, 'status' => 1, 'order_id' => $order_id))->save();
        }
    }
    /**
     * [商城提交订单调用]
     * @param $order_id
     * @param $request
     * @param $result
     * 直接购买
     * 提交订单成功后执行
     */
    private function _afterSubmitSuccessDoOne($order_id, $request, $result)
    {
        //添加订单商品信息
        foreach ($result['goods_list'] as $key => $goods) {
            $cover_path=M('File')->where(array('id'=>$goods['cover']))->getField('abs_url');
            $goods_data[$key]['order_id'] = $order_id;
            $goods_data[$key]['goods_id'] = $goods['goods_id'];
            $goods_data[$key]['goods_name'] = $goods['goods_name'];
            $goods_data[$key]['goods_sn'] = $goods['goods_sn'];
            $goods_data[$key]['cover'] = $cover_path;
            $goods_data[$key]['number'] = 1;
            $goods_data[$key]['price'] = $goods['price'];
            $goods_data[$key]['unit'] = $goods['unit'];
            //更新商品库存
            D('FrontC/Goods', 'Service')->updStock($goods['goods_id'], 0, 1);
        }
        M('OrderGoods')->addAll($goods_data);
    }
    /**
     * [下单整理订单调用、提交订单调用]
     * @param array $request
     * @return mixed
     * 整理订单信息
     * 商品列表、金额信息
     */
    private function _arrangeOrderInfo($request = array())
    {
        //获取商品清单 商品中 可用积分商品  商品分类 判断是否可用优惠券
        $cart_goods_list = D('FrontC/Cart', 'Logic')->cartList($request);
        //判断购物车中是否有商品
        if (empty($cart_goods_list))
            return $this->setLogicInfo('购物车中暂无商品！', false);
        //商品价格总额
        $goods_amounts = 0;
        //可用积分抵扣的总金额
//        $can_use_itg_price_amounts  = 0;
        //循环商品列表
        foreach ($cart_goods_list as $goods) {
            //如果该商品可用积分抵扣 则记录金额
//            if($goods['is_integral'] == 1)
//                //计算可用积分抵扣的总额
//                $can_use_itg_price_amounts += $goods['price'] * $goods['number'];
            //获取商品分类数组
            $goods_cate_id_array[] = $goods['goods_cate_id'];
            //计算商品总额
            $goods_amounts += $goods['price'] * $goods['number'];
        }
        //获取可用的积分数量和抵扣金额
//        $integral_info = D('FrontC/Flow', 'Service')->getItgDed($request['m_id'], $can_use_itg_price_amounts);
        //获取可用的优惠券列表  是否有可用优惠券 查询用户优惠券 可用分类为0/在商品分类数组中 时间 状态筛选
        $coupons = D('FrontC/Finance', 'Service')->getAvailableCoupon($request['m_id'], $goods_cate_id_array, $goods_amounts);
        //是否使用积分抵扣 使用了积分
//        if($request['uer_itg'] == 1) {
//            //计算抵扣金额
//            $result['integral_ded_amounts'] = $integral_info['integral_ded_amounts'];
//        }
        //用户优惠券ID  如果大于0 则用户选择了优惠券
        $result['coupon_amounts'] = 0.00;
        if ($request['m_cpn_id'] > 0) {
            //获取优惠券信息
            $coupon = M('MemberCoupon')->where(array('id' => $request['m_cpn_id']))->field('face_value,status')->find();
            //判断当前是否还是可用状态
            if (!D('FrontC/Flow', 'Service')->checkCoupon(0, $coupon))
                return $this->setLogicInfo(D('FrontC/Flow', 'Service')->getServiceInfo(), false);
            //优惠券面值
            $result['coupon_amounts'] = $coupon['face_value'];
        }
        //结果
//        $result['integral_info']    = $integral_info; //积分抵扣信息
        $result['coupons'] = $coupons; //优惠券列表
        $result['goods_list'] = $cart_goods_list; //商品清单
        $result['goods_amounts'] = $goods_amounts; //商品总额
//        $result['order_amounts']    = $goods_amounts - $result['integral_ded_amounts'] - $result['coupon_amounts']; //订单总额
//        $result['pay_amounts']      = $goods_amounts - $result['integral_ded_amounts'] - $result['coupon_amounts']; //支付总额
        $result['order_amounts'] = $goods_amounts - $result['coupon_amounts']; //订单总额
        $result['pay_amounts'] = $goods_amounts - $result['coupon_amounts']; //支付总额

        return $result;
    }
    /**
     * [下单整理订单调用、提交订单调用]
     * @param array $request
     * @return mixed
     * 整理订单信息
     * 商品列表、金额信息
     */
    private function _arrangeOrderInfoOne($request = array())
    {
         $param=array('goods.id'=>$request['goods_id']);
         $goods_info=D('FrontC/Goods')->findRow($param);
         //直接购买商品 数量为1  获取图片
         $goods_info['number'] = '1';
         $arr_cover = api('File/getFiles',array($goods_info['cover']));
         $goods_info['cover'] = $arr_cover[0]['abs_url'];
         unset($goods_info['goods_desc']);
        //结果
        $result['coupons'] = array(); //优惠券列表
        $result['coupon_amounts'] = '0'; //优惠券列表
        $result['goods_list'][] = $goods_info; //商品清单
        $result['goods_amounts'] = $goods_info['price']; //商品总额
        $result['order_amounts'] = $goods_info['price']; //订单总额
        $result['pay_amounts'] = $goods_info['price']; //支付总额
        if(empty($result)){
            return array();
        }else{
            return $result;
        }
    }
}