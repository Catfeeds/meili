<?php
namespace Home\Controller;

/**
 * Class CronController
 * @package Home\Controller
 * 计划任务
 * m--分钟  h--小时  具体时间
 */
class CronController extends HomeBaseController {

    /**
     * 每分钟执行
     */
    function cron_1m() {
        do {
            $order_list = $this->getOrderList();
            $this->cancelOrder($order_list);
            sleep(1);
        } while (!empty($order_list));
    }

    /**
     * @return mixed
     * 获取订单列表  未支付  超过支付时间
     */
    function getOrderList() {
        $where['status']        = 1;
        $where['create_time']   = array('exp', '<' . (NOW_TIME - C('CANCEL_ORDER')) . '');
        return M('OrderInfo')->field('id,status')->where($where)->page(0, 50)->order('id desc')->select();
    }

    /**
     * @param $order_list
     * 取消订单
     */
    function cancelOrder($order_list) {
        if(empty($order_list))
            return false;
        foreach($order_list as $order) {
            //验证实时状态 是否可进行此操作
            if(!D('FrontC/OrderInfo', 'Service')->checkStatus(0, array(1), $order))
                continue;
            $data['status'] = 10; //10 取消状态
            $data['cancel_order_time'] = NOW_TIME; //取消时间
            //修改订单信息
            if(!M('OrderInfo')->where(array('id'=>$order['id']))->data($data)->save())
                continue;
            //TODO 更新订单商品库存
            D('FrontC/OrderInfo', 'Service')->updOrderGoodsStock($order['id']);
        }
    }

    /**
     * 6小时执行一次
     * 定时器清理无效团购订单即下单了未支付的订单
     * */
    public function clean_group_6h()
    {
        //清理当前时间的前一个小时前的无效订单 所有用户都清理
        $before_time=strtotime("-1 hour") ;
        $param=array('flag'=>3,'status'=>1,'create_time'=>array('lt',$before_time));
        $order_ids=M('OrderInfoSer')->where($param)->field('id,flag_id')->select();
        if(!empty($order_ids)){
            foreach ($order_ids as $k=>$v){
                //删除团购记录
                M('ActivityGroupList')->where(array('id'=>$v['flag_id']))->delete();
                //删除订单
                M('OrderInfoSer')->where(array('id'=>$v['id']))->delete();
                //删除订单商品
                M('OrderService')->where(array('order_id'=>$v['id']))->delete();
            }
        }
    }


    /**
     * 团购判断计划任务
     */
    public function group_cron_1m() {
        do {
            $group_list = $this->getGroups();
            $this->groupRefund($group_list);
            sleep(1);
        } while (!empty($order_list));
    }



    /**
     * 筛选团购
     * */
    public function getGroups()
    {
        $where['status']        = 1;
        $where['end_time']   = array('exp', '<' . NOW_TIME . '');
        return M('ActivityGroupList')->field('id,status')->where($where)->order('id desc')->select();
    }
    /**
     * @param array $refund_records
     * @return bool
     * 团购失败退款
     * 黑暗中的武者
     */
    public function groupRefund($group_list = []) {
        if(empty($group_list)) {
            return false;
        }
        foreach($group_list as $group) {
            //获取订单信息
            $order_info = M('OrderInfoSer')->where(['flag'=>3,'flag_id' => $group['id']])->field('id order_id,m_id,order_sn,pay_amounts,status,payment')->find();
            M('ActivityGroupList')->where(['id'=>$group['id']])->data(['status'=>3])->save();
            if ($order_info['payment'] == 3) {
                //获取用户信息
                $user_info = M('Member')->where(['id'=>$order_info['m_id']])->field('balance')->find();
                M('Member')->where(['id'=>$order_info['m_id']])->setInc('balance', $order_info['pay_amounts']);
                D('FrontC/Finance', 'Service')->addBalanceRecords($order_info['m_id'],1,1,14,$order_info['pay_amounts'],$user_info['balance'],1,$order_info['order_id'],$order_info['order_sn']);
            }
            //微信退款，原路退回
            elseif ($order_info['payment'] == 2){
                //添加退款资金记录
                if(!M('FinanceTradeRecords')->where(array('order_id'=>$order_info['order_id'], 'trend'=>14, 'platform'=>$order_info['payment']))->count()){
                    D('FrontC/Finance', 'Service')->addTradeRecords($order_info['m_id'],1,2,$order_info['payment'],14,$order_info['pay_amounts'],0,$order_info['order_id'],$order_info['order_sn'],'',0);
                }
                //微信原路退款，要查询出支付流水
                $trade_no = M("FinanceTradeRecords")->where(array('trend'=>1,'symbol'=>1,'status'=>1,'order_id'=>$order_info['order_id']))->getField('trade_no');
                //D('Shop/OrderInfo', 'Service')->afterAgreeRefund($request['order_id'], $request['refund_itg_amounts']);
                $result = api('WeChat/refund', [$trade_no, $order_info['pay_amounts'], $order_info['pay_amounts'], 'mini',0, [new \Bms\Logic\OrderRefundRecordsLogic(), 'wxRefundCallback']]);
            }
            //改变订单状态
            M("OrderInfoSer")->where(['id'=>$order_info['order_id']])->data(['status'=>10,'cancel_order_time'=>NOW_TIME,'cancel_order_reason'=>'拼团失败'])->save();
            //修改用户消费总金额
//            M('Member')->where(['id'=>$order_info['m_id']])->setDec('consum_amounts', $order_info['pay_amounts']);
            //根据消费金额来修改用户的等级
//            $m_info =  M('Member')->where(['id'=>$order_info['m_id']])->field("level,consum_amounts")->find();
//            D('Bms/Member', 'Logic')->downMemLevel($order_info['m_id'],$m_info['consum_amounts'],$m_info['level']);

            //发送消息提醒
            // $param = [
            //     'receiver'      => $order_info['m_id'],
            //     'user_type'     => 1,
            //     'unique_code'   => 'bms_agree_refund_notify',
            //     'replaces'      => ['order_sn'=>$order_info['order_sn']],
            //     'param'         => $refund['order_id'],
            //     'synchronous'   => 1
            // ];

            // api('Send/sendMsg', [$param]);
        }
    }
}