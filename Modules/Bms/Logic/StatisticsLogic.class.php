<?php
namespace Bms\Logic;

/**
 * Class StatisticsLogic
 * @package Bms\Logic
 * 相关统计
 */
class StatisticsLogic extends BmsBaseLogic {

    /**
     * 各模块的总数统计
     */
    function totalStat() {
        //各模块总数统计
        $total_stat = S('TotalStat_Cache');
        if(!$total_stat) {
            $total_stat = call_procedure('_total_stat_');
            S('TotalStat_Cache',$total_stat,3600);
        }
        return $total_stat;
    }

    /********************* 线状/柱形 统计 *************************/

    /**
     * @param array $request
     * @param $date_param
     * @return array
     * 用户数量统计
     */
    function usersQuantityLine($request = array(), $date_param = array()){
        //折线图数据 查询条件及对象
        $line_parameter = array(
            array('title'=>'会员数量','where'=>array(),'obj'=>D('Member')),
        );
        return $this->_createLine($date_param,$line_parameter);
    }

    /**
     * @return array
     */
    function aaa() {
        $list = D('Property')->select();
        foreach($list as $v) {
            $field_param[] = array('value'=>$v['id'],'x'=>$v['name']);
        }
        //折线图数据 查询条件及对象
        $line_parameter = array(
            array('title'=>'收益','field'=>'prop_id','where'=>array(),'obj'=>D('CashLog'),'reckon'=>array('SUM','amounts')),
        );
        //获取数据
        $line_data  = api('Statistics/getLineDataByField',array($field_param,$line_parameter));
        $line       = api('Statistics/createLine',array($line_data));
        //横坐标赋值时间
        $x = api('Statistics/createXByField',array($field_param));

        return array('line'=>$line, 'x'=>$x);
    }

    /********************* 饼状统计 *************************/

    /**
     * @param array $request
     * 各支付方式比例饼状图
     * @return array
     */
    function paymentPie($request = array()) {
        //饼状图数据查询条件
        $pie_parameter = array(
            array('title'=>'支付宝支付','where'=>array('payment'=>1),'obj'=>D('PayLog')),
            array('title'=>'微信支付','where'=>array('payment'=>2),'obj'=>D('PayLog')),
            array('title'=>'银联支付','where'=>array('payment'=>3),'obj'=>D('PayLog')),
            array('title'=>'银通卡支付','where'=>array('payment'=>4),'obj'=>D('PayLog')),
            array('title'=>'停车币支付','where'=>array('payment'=>5),'obj'=>D('PayLog')),
        );
        $pie_data = api('Statistics/getPieData',array($pie_parameter,'paymentPie'));
        //创建饼状图
        return api('Statistics/createPie',array($pie_data));
    }

    /**
     * @param $date_param
     * @param $line_parameter
     * @return array
     * 创建线型统计相关数据
     */
    private function _createLine($date_param,$line_parameter) {
        //获取数据
        $line_data  = api('Statistics/getLineDataByDate',array($date_param,$line_parameter));
        $line       = api('Statistics/createLine',array($line_data));
        //横坐标赋值时间
        $x = api('Statistics/createXByDate',array($date_param));

        return array('line'=>$line, 'x'=>$x);
    }
}