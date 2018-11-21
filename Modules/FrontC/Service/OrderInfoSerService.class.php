<?php
namespace FrontC\Service;

/**
 * Class OrderInfoService
 * @package FrontC\Service
 * 用户订单相关管理服务层
 * 黑暗中的武者
 */
class OrderInfoSerService extends FrontBaseService {

    /**
     * @param array $custom_param
     * @return array
     * 我的订单列表
     * 黑暗中的武者
     * flag 0服务商品 1套餐 2一卡通
     */
    function orderList($custom_param = array()) {
        //每页数量
        $param['page_size'] = 6;
        //排序
        $param['order']     = 'oi.id DESC';
        //基础字段
        $param['field']     = 'oi.id order_id,oi.order_sn,oi.pay_amounts,oi.status,oi.is_comm,oi.shop_id';
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        //隐藏用户假删除订单
        $param['where']['oi.is_hidden']=0;
        //g过滤一卡通订单
        $param['where']['oi.flag']=array('exp', '<2');
        $result = D('FrontC/OrderInfoSer')->getList($param);
        //处理数据
        foreach($result['list'] as &$value) {
            //状态
            $value['status_name'] = $this->getStatusName($value['status']);
            //获取商品信息
            $value['goods_list']  = $this->getOrderGoodsTe($value['order_id']);
        }
        return $result;
    }

    /**
     * @param $order_id
     * @return mixed
     * 获取订单商品信息
     * flag 0服务商品 1套餐 2一卡通
     * 一卡通订单不需要显示
     */
    function getOrderGoodsTe($order_id, $group = 0) {
        //判断商品类型 flag 0单个服务商品 1套餐
        $order_info=M('OrderInfoSer')->where(array('id'=>$order_id))->field('flag,flag_id')->find();
        if($order_info['flag'] == 0){
             //单个服务商品
            if(empty($group)){
                $service=M('OrderService')->where(array('order_id'=>$order_id))->field('service_id id,service_name name,price,number,cover')->select();
                foreach ($service as $k=>$v){
                    $v['short_desc']=M('Service')->where(array('id'=>$v['id']))->getField('service_short_desc');
                    $result[]=$v;
                }
                return $result;
            }else{
                $service=M('OrderService')->where(array('order_id'=>$order_id))->group('goods_id')->field('service_id id,service_name name,price,number,cover')->select();
                foreach ($service as $k=>$v){
                    $v['short_desc']=M('Service')->where(array('id'=>$v['id']))->getField('service_short_desc');
                    $result[]=$v;
                }
                return $result;
            }
        }
        if ($order_info['flag'] == 1){
             //套餐
            $package_info=M('Package')->alias('package')
                ->where(array('package.id'=>$order_info['flag_id']))
                ->field('package.id,package.package_name name,package.package_short_desc short_desc,package.price,package.number,file.abs_url cover')
                ->join(array(
                    'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = package.cover',
                ))
                ->select();
            return $package_info;
        }
    }

    /**
     * @param $order_id
     * @return mixed
     * 获取订单商品信息
     * flag 0服务商品 1套餐 2一卡通
     * 一卡通订单不需要显示
     */
    function getOrderGoods($order_id, $group = 0) {
        if(empty($group)){
            $services = M('OrderService')->where(array('order_id'=>$order_id))->field('id order_service_id,service_id,service_name,service_sn,price,number,cover')->select();
           foreach ($services as $k=>$v){
               $short_desc=M('Service')->where(array('id'=>$v['service_id']))->getField('service_short_desc short_desc');
               $v['short_desc']=$short_desc;
               $result[]=$v;
           }
          return $result;
        }else{
            $services = M('OrderService')->where(array('order_id'=>$order_id))->group('goods_id')->field('id order_service_id,service_id,service_name,service_sn,price,number,cover')->select();
            foreach ($services as $k=>$v){
                $short_desc=M('Service')->where(array('id'=>$v['service_id']))->getField('service_short_desc short_desc');
                $v['short_desc']=$short_desc;
                $result[]=$v;
            }
            return $result;
        }
    }

    /**
     * @param array $custom_param
     * @return array
     * 订单详情
     * 黑暗中的武者
     */
    function orderDetail($custom_param = array()) {
        //排序
        $param['where']     = array();
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $order = D('FrontC/OrderInfoSer')->findRow($param);
        if(!$order)
            return array();
        //时间处理
        $order['create_time']      = timestamp2date($order['create_time']);
        //状态名称
        $order['status_name']      = $this->getStatusName($order['status']);
        //获取支付方式
        $order['payment_name']     = get_payment_name($order['payment']);
        //获取订单明细
        $order['goods_list']       = $this->getOrderGoods($order['order_id']);
        //获取详细地址拼接
//        $order['address']          = D('FrontC/Address', 'Service')->getAddress($order);
        //物流详情
        //$order['logistics']        = $this->getLogistics($order['logistics_number']);
        return $order;
    }

    /**
     * @param $order_id
     * @param array $actionable 可执行当前操作的状态数组
     * @param array $order
     * @return bool
     * 验证订单实时状态 是否可进行当前操作
     */
    function checkStatus($order_id, $actionable = array(), $order = array()) {
        if(!empty($order)) {
            //判断当前状态是否在可执行当前操作的状态数组中 不存在则不能进行操作
            if(!in_array($order['status'], $actionable))
                return false;
            else
                return true;
        }
        //判断当前状态是否在可执行当前操作的状态数组中 不存在则不能进行操作
        if(!in_array(M('OrderInfoSer')->where(array('id'=>$order_id))->getField('status'), $actionable))
            return false;
        else
            return true;
    }

    /**
     * [我的订单调用]
     * @param $status
     * @return string
     * 获取状态名称
     * 黑暗中的武者
     */
    function getStatusName($status) {
        $function = 'status_' . $status;
        return $this->$function($status);
    }

    /**
     * @param $logistics_number
     * @return array
     * 获取物流信息
     */
    function getLogistics($logistics_number) {
        $result = $this->logistics($logistics_number);
        //状态值不为 200则获取失败
        if($result['status'] != 200)
            return array();
        return $result['data'];
    }

    /**
     * @param $current_status 当前状态
     * @param $order
     * @return array
     * 获取物流
     * 循环小于等于当前状态的所有状态 获取每个状态节点相对应的信息
     * 黑暗中的武者
     */
    /*function getLogistics($current_status, $order) {
        //循环次数
        $for = TERMINAL != 'home' ? $current_status : 5;
        //循环小于等于当前状态的所有状态
        for($status = 0; $status <= $for; $status++) {
            //要执行的方法
            $function    = 'status_' . $status;
            //获取循环状态对应的信息
            $logistics[] = $this->$function($current_status, $order);
            //执行完循环状态5之后 就结束
            if($status == 5)
                break;
        }
        return $logistics;
    }*/

    /**
     * @param $logistics_number
     * @return bool
     * 获取对应快递公司名称和对应传值的方法
     */
    private function _expressName($logistics_number) {
        $name   = json_decode(file_get_contents("http://www.kuaidi100.com/autonumber/auto?num={$logistics_number}"), true);
        $result = $name[0]['comCode'];
        if (empty($result)) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * @param $logistics_number  快递的单号
     * @return bool|mixed
     * 返回$data array      快递数组查询失败返回false
     * $data['ischeck'] ==1 已经签收
     * $data['data']        快递实时查询的状态 array
     */
    function logistics($logistics_number) {
        $keywords = $this->_expressName($logistics_number);
        if (!$keywords) {
            return false;
        } else {
            return json_decode(file_get_contents("http://www.kuaidi100.com/query?type={$keywords}&postid={$logistics_number}"), true);
        }
    }

    /**
     * @param $status
     * @param array $order
     * @return array|string
     * 以下是获取订单状态名称  和  状态节点对应显示数组信息
     * 获取物流信息
     * 黑暗中的武者
     */
    function status_0($status, $order = array()) {
        if(empty($order))
            return '待分配';
        if($status <= 0)
            return array('active' => 1, 'title' => '待分配', 'time' => '', 'info' => '');
        if($status > 0)
            return array('active' => 0, 'title' => '已分配', 'time' => timestamp2date($order['cis_dispatch_time']), 'info' => '取送员：'.$order['really_name'].'-'.$order['cis_account'].'');
    }
    function status_1($status, $order = array()) {
        if(empty($order))
            return '待支付';
        if($status <= 1)
            return array('active' => 1, 'title' => '待支付', 'time' => '', 'info' => '');
        if($status > 1)
            return array('active' => 0, 'title' => '已支付', 'time' => timestamp2date($order['metered_time']), 'info' => '');
    }
    function status_2($status, $order = array()) {
        if(empty($order))
            return '待预约';
        if($status <= 2)
            return array('active' => 1, 'title' => '待预约', 'time' => '', 'info' => '');
        if($status > 2)
            return array('active' => 0, 'title' => '已预约', 'time' => timestamp2date($order['pay_time']), 'info' => '');
    }
    function status_3($status, $order = array()) {
        if(empty($order))
            return '待服务';
        if($status <= 3)
            return array('active' => 1, 'title' => '待服务', 'time' => '', 'info' => '');
        if($status > 3)
            return array('active' => 0, 'title' => '已服务', 'time' => timestamp2date($order['arrived_time']), 'info' => '');
    }
    function status_4($status, $order = array()) {
        if(empty($order))
            return '已完成';
        if($status <= 4)
            return array('active' => 1, 'title' => '已完成', 'time' => '', 'info' => '');
        if($status > 4)
            return array('active' => 0, 'title' => '已完成', 'time' => timestamp2date($order['back_time']), 'info' => '');
    }
    function status_5($status, $order = array()) {
        if(empty($order))
            return '预留';
    }
    function status_6($status, $order = array()) {
        if(empty($order))
            return '预留';
    }
    function status_7($status, $order = array()) {
        if(empty($order))
            return '申请退款中';
    }
    function status_8($status, $order = array()) {
        if(empty($order))
            return '退款完成';
    }
    function status_9($status, $order = array()) {
        if(empty($order))
            return '取消退款';
    }
    function status_10($status, $order = array()) {
        if(empty($order))
            return '订单关闭';
    }

    /**
     * @param int $order_id
     * @param array $order
     * 确认签收后操作
     */
    function afterSignFor($order_id = 0, $order = array()) {
        if(empty($order_id) && empty($order))
            return false;
        if(empty($order))
            $order = M('OrderInfo')->where(array('id'=>$order_id))->field('id order_id,order_sn,m_id,pay_amounts')->find();
        //TODO 判断下单人是否有邀请者 如果有则给邀请者佣金
        //获取应得收益 判断应得收益是否为 0.00 如果不为0.00往下进行
        $profit = D('FrontC/Finance', 'Service')->getInviteProfit($order['pay_amounts']);
        if($profit != 0.00) {
            //是否有邀请人
            $user_id = M('InviteLog')->where(array('other_id' => $order['m_id']))->getField('user_id');
            if($user_id) {
                //获取用户余额
                $user_info = M('Member')->where(array('id' => $user_id))->field('balance')->find();
                //给邀请人加佣金 记录  余额 ...
                $data = array(
                    'balance' => array('exp', '`balance`+' . $profit . ''),
                    'profit' => array('exp', '`profit`+' . $profit . ''),
                );
                //修改邀请者余额和获利总额
                M('Member')->where(array('id' => $user_id))->data($data)->save();
                //添加余额记录
                D('FrontC/Finance', 'Service')->addBalanceTurnover($user_id,1,1,4,$profit,$user_info['balance'],1,$order['order_id'],$order['order_sn'],1);
            }
        }
        //TODO 商品增加销量

        //TODO 完成订单给会员赠积分
        //积分记录
        //D('FrontC/Finance', 'Service')->addItgLog($order_info['m_id'], 1, 2, C('FINISH_ORDER_INTEGRAL'));
    }

    /**
     * @param int $order_id
     * @return bool
     * 更新订单商品清单的库存  订单失效后给商品加库存
     */
    function updOrderGoodsStock($order_id = 0) {
        if(empty($order_id))
            return false;
        $goods_list = M('OrderGoods')->where(array('order_id'=>$order_id))->field('goods_id,product_id,number')->select();
        foreach($goods_list as $key => $goods) {
            //更新商品库存
            D('FrontC/Goods', 'Service')->updStock($goods['goods_id'], $goods['product_id'], $goods['number'], 1);
        }
    }

    /**
     * @param int $flag
     * @return string
     * 生成订单号
     * 黑暗中的武者
     */
    function createSn($flag = 0) {
        switch($flag) {
            case 0 : return 'Y' . time() . get_vc(7, 2); break;
            case 1 : return 'R' . time() . get_vc(7, 2); break;
        }
    }

    /**
     * @param $order_info
     * @param $order_id
     * @return bool|string
     * 对接业务订单
     */
    public function dockOrder($order_info, $order_id = 0) {
        //获取订单产品信息
        $order_products = M('OrderGoods')->where(array('order_id'=>$order_info['order_id']))->field('goods_sn,goods_name,number,price,unit')->select();
        foreach($order_products as $product) {
            $products[] = [
                'ProductNo'     => $product['goods_sn'],
                'ProductName'   => $product['goods_name'],
                'Quantity'      => $product['number'],
                'Unit'          => $product['unit'],
                'Price'         => $product['price']
            ];
        }
        //支付方式
        $payMode = function($order_info) {
            switch($order_info['payment']) {
                case 1 : return 'AP'; break;
                case 2 : return 'WP'; break;
                case 3 : return 'OP'; break;
                default : return ''; break;
            }
        };
        $orderE = \Dock\Order\OrderEntity::instance();
        $orderE->setOrderNo($order_info['order_sn']);
        $orderE->setPayMode($payMode($order_info));
        $orderE->setTotalPrice($order_info['order_amounts']);
        $orderE->setRealPay($order_info['pay_amounts']);
        $orderE->setPayTime(date('Y-m-d H:i:s'));
        $orderE->setReceiveType('E');
        $orderE->setCustomerName($order_info['consignee']);
        $orderE->setMobileNum($order_info['mobile']);
        $orderE->setAddress($order_info['province_name'].$order_info['city_name'].$order_info['area_name'].$order_info['address']);
        $orderE->setOrderProducts($products);

        $order = \Dock\Order\Order::instance();

        try {
            $result = $order->placeOrder($orderE);
        } catch(\Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }
}