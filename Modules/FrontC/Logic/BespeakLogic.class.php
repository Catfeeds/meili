<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/18
 * Time: 11:19
 */

namespace FrontC\Logic;


class BespeakLogic extends FrontBaseLogic
{
    /**
     * 获取取消预约理由
     * */
    public function cancelBespeakReason($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        if (empty($request['bespeak_id'])) {
            $service_name = '';
        } else {
            //获取该预预约项目名称
            $param = array('id' => $request['bespeak_id']);
            $service_name = M('Bespeak')->where($param)->getField('service_name');
        }
        //获取理由
        $reasons = D('FrontC/BespeakReason')->getRows();
        $result = array(
            'service_name' => $service_name,
            'reasons' => $reasons
        );
        if (empty($result)) {
            return array();
        } else {
            return $result;
        }
    }

    /**
     * 取消预约
     * */
    public function cancelBespeak($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        if (empty($request['bespeak_id'])) {
            return $this->setLogicInfo('参数错误哦！', false);
        }
        if (!empty($request['reason'])) {
            $reason = $request['reason'];
        } elseif (!empty($request['reason_id'])) {
            $reason = M('BespeakReason')->where(array('id' => $request['reason_id']))->getField('reason');
        } else {
            return $this->setLogicInfo('请输入取消理由哦！', false);
        }
        //验证该预约记录状态
        $bespeak_info = M('Bespeak')->field('status,way,order_id')->find($request['bespeak_id']);
        if ($bespeak_info['status'] == 1) {
            if ($bespeak_info['way'] == 1) {
                //品项 改变订单服务商品状态
                $data_or['id'] = $bespeak_info['order_id'];
                $data_or['flag'] = 0;
                $res = M('OrderService')->data($data_or)->save();
                if (!$res) {
                    return $this->setLogicInfo('系统繁忙，稍后重试！', false);
                }
            } else {
                //卡包 操作一卡通点数
                $order_card_info = M('OrderCard')->field('before_count')->find($bespeak_info['order_id']);
                $data_card['id'] = $bespeak_info['order_id'];
                $data_card['count'] = $order_card_info['before_count'];
                $resu = M('OrderCard')->data($data_card)->save();
                if (!$resu) {
                    return $this->setLogicInfo('系统繁忙，稍后重试！', false);
                }
            }
            //操作预约订单状态
            $data['id'] = $request['bespeak_id'];
            $data['status'] = 2;
            $data['bespeak_reason'] = $reason;
            $res = M('Bespeak')->data($data)->save();
            if (!$res) {
                return $this->setLogicInfo('系统繁忙，稍后重试！', false);
            } else {
                return array();
            }
        } else {
            return $this->setLogicInfo('该状态不可操作！', false);
        }
    }

    /**
     * 获取预约记录
     * */
    public function getBespeak($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        if (empty($request['status'])) {
            return $this->setLogicInfo('参数错误哦！', false);
        }
        $param = array('m_id' => $request['m_id'], 'status' => $request['status']);
        $bespeaks = D('FrontC/Bespeak', 'Model')->getRows($param);
        if(empty($bespeaks)){
            $bespeaks = array();
        }
        //获取用户有效一卡通数量和有效待预约数量
        $card_arr=$this->myCard($request);
        $card_count=(string)count($card_arr);
        $service_arr=$this->goBespeak($request);
        $service_count=(string)count($service_arr['service']);
        return array(
            'card_count'=>$card_count,
            'service_count'=>$service_count,
            'bespeaks'=>$bespeaks,
        );
    }

    /**
     * [获取用户有效一卡通调用、]
     * 用户有效一卡通
     * */
    public function myCard($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        //查出用户有效订单
        $param_m = array('m_id' => $request['m_id'], 'flag' => 2, 'status' => 2);
        $orderIds = M('OrderInfoSer')->where($param_m)->field('id')->select();
        if (!empty($orderIds)) {
            foreach ($orderIds as $k1 => $v1) {
//               $card=M('OrderCard')->where(array('order_id'=>$v1['id']))->find();
                $card = M('OrderCard')->alias('ordercard')
                    ->field('ordercard.id order_card_id,ordercard.card_id,ordercard.font_color,ordercard.shop_id,ordercard.card_name,ordercard.price,ordercard.type,ordercard.count,ordercard.m_count,ordercard.y_count,ordercard.start_time,ordercard.end_time')
                    ->where(array('order_id' => $v1['id']))
                    ->find();
                $cards[] = $card;
            }
            //时间处理 卡类型名称处理
            foreach ($cards as $k => $v) {
                //获取该卡背景图
                $card_bg_cover_id=M('Card')->where(array('id'=>$v['card_id']))->getField('bg_cover');
                $v['cover']=M('File')->where(array('id'=>$card_bg_cover_id))->getField('abs_url');
                $v['start_time'] = date('Y-m-d', $v['start_time']);
                $v['end_time'] = date('Y-m-d', $v['end_time']);
                if ($v['type'] == 1) {
                    $v['type'] = '季卡';
                    $v['before_count'] = $v['m_count'];
                } else {
                    $v['type'] = '年卡';
                    $v['before_count'] = $v['y_count'];
                }
                $result[] = $v;
            }
            if (!empty($result)) {
                return $result;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /*
     * 用户有效服务
     * */
//    public function myService($request = array())
//    {
//        if(empty($request['m_id'])) {
//            return $this->setLogicInfo('请先登录哦！', false);
//        }
//        //查出用户有效订单
//        $param_m=array('m_id'=>$request['m_id'],'flag'=>array('lt',2),'status'=>2);
//        $orderIds=M('OrderInfoSer')->where($param_m)->field('id')->select();
//        if(!empty($orderIds)){
//            foreach ($orderIds as $k=>$v){
//                $card=M('OrderService')
//                    ->field('id order_service_id,order_id,service_id,service_name,cover')
//                    ->where(array('order_id'=>$v['id']))
//                    ->find();
//                $cards[]=$card;
//            }
//            foreach ($cards as $k1=>$v1){
//                $desc=M('Service')->where(array('id'=>$v1['service_id']))->getField('service_short_desc');
//                $v1['service_short_desc']=$desc;
//                $result[]=$v1;
//            }
//            if(!empty($result)){
//                return $result;
//            }else{
//                return array();
//            }
//        }else{
//            return array();
//        }
//    }

    /**
     * 去预约
     * */
    public function goBespeak($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请登录哦！', false);
        }
        if (empty($request['card_id']) && empty($request['order_card_id'])) {
            //品项预约
            //判断是否传递店铺ID
            if (empty($request['shop_id'])) {
                //查出用户有效订单 不过滤店铺
                $param_m = array('m_id' => $request['m_id'], 'flag' => array('lt', 2), 'status' => 2);
                $orderIds = M('OrderInfoSer')->where($param_m)->field('id,shop_id')->select();
                if (!empty($orderIds)) {
                    foreach ($orderIds as $k => $v) {
                        $card = M('OrderService')
                            ->field('id order_service_id,service_id,number,service_name,cover')
                            ->where(array('order_id' => $v['id'], 'flag' => 0))
                            ->select();
                        $cardc[] = $card;
                    }
                    foreach ($cardc as $kk=>$vv){
                        if(sizeof($vv) == 1){
                            $cards[]=$vv[0];
                        }elseif (sizeof($vv) > 1){
                            foreach ($vv as $kkk=>$vvv){
                                $cards[]=$vvv;
                            }
                        }
                    }
                    foreach ($cards as $k1 => $v1) {
                        $order_id=M('OrderService')->where(array('id'=>$v1['order_service_id']))->getField('order_id');
                        $shop_id=M('OrderInfoSer')->where(array('id'=>$order_id))->getField('shop_id');
                        $v1['shop_id'] = $shop_id;
                        $shop_name = M('Shop')->where(array('id' => $shop_id))->getField('name');
                        $v1['shop_name'] = $shop_name;
                        $desc = M('Service')->where(array('id' => $v1['service_id']))->getField('service_short_desc');
                        $v1['service_short_desc'] = $desc;
                        $result[] = $v1;
                    }
                    if (!empty($result)) {
                        $result_final = array(
                            'shop_name' => '选择项目即可',
                            'service' => $result,
                        );
                        return $result_final;
                    } else {
                        return array(
                            'shop_name' => '暂无可预约项',
                            'service' => array(),
                        );
                    }
                } else {
                    return array(
                        'shop_name' => '暂无可预约项',
                        'service' => array(),
                    );
                }
            } else {
                //查出用户有效订单 需要过滤店铺
                $param_m = array('m_id' => $request['m_id'], 'flag' => array('lt', 2), 'status' => 2, 'shop_id' => $request['shop_id']);
                $orderIds = M('OrderInfoSer')->where($param_m)->field('id,shop_id')->select();
                if (!empty($orderIds)) {
                    foreach ($orderIds as $k => $v) {
                        $card = M('OrderService')
                            ->field('id order_service_id,service_id,number,service_name,cover')
                            ->where(array('order_id' => $v['id'], 'flag' => 0))
                            ->select();
                        $cardc[] = $card;
                    }
                    //套餐商品可能是多个 需要将不规则数组转化为统一一维数组
                    foreach ($cardc as $kk=>$vv){
                        if(sizeof($vv) == 1){
                            $cards[]=$vv[0];
                        }elseif (sizeof($vv) > 1){
                            foreach ($vv as $kkk=>$vvv){
                                $cards[]=$vvv;
                            }
                        }
                    }
                    foreach ($cards as $k1 => $v1) {
                        $desc = M('Service')->where(array('id' => $v1['service_id']))->getField('service_short_desc');
                        $v1['service_short_desc'] = $desc;
                        $result[] = $v1;
                    }
                    //获取店铺名称
                    $shop_name = M('Shop')->where(array('id' => $request['shop_id']))->getField('name');
                    if (!empty($result)) {
                        return array(
                            'shop_id' => $request['shop_id'],
                            'shop_name' => $shop_name,
                            'service' => $result,
                        );
                    } else {
                        return array(
                            'shop_id' => $request['shop_id'],
                            'shop_name' => $shop_name,
                            'service' => array(),
                        );
                    }
                } else {
                    $shop_name = M('Shop')->where(array('id' => $request['shop_id']))->getField('name');
                    return array(
                        'shop_id' => $request['shop_id'],
                        'shop_name' => $shop_name,
                        'service' => array(),
                    );
                }
            }
        } else {
            if (empty($request['order_card_id'] || empty($request['card_id']))) {
                return $this->setLogicInfo('参数错误！', false);
            }
            //卡包预约
            $shop_id = M('OrderCard')->where(array('id' => $request['order_card_id']))->getField('shop_id');
            $shop_name = M('Shop')->where(array('id' => $shop_id))->getField('name');
            //通过一卡通ID查询该卡下有效的项目
            $param = array('card_id' => $request['card_id']);
            $servers = D('FrontC/CardService', 'Model')->getService($param);
            foreach ($servers as $k => $v) {
                $v['shop_id'] = $shop_id;
                $serverss[] = $v;
            }
            if (!empty($servers) || !empty($shop_name)) {
                return array(
                    'shop_id' => $shop_id,
                    'shop_name' => $shop_name,
                    'service' => $serverss,
                );
            } else {
                return array(
                    'shop_id' => $shop_id,
                    'shop_name' => $shop_name,
                    'service' => array(),
                );
            }
        }
    }

    /**
     * 确认预约
     * */
    public function confirmBespeak($request = array())
    {
        if (empty($request['m_id']))
            return $this->setLogicInfo('请登录哦！', false);
        if (empty($request['name']))
            return $this->setLogicInfo('请填姓名哦！', false);
        if (empty($request['mobile']))
            return $this->setLogicInfo('请填手机号哦！', false);
        if (!preg_match(C('MOBILE'), $request['mobile']))
            return $this->setLogicInfo('请填正确格式的手机号码哦', false);
        if (empty($request['shop_id']))
            return $this->setLogicInfo('请选择实体店哦！', false);
        if (empty($request['time_id']) || empty($request['now_time']))
            return $this->setLogicInfo('请选择预约时间哦！', false);
        if (empty($request['service_id']))
            return $this->setLogicInfo('请选择预约项目哦！', false);
        /*
         * 预约时间处理
         * */
        $now_time = $request['now_time'];
        $now_time_ymd = date('Y-m-d', $now_time);
        $time_his = M('BespeakTime')->field('start_time,end_time')->find($request['time_id']);
        $start_time_his = date('H:i:s', $time_his['start_time']);
        $end_time_his = date('H:i:s', $time_his['end_time']);
        $start_time = strtotime($now_time_ymd . ' ' . $start_time_his);
        $end_time = strtotime($now_time_ymd . ' ' . $end_time_his);
        //判断何种方式预约
        if (empty($request['card_id'])) {
            if (empty($request['order_service_id']))
                return $this->setLogicInfo('参数错误哦！', false);
            $service_name = M('OrderService')->where(array('id' => $request['order_service_id']))->getField('service_name');
            //改变该服务商品状态 标识为已预约使用
            $data_res['id'] = $request['order_service_id'];
            $data_res['flag'] = 1;
            $res = M('OrderService')->data($data_res)->save();
            if (!$res) {
                return $this->setLogicInfo('系统繁忙，稍后重试！', false);
            }
            $bespeak_sn = D('FrontC/Flow', 'Service')->createSn(4);
            //品项预约
            $data['m_id'] = $request['m_id'];
            $data['name'] = $request['name'];
            $data['mobile'] = $request['mobile'];
            $data['shop_id'] = $request['shop_id'];
            $data['time_id'] = $request['time_id'];
            $data['bespeak_time'] = $request['now_time'];
            $data['service_id'] = $request['service_id'];
            $data['service_name'] = $service_name;
            //订单一卡通ID
            $data['order_id'] = $request['order_service_id'];
            $data['card_id'] = 0;  //没有选择卡包预约则为0
            $data['way'] = 1;   //1品项 2卡包
            $data['status'] = 1;   //1品项 2卡包s
            $data['bespeak_sn'] = $bespeak_sn;
            $data['create_time'] = NOW_TIME;
            $data['update_time'] = NOW_TIME;
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time;
            $result = M('Bespeak')->data($data)->add();
            //改变该服务商品订单状态 预约完成后就不能再去预约 注意套餐和单项的区别
            //通过order_service_id获取订单信息
//            $order_id=M('OrderService')->where(array('id'=>$request['order_service_id']))->getField('order_id');
//            $order_info=M('OrderInfoSer')->field('flag,flag_id')->find($order_id);
//            if($order_info['flag'] == 0){
//                //单项商品
//                $data['id']=$order_id;
//                $data['status']=3;  //状态3已预约待服务
//                M('OrderInfoSer')->data($data)->save();
//            }elseif ($order_info['flag'] == 1){
//                //套餐 判断该套餐下的商品是否全部预约完成 若预约完则改变状态
//                //获取该套餐所有商品数量 去比对已经预约的记录
//                //获取该用户套餐已经使用的项目数量
//
//            }elseif ($order_info['flag'] == 3){
//                //团购单项商品
//
//            }
            if (!$result) {
                return $this->setLogicInfo('系统繁忙，稍后重试！', false);
            } else {
                return array();
            }
        } else {
            if (empty($request['order_card_id'])) {
                return $this->setLogicInfo('参数错误哦！', false);
            }
            //卡包预约
            $service_info = M('CardService')->where(array('service_id' => $request['service_id'], 'card_id' => $request['card_id']))->field('count,service_name')->find();
            //获取用户该卡点数信息 操作
            $card_info = M('OrderCard')->where(array('id' => $request['order_card_id']))->field('count')->find();
            //判断该卡点数是否足够支付该项目点数
            if ($card_info['count'] < $service_info['count']) {
                return $this->setLogicInfo('该卡点数不足哦！', false);
            } else {
                //操作该卡点数信息
                $before_count = $card_info['count'];
                $now_count = $card_info['count'] - $service_info['count'];
                $data_card['id'] = $request['order_card_id'];
                $data_card['before_count'] = $before_count;
                $data_card['count'] = $now_count;
                $res = M('OrderCard')->data($data_card)->save();
                if (!$res) {
                    return $this->setLogicInfo('系统繁忙，稍后重试！', false);
                }
                $bespeak_sn = D('FrontC/Flow', 'Service')->createSn(4);
                //卡包预约
                $data['m_id'] = $request['m_id'];
                $data['name'] = $request['name'];
                $data['mobile'] = $request['mobile'];
                $data['shop_id'] = $request['shop_id'];
                $data['time_id'] = $request['time_id'];
                $data['bespeak_time'] = $request['now_time'];
                $data['service_id'] = $request['service_id'];
                $data['service_name'] = $service_info['service_name'];
                $data['card_id'] = $request['card_id'];
                //订单一卡通ID
                $data['order_id'] = $request['order_card_id'];
                $data['way'] = 2;   //1品项 2卡包
                $data['status'] = 1;   //1品项 2卡包
                $data['bespeak_sn'] = $bespeak_sn;
                $data['create_time'] = NOW_TIME;
                $data['update_time'] = NOW_TIME;
                $data['start_time'] = $start_time;
                $data['end_time'] = $end_time;
                $result = M('Bespeak')->data($data)->add();
                if (!$result) {
                    return $this->setLogicInfo('系统繁忙，稍后重试！', false);
                } else {
                    return array();
                }
            }
        }
    }

    /**
     * 获取预约时间段
     * */
    public function getTimes($request = array())
    {
        if (empty($request['m_id']))
            return $this->setLogicInfo('请先登录哦！', false);
        if (empty($request['shop_id']))
            return $this->setLogicInfo('请选择需要获取时间的店铺哦！', false);
        if (empty($request['now_time'])) {
            $now_time = NOW_TIME;
        } else {
            $now_time = $request['now_time'];
        }
        $times = M('BespeakTime')->where(array('status' => 1))->field('id time_id,max_count,name,start_time,end_time')->select();
        //判断时间是否过期 添加标识
        $now_time_ymd = date('Y-m-d', $now_time);
        $now_time_his = date('H:i:s', $now_time);
        $time_ymd = date('Y-m-d', NOW_TIME);
        if ($now_time_ymd <= $time_ymd) {
            //当天的时间 需要判断该时间段是否有效
            foreach ($times as $k => $v) {
                $temp_time = date('H:i:s', $v['end_time']);
                //判断有效时间
                if ($temp_time < $now_time_his) {
                    $v['is_effect'] = '0';
                } else {
                    $v['is_effect'] = '1';
                }
                //判断该时间段是否预约已满
                //组装查询时间条件
                $start_time_his = date('H:i:s', $v['start_time']);
                $end_time_his = date('H:i:s', $v['end_time']);
                $start_time = strtotime($now_time_ymd . ' ' . $start_time_his);
                $end_time = strtotime($now_time_ymd . ' ' . $end_time_his);
                $num = M('Bespeak')->where(array('status'=>1,'time_id' => $v['time_id'], 'start_time' => $start_time, 'end_time' => $end_time))->count();
                if ($num <= $v['max_count']) {
                    $v['is_enough'] = '0';
                    $v['num'] = $num;
                    $v['message'] = '可预约';
                } else {
                    $v['is_enough'] = '1';
                    $v['num'] = $num;
                    $v['message'] = '已约满';
                }
                $result[] = $v;
            }
        } else {
            //后面的时间 则全部设定为有效
            foreach ($times as $k => $v) {
                $v['is_effect'] = '1';
                //判断该时间段是否预约已满
                //组装查询时间条件
                $start_time_his = date('H:i:s', $v['start_time']);
                $end_time_his = date('H:i:s', $v['end_time']);
                $start_time = strtotime($now_time_ymd . ' ' . $start_time_his);
                $end_time = strtotime($now_time_ymd . ' ' . $end_time_his);
                $num = M('Bespeak')->where(array('shop_id'=>$request['shop_id'],'time_id' => $v['time_id'], 'start_time' => $start_time, 'end_time' => $end_time))->count();
                if ($num == 0) {
                    $v['is_enough'] = '0';
                    $v['num'] = $num;
                    $v['message'] = '无预约';
                } else {
                    $v['is_enough'] = '1';
                    $v['num'] = $num;
                    $v['message'] = '已约满';
                }
                $result[] = $v;
            }
        }
        //添加月日星期
        //当前时间月日 星期
        $weekarray = array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
        $time_one = NOW_TIME;
        $time_week_one = date('w', $time_one);
        $time_md_one = date('m月d日', $time_one);
        $week_one = $weekarray["$time_week_one"];
        $time[0] = array('now_time' => (string)$time_one, 'time_md' => $time_md_one, 'time_wekk' => $week_one);
        $time_two = strtotime("+1 day");
        $time_week_two = date('w', $time_two);
        $time_md_two = date('m月d日', $time_two);
        $week_two = $weekarray["$time_week_two"];
        $time[1] = array('now_time' => (string)$time_two, 'time_md' => $time_md_two, 'time_wekk' => $week_two);
        $time_three = strtotime("+2 day");
        $time_week_three = date('w', $time_three);
        $time_md_three = date('m-d', $time_three);
        $week_three = $weekarray["$time_week_three"];
        $time[2] = array('now_time' => (string)$time_three, 'time_md' => $time_md_three, 'time_wekk' => $week_three);
        if (!empty($result)) {
            $result_final['time'] = $time;
            $result_final['his'] = $result;
            return $result_final;
        } else {
            return array();
        }
    }
}