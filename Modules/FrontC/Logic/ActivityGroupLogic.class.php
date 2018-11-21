<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/9
 * Time: 16:14
 */

namespace FrontC\Logic;


class ActivityGroupLogic extends FrontBaseLogic
{

    /**
     * 删除团购失败status=3的团购
     * */
    public function delGroup($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        if (empty($request['group_list_id'])) {
            return $this->setLogicInfo('请选择需要删除的记录哦！', false);
        }
        //验证状态是否为3
        $status=M('ActivityGroupList')->where(array('id'=>$request['group_list_id']))->getField('status');
        if($status != 3){
            return $this->setLogicInfo('该状态不能删除哦！', false);
        }
        //删除团购记录
        $res_one=M('ActivityGroupList')->where(array('id'=>$request['group_list_id']))->delete();
        $param=array('flag'=>3,'m_id'=>$request['m_id'],'flag_id'=>$request['group_list_id']);
        $order_id=M('OrderInfoSer')->where($param)->field('id')->find();
        //删除订单
        $res_two=M('OrderInfoSer')->where(array('id'=>$order_id['id']))->delete();
        //删除订单商品
        $res_three=M('OrderService')->where(array('order_id'=>$order_id['id']))->delete();
        if($res_one && $res_two && $res_three){
            return array();
        }else{
            return $this->setLogicInfo('系统繁忙，请稍后重试！', false);
        }

    }

    /**
     * 团购商品列表
     * */
    public function goodsList()
    {
        //筛选有效的团购商品
        $param['where']['group_service.group_start_time'] = array('gt', NOW_TIME);
        $param['where']['group_service.group_end_time'] = array('lt', NOW_TIME);
        $group_services = D('FrontC/ActivityGroupService', 'Model')->getRows($param);
        if (!empty($group_services)) {
            return $group_services;
        } else {
            return array();
        }
    }

    /**
     * 我的团购列表
     * */
    public function myGroup($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        //获取有效的团购列表
        //status 0未付款未成立的团 1成团中 2已成团 3已经失败
        if (empty($request['status'])) {
            $param=array('m_id'=>$request['m_id'],'status'=>array('gt',0));
        }else{
            $param=array('m_id'=>$request['m_id'],'status'=>$request['status']);
        }
       $groups=D('FrontC/ActivityGroupList','Model')->getRows($param);
        if(empty($groups)){
            return array();
        }
        //已经有效开团每个团的人数情况 || 获取该团有效参团会员
        foreach ($groups as $k=>$v){
            //判断该团状态 1成团中 2已成团 3团失败
           if($v['status'] == 1){
               $v['status_info']='等待成团！';
           }elseif ($v['status'] == 2){
               $v['status_info']='拼团成功，等待预约！';
           }elseif ($v['status'] == 3){
               $v['status_info']='拼团失败，已退款！';
           }
           //获取该团购订单ID
            $order_id=M('OrderInfoSer')->where(array('flag_id'=>$v['group_list_id'],'m_id'=>$request['m_id']))->getField('id');
            $v['order_id']=$order_id ? $order_id : '0';
            //获取该团团长
            $is_head_m_id=M('ActivityGroupList')->where(array('group_sn'=>$v['group_sn'],'is_head'=>1))->getField('m_id');
            //获取会员
            $m_ids=M('ActivityGroupList')->where(array('group_sn'=>$v['group_sn'],'status'=>array('gt',0)))->field('m_id')->select();
            if(!empty($m_ids)){
                foreach ($m_ids as $kk=>$vv){
                     $m_info=M('Member')->where(array('id'=>$vv['m_id']))->getField('head');
                     $m_head_info=M('File')->field('abs_url head')->find($m_info);
                     if($is_head_m_id == $vv['m_id']){
                         $m_head_info['id_head'] = '1';
                     }else{
                         $m_head_info['id_head'] = '0';
                     }
                     $member[]=$m_head_info;
                }
            }else{
                $member=array();
            }
            //根据团购sn获取该团有效团个数
            $count=M('ActivityGroupList')->where(array('group_sn'=>$v['group_sn'],'status'=>array('gt',0)))->count();
            $num=$v['group_service_info']['people_limit'] - $count;
            $v['people_num']=(string)$num;
            $v['member']=$member;
            unset($member);
            $result[]=$v;
        }
        if(empty($result))
            return array();
        return $result;
    }

    /**
     * 团购详情
     * */
    public function detail($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        if (empty($request['group_list_id'])) {
            return $this->setLogicInfo('参数错误哦！', false);
        }
        //获取团购信息
        $param=array('id'=>$request['group_list_id']);
        $group_info=D('FrontC/ActivityGroupList','Model')->findRow($param);
        //团购过期时间处理
        $surplus_time=$group_info['end_time']-NOW_TIME;
        if($surplus_time > 0){
            $group_info['end_time']=$surplus_time;
        }else{
            $group_info['end_time']='000000';
        }
        //判断状态 赋值不同提示
        if($group_info['status'] == 1){
            $group_info['status_info'] = '成团中！';
        }elseif ($group_info['status'] == 2){
            $group_info['status_info'] = '恭喜您拼购成功！';
        }elseif ($group_info['status'] == 3){
            $group_info['status_info'] = '拼团失败，已退款！';
        }
        //获取该团团长
        $is_head_m_id=M('ActivityGroupList')->where(array('group_sn'=>$group_info['group_sn'],'is_head'=>1))->getField('m_id');
        //根据团购状态判断成团会员信息 1成团中 2已成团 3成团失败
        $m_ids=M('ActivityGroupList')->where(array('group_sn'=>$group_info['group_sn'],'status'=>array('gt',0)))->field('m_id')->select();
        //获取已经加入会员信息即可
        if(!empty($m_ids)){
            foreach ($m_ids as $kk=>$vv){
                $m_info=M('Member')->where(array('id'=>$vv['m_id']))->getField('head');
                $m_head_info=M('File')->field('abs_url head')->find($m_info);
                //判断该用户是否是该团团长
               if($is_head_m_id == $vv['m_id']){
                   $m_head_info['is_head'] = '1';
               }else{
                   $m_head_info['is_head'] = '0';
               }
                $member[]=$m_head_info;
            }
        }else{
            $member=array();
        }
        $group_info['member']=$member;
        if($group_info['status'] == 2 || $group_info['status'] == 3){
            $group_info['people_num']='';
        }elseif($group_info['status'] == 1){
            //获取成团所差人数 根据团购sn获取该团有效团个数
            $count=M('ActivityGroupList')->where(array('group_sn'=>$group_info['group_sn']))->count();
            $num=$group_info['group_service_info']['people_limit'] - $count;
            $group_info['people_num']=(string)$num;
        }
        //获取团购订单iD
        $order_id=M('OrderInfoSer')->where(array('m_id'=>$request['m_id'],'flag_id'=>$request['group_list_id']))->getField('id');
        $group_info['order_id']=$order_id ? $order_id : '0';
        if(empty($group_info))
            return array();
        return $group_info;
    }


    /**
     * 开团详情
     * */
    public function groupDetail($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        if (empty($request['group_service_id'])) {
            return $this->setLogicInfo('请选择团购商品！', false);
        }
        //获取团购商品信息
        $param = array('id' => $request['group_service_id']);
        $group_service = D('FrontC/ActivityGroupService', 'Model')->getRow($param);
        //判断该商品是 2 套餐还是 1服务商品
//        if ($group_service['service_type'] == 2) {
//            //套餐
//            $param_service = array('package.id' => $group_service['service_id']);
//            $service_info = D('FrontC/Package', 'Model')->getRowSeS($param_service);
//            $service_info['desc'] = path2abs($service_info['package_desc']);
//            unset($service_info['package_desc']);
//        } else {
            //单个服务商品
            $param_service = array('service.id' => $group_service['service_id']);
            $service_info = D('FrontC/Service', 'Model')->getRowSeS($param_service);
            $service_info['service_id']=$group_service['service_id'];
            $service_info['group_service_id']=$request['group_service_id'];
            $service_info['desc'] = path2abs($service_info['service_desc']);
            unset($service_info['service_desc']);
//        }
        $service_info['group_price'] = $group_service['group_price'];
        if (!empty($service_info)) {
            //获取该商品已经开团未满的团
            $groups=D('ActivityGroupList')->where(array('group_service_id'=>$request['group_service_id'],'status'=>1,'is_head'=>1))->field('id group_list_id,group_service_id,group_sn,m_id,end_time')->select();
            foreach ($groups as $k=>$v){
                //还需获取成团所差人数 根据团购sn获取该团有效团个数
                $count=M('ActivityGroupList')->where(array('group_sn'=>$v['group_sn']))->count();
                $people_limit=M('ActivityGroupService')->where(array('id'=>$v['group_service_id']))->getField('people_limit');
                $num=$people_limit - $count;
                $v['people_num']=(string)$num;
                //剩余时间处理
                $surplus_time=$v['end_time']-NOW_TIME;
                if($surplus_time > 0){
                    $v['end_time']=$surplus_time;
                }else{
                    $v['end_time']='000000';
                }
                //获取用户信息 头像 昵称
                $m_info=M('Member')->field('nickname,head')->find($v['m_id']);
                $head_path=M('File')->where(array('id'=>$m_info['head']))->getField('abs_url');
                $v['nickname']=$m_info['nickname'].'的团';
                $v['head']=$head_path;
                $groups_c[]=$v;
            }
            if(empty($groups_c)){
                $service_info['group_list'] = array();
            }else{
                $service_info['group_list'] = $groups_c;
            }
            return $service_info;

        } else {
            return array();
        }
    }

    /**
     * 团购确认订单
     */
    function confirmGroup($request = array())
    {
        //整理订单信息 团购商品
        if (empty($request['m_id']))
            return $this->setLogicInfo('请先登录哦！', false);
        if (empty($request['group_service_id']))
            return $this->setLogicInfo('团购商品参数错误哦！', false);
        //获取团购价格 团购商品ID
        $group_info=M('ActivityGroupService')->field('id group_service_id,group_price,number,service_id')->find($request['group_service_id']);
        //单项服务商品
        $param = array('service.id' => $group_info['service_id'], 'service.status' => 1);
        $result = D('FrontC/Service', 'Model')->getRowSe($param);
        $result['price']=$group_info['group_price'];
        $result['group_service_id']=$group_info['group_service_id'];
        $result['goods_amounts'] = $group_info['group_price'];
        $result['order_amounts'] = $group_info['group_price'];
        $result['pay_amounts'] = $group_info['group_price'];
        if (empty($result))
            return $this->setLogicInfo($this->getLogicInfo(), false);
        return $result;
    }

    /**
     * 开/参团
     * */
    public function ojGroup($request = array())
    {
        if (empty($request['m_id']))
            return $this->setLogicInfo('请先登录哦！', false);
        if (empty($request['group_service_id']))
            return $this->setLogicInfo('团购商品参数错误哦！', false);
        //创建团记录
        $group_list_id = $this->createGroupList($request);
        if (!$group_list_id) {
             return $this->createGroupList($request);
        }
        //创建订单数据 入订单
        $order_sn = D('FrontC/Flow', 'Service')->createSn(5);
        //获取该团购商品信息
        $service_info = M('ActivityGroupService')->field('group_price,service_id')->find($request['group_service_id']);
        $data = array(
            'order_sn' => $order_sn,
            'm_id' => $request['m_id'],
            'shop_id' => $request['shop_id'],
            'coupon_amounts' => 0.00,
            'goods_amounts' => $service_info['group_price'],
            'order_amounts' => $service_info['group_price'],
            'pay_amounts' => $service_info['group_price'],
            'remark' => 'remark',
            'flag' => 3,
            'flag_id' => $group_list_id,
            'create_time' => NOW_TIME,
        );
        //创建订单
        $order_id = M('OrderInfoSer')->data($data)->add();
        if (!$order_id)
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);

        //获取团购商品信息
        $parm = array('service.id' => $service_info['service_id']);
        $result = D('FrontC/Service')->getRowSe($parm);
        $this->_afterSubmitSuccessDo($order_id, $request, $result);
        //获取用户余额
        $member_param = array('id' => $request['m_id'], 'status' => 1);
        $balance = D('FrontC/Member', 'Model')->getBalance($member_param);
        $shop_param = array('id' => $request['shop_id'], 'status' => 1);
        $shop_info = D('FrontC/Shop', 'Model')->getRow($shop_param);
        return array(
            'order_id' => $order_id,
            'order_sn' => $order_sn,
            'pay_amounts' => $service_info['group_price'],
            'balance' => $balance,
            'goods_name' => $result['name'],
            'cover' => $result['cover'],
            'shop_name' => $shop_info['name'],
        );
    }


    /**
     * [提交订单调用]
     * @param $order_id
     * @param $request
     * @param $result
     * 提交订单成功后执行
     */
    private function _afterSubmitSuccessDo($order_id, $request, $result)
    {
        //单个服务商品
        $service_data['order_id'] = $order_id;
        $service_data['m_id'] = $request['m_id'];
        $service_data['service_id'] = $result['id'];
        $service_data['service_name'] = $result['name'];
        $service_data['price'] = $result['price'];
        $service_data['cover'] = $result['cover'];
        $service_data['number'] = 1;
        $service_data['create_time'] = NOW_TIME;
        M('OrderService')->data($service_data)->add();
    }

    /**
     * [提交订单调用、]
     * 创建团记录和加入别人的团
     */
    public function createGroupList($request = [])
    {
        //这里按照参团和开团两种处理
        if ($request['group_list_id']) {
            //参团
            $group_info = M('ActivityGroupList')->where(['id' => $request['group_list_id'], 'is_head' => 1])->field('m_id,group_sn,end_time,id')->find();
            if ($group_info['m_id'] == $request['m_id']) {
                return $this->setLogicInfo('不能参加自己的团！', false);
            }
            //如果用户已经参团了
            if (M("ActivityGroupList")->where(['group_list_id' => $request['group_list_id'], 'm_id' => $request['m_id']])->find()) {
                return $this->setLogicInfo('您已经参加过该团！', false);
            }
            //判断该团时间是否有效
            $now_time=NOW_TIME;
            $group_info['end_time']=(int)$group_info['end_time'];
            if ($group_info['end_time'] <= $now_time) {
                return $this->setLogicInfo('该团已经过期！', false);
            }
            //判断人数是否已满 通过团购SN判断
            //获取该团购上限人数
            $group=M('ActivityGroupService')->find($request['group_service_id']);
            //获取该商品已经存在团购SN和已经存在的团购
            $sn=M('ActivityGroupList')->where(array('id'=>$request['group_list_id']))->getField('group_sn');
            //若是该团已经成团则不能参加
            $count_c=M('ActivityGroupList')->where(array('group_sn'=>$sn,'status'=>2))->count();
            if($count_c > 0){
                return $this->setLogicInfo('该团已经成团，请参加新团吧！', false);
            }
            //判断该团还在进行中的数量
            $count=M('ActivityGroupList')->where(array('group_sn'=>$sn,'status'=>1))->count();
            $count=$count+1;
            if($count > $group['people_limit']){
                return $this->setLogicInfo('该团人数已满！', false);
            }

            $data = [
                'group_sn' => $group_info['group_sn'],
                'm_id' => $request['m_id'],
                'group_service_id' => $request['group_service_id'],
                'is_head' => 0,
                'create_time' => NOW_TIME,
                'end_time' => $group_info['end_time'],
                'group_list_id' => $group_info['id'],
                'status' => 0,
            ];
            $result = M('ActivityGroupList')->data($data)->add();

            if (!$result) {
                return $this->setLogicInfo('该团已完成或已失效！', false);
            }
            return $result;
        } else {
            //开团
            //团号
            $group_sn = get_vc(8, 2);
            //查询出成团限制时间
            $group_hour = M("ActivityGroupService")->where(['id' => $request['group_service_id']])->getField('join_time_limit');
            $data = [
                'group_sn' => $group_sn,
                'm_id' => $request['m_id'],
                'group_service_id' => $request['group_service_id'],
                'is_head' => 1,
                'create_time' => NOW_TIME,
                'end_time' => empty($request['group_list_id']) ? NOW_TIME + ($group_hour * 3600) : 0,
                'group_list_id' => empty($request['group_list_id']) ? 0 : $request['group_list_id'],
                'status' => 0,
            ];

            $group_list_id = M('ActivityGroupList')->data($data)->add();

            if (!$group_list_id) {
                return $this->setLogicInfo('开团失败！', false);
            }
            return $group_list_id;
        }
    }

}