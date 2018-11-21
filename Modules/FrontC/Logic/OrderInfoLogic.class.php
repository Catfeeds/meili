<?php
namespace FrontC\Logic;

/**
 * Class OrderInfoLogic
 * @package FrontC\Logic
 * 用户订单相关管理逻辑层
 * 黑暗中的武者
 */
class OrderInfoLogic extends FrontBaseLogic {

    /**
     * @param array $request
     * @return array
     * 我的订单列表
     * 黑暗中的武者
     */
    function orderList($request = array()) {
        //普通用户ID查询
        if(!empty($request['m_id']) || session('M_ID') != null) {
            $param['where']['oi.m_id'] = session('M_ID') == null ? $request['m_id'] : session('M_ID');
        }
        //状态查询
        if($request['status'] == -1 || empty($request['status'])) {
            $param['where']['oi.status'] = array('exp', '<11');
        } else {
            $param['where']['oi.status'] = array('IN', $request['status']);
            if($request['status'] == 4) //已完成待评价的
                $param['where']['oi.is_comm'] = 0;
        }
        return D('FrontC/OrderInfo', 'Service')->orderList($param);
    }

    /**
     * @param array $request
     * @return array
     * 订单详情
     * 黑暗中的武者
     */
    function orderDetail($request = array()) {
        //参数判空
        if(empty($request['order_id'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //查询该订单对应店铺信息
        $shop_id=M('OrderInfo')->where(array('id'=>$request['order_id']))->getField('shop_id');
        $shop_name=M('Shop')->where(array('id'=>$shop_id,'status'=>1))->getField('name');
        //订单ID查询
        $param['where']['oi.id'] = $request['order_id'];
        $result=D('FrontC/OrderInfo', 'Service')->orderDetail($param);
        $result['shop_name']=$shop_name;
        return $result;
    }

    /**
     * @param array $request
     * @return string
     * 取消订单
     * 黑暗中的武者
     */
    function cancelOrder($request = array()) {
        //判断参数空
        if(empty($request['order_id'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('m_id'=>$request['m_id'],'id'=>$request['order_id']))->field('pay_status,status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(1), $order)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        //判断是否已支付
        if($order['pay_status'] == 1) { //已支付
            $data['status'] = 7; //7 退款
            $data['before_status'] = $order['status']; //记录取消前的状态
            $data['apply_refund_time']  = NOW_TIME; //申请退款时间
            $data['refund_reason']  = '未发货取消订单'; //申请退款原因
            $prompt = '取消成功，后台审核后所付金额将原路返回...';
        } else {
            $prompt = '取消成功！';
            $data['status'] = 10; //10 取消状态
            $data['cancel_order_time']  = NOW_TIME; //取消时间
        }
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>$request['order_id']))->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        //TODO 更新订单商品库存
        D('FrontC/OrderInfo', 'Service')->updOrderGoodsStock($request['order_id']);
        return $this->setLogicInfo($prompt, true);
    }

    /**
     * @param array $request
     * @return string
     * 催单操作
     * 黑暗中的武者
     */
    function urge($request = array()) {
        //判断参数空
        if(empty($request['order_id']))
            return $this->setLogicInfo('参数错误！', false);
        //获取订单信息
        $order = M('OrderInfo')->where(array('m_id'=>$request['m_id'],'id'=>$request['order_id']))->field('order_sn,pay_time,status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus($request['order_id'], array(2), $order))
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        //支付成功 一天后还未发货 可以提醒发货
        if($order['pay_time'] + 86400 > NOW_TIME)
            return $this->setLogicInfo('支付成功一天后可提醒发货！', false);
        //获取最近一次催单时间
        $urge = M('UrgeLog')->where(array('order_id'=>$request['order_id']))->order('id DESC')->field('id,create_time')->find();
        //判断是否在规定时间间隔内催单
        if($urge && $urge['create_time'] + C('URGE_INTERVAL') > NOW_TIME)
            return $this->setLogicInfo('提醒太频繁，每隔' . (C('URGE_INTERVAL')/60) . '分钟可提醒一次！', false);
        //如果还没有催单记录 或者 催单时间间隔无误  则添加记录
        //添加记录
        $data = array(
            'm_id'          => $request['m_id'],
            'order_id'      => $request['order_id'],
            'order_sn'      => $order['order_sn'],
            'order_status'  => $order['status'],
            'create_time'   => NOW_TIME,
        );
        if(!M('UrgeLog')->data($data)->add()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('提醒成功，会尽快给您发货！', true);
    }

    /**
     * @param array $request
     * @return bool
     * 完成订单相关操作
     * 黑暗中的武者
     */
    function signFor($request = array()) {
        //判断参数空
        if(empty($request['order_id'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('m_id'=>$request['m_id'],'id'=>$request['order_id']))->field('id order_id,order_sn,m_id,pay_amounts,status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(3,7), $order)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        $data['status']         = 4; //4 已完成
        $data['receiving_time'] = NOW_TIME; //签收时间
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>$request['order_id']))->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，签收失败！', false);
        }
        //TODO 完成订单后续操作
        D('FrontC/OrderInfo', 'Service')->afterSignFor(0, $order);

        return $this->setLogicInfo('操作成功！', true);
    }

    /**
     * @param array $request
     * @return bool
     * 评价订单
     * 黑暗中的武者
     */
    function comment($request = array()) {
        //判断参数空
        if(empty($request['order_id']) || empty($request['data']))
            return $this->setLogicInfo('参数错误！', false);
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus($request['order_id'], array(4)))
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        //解析json 评价数据为数组
        $comment_array = json_decode($_REQUEST['data'], 'array');
        if(empty($comment_array))
            return $this->setLogicInfo('评价数据解析失败！', false);
        //TODO 图片处理  统一上传
        if($request['is_pic'] == 1) {
            $up_result = api('UpDownLoad/upload', array(I('request.')));
            //array_dimension($result) == 1 ? $result : $result['fileData']
            if(isset($up_result['status'])) //上传失败
                return $this->setLogicInfo($up_result['info'], false);
            //数组一维键值 为  file_(goods_id)_(数字) 处理文件数组为 goods_id为键值的数组
            foreach($up_result as $key => $file) {
                $key_arr = explode('_', $key);
                $files[$key_arr[1]][] = $file['id'];
            }
        }
        //循环评价数据
        foreach($comment_array as $comment) {
            //评价内容判断
            if(empty($comment['content']))
                return $this->setLogicInfo('存在空评价内容！', false);
            if(strlen($comment['content']) > 600)
                return $this->setLogicInfo('存在评价内容超过200个字！', false);
            //创建评价数据
            $data[] = array(
                'm_id'          => $request['m_id'],
                'order_id'      => $request['order_id'],
                'goods_id'      => $comment['goods_id'],
                'level'         => $comment['level'],
                'content'       => $comment['content'],
                'pictures'      => empty($files[$comment['goods_id']]) ? '' : implode(',', $files[$comment['goods_id']]),
                'create_time'   => NOW_TIME,
            );
        }
        //添加评价
        if(!M('GoodsComment')->addAll($data))
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        //修改订单信息
        M('OrderInfo')->where(array('id'=>$request['order_id']))->data(array('is_comm'=>1))->save();
        //TODO 评价赠积分
//        if(C('COMMENT_INTEGRAL') != 0)
//            D('FrontC/Finance', 'Service')->addItgLog($request['m_id'], 1, 4, C('COMMENT_INTEGRAL'));

        return $this->setLogicInfo('评价成功！', true);
    }

    /**
     * @param array $request
     * @return array|bool|string
     * 网站评价提交
     */
    function commentWap($request = array()) {
        //判断参数空
        if(empty($request['order_id']))
            return $this->setLogicInfo('参数错误！', false);
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus($request['order_id'], array(4)))
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        //循环评价数据
        foreach($request['goods_id'] as $key => $goods_id) {
            //创建评价数据
            $data[] = array(
                'm_id'          => $request['m_id'],
                'order_id'      => $request['order_id'],
                'goods_id'      => $goods_id,
                'level'         => $request['level'][$key],
                'content'       => empty($request['content'][$key]) ? '默认评价！' : $request['content'][$key],
                'pictures'      => $request['pictures'][$key],
                'create_time'   => NOW_TIME,
            );
        }
        //添加评价
        if(!M('GoodsComment')->addAll($data))
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        //修改订单信息
        M('OrderInfo')->where(array('id'=>$request['order_id']))->data(array('status'=>5))->save();
        //TODO 评价赠积分
        if(C('COMMENT_INTEGRAL') != 0)
            D('FrontC/Finance', 'Service')->addItgLog($request['m_id'], 1, 4, C('COMMENT_INTEGRAL'));
        return $this->setLogicInfo('评价成功！', true);
    }

    /**
     * @param array $request
     * @return bool
     * 申请退款相关操作
     * 黑暗中的武者
     */
    function applyRefund($request = array()) {
        //判断参数空
        if(empty($request['order_id'])) {
            return $this->setLogicInfo('参数错误！', false);
        } if(empty($request['reason'])) {
            return $this->setLogicInfo('请输入退款原因！', false);
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('m_id'=>$request['m_id'],'id'=>$request['order_id']))->field('receiving_time,status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(2,3), $order)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        //验证是否还可以申请退款  退款通道过期关闭不能退
//        if($order['status'] == 4 && ($order['receiving_time'] + C('REFUND_DELAY') * 86400) <= NOW_TIME) {
//            return $this->setLogicInfo('退款通道已关闭！', false);
//        }
        $data['status']             = 7; //7 退款中
        $data['before_status']      = $order['status']; //
        $data['refund_reason']      = $request['reason']; //退款原因
        $data['apply_refund_time']  = NOW_TIME; //申请退款时间
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>$request['order_id']))->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，申请失败！', false);
        }
        return $this->setLogicInfo('申请成功，等待客服与您对接...', true);
    }

    /**
     * @param array $request
     * @return bool
     * 取消申请退款相关操作
     * 黑暗中的武者
     */
    function cancelRefund($request = array()) {
        //判断参数空
        if(empty($request['order_id'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('m_id'=>$request['m_id'],'id'=>$request['order_id']))->field('before_status,status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(7),$order)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        $data['status']             = $order['before_status']; //回到申请退款前的状态
        $data['cancel_refund_time'] = NOW_TIME; //取消申请退款时间
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>$request['order_id']))->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('取消退款申请成功！', true);
    }

    /*
     * 删除订单
     * */
    public function delOrder($request = array())
    {
        //判断参数空
        if(empty($request['order_id'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //获取订单信息
        $order = M('OrderInfo')->where(array('m_id'=>$request['m_id'],'id'=>$request['order_id']))->field('before_status,status')->find();
        //验证实时状态 是否可进行此操作
        if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(4,8,10),$order)) {
            return $this->setLogicInfo('当前状态不能进行此操作！', false);
        }
        $data['is_hidden']= 1; //隐藏订单
        //修改订单信息
        if(!M('OrderInfo')->where(array('id'=>$request['order_id']))->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('订单删除成功！', true);
    }
}