<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/13
 * Time: 15:49
 */

namespace Bms\Model;


class BespeakModel extends BmsBaseModel
{
    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('bespeak')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'bespeak');
        //获取数据
        $list  = $this->alias('bespeak')
            ->field('bespeak.id,bespeak.status,bespeak.bespeak_sn,bespeak.m_id,bespeak.name,bespeak.mobile,bespeak.shop_id,bespeak.service_name,bespeak.bespeak_time,bespeak.start_time,bespeak.end_time')
            ->where($param['special_where'])
            ->select();
        foreach ($list as $k=>$v){
            $account=M('Member')->where(array('id'=>$v['m_id']))->getField('account');
            $shop_name=M('Shop')->where(array('id'=>$v['shop_id']))->getField('name');
            $v['account']=$account;
            $v['shop_name']=$shop_name;
            $lists[]=$v;
        }
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $lists), 'page'=>$Page->show());
    }
    /**
     * @param array $param
     * @return mixed|void
     */
    function findRow($param = array()) {
        $row= $this->alias('bespeak')
            ->field('bespeak.id,bespeak.status,bespeak.way,bespeak.bespeak_sn,bespeak.m_id,bespeak.name,bespeak.mobile,bespeak.shop_id,bespeak.service_name,bespeak.service_id,bespeak.bespeak_time,bespeak.start_time,bespeak.end_time')
            ->where($param['where'])
            ->join(array())
            ->find();
        $service_info=M('Service')->field('service_name,cover')->find($row['service_id']);
        $cover=M('File')->where(array('id'=>$service_info['cover']))->getField('abs_url');
        $row['cover']=$cover;
        $shop_info=M('Shop')->field('name,logo,mobile')->find($row['shop_id']);
        $logo=M('File')->where(array('id'=>$shop_info['logo']))->getField('abs_url');
        $row['logo']=$logo;
        $row['shop_name']=$shop_info['name'];
        $row['shop_mobile']=$shop_info['mobile'];
        return $row;
    }
}