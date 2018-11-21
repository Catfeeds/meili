<?php
namespace Bms\Controller;

/**
 * Class StatisticsController
 * @package Bms\Controller
 * 统计控制器
 */
class StatisticsController extends BmsBaseController {

    /***
     * @var array
     * 日期参数
     */
    protected $dateParam = array();

    /**
     * 初始化执行
     */
    public function _initialize() {
        parent::_initialize();
        //日期参数
        $this->dateParam = api('Statistics/setDateParam',array(I('request.')));
        //总数统计
        //$this->assign('total_stat', D('Statistics','Logic')->totalStat());
    }

    /**
     * 用户相关统计
     */
    function members() {
        //每天用户数量线状统计
        $this->assign('day_users_quantity_line',D('Statistics','Logic')->usersQuantityLine(I('request.'), $this->dateParam));
        //支付方式比例饼状图
        //$this->assign('payment_pie', D('Statistics', 'Logic')->paymentPie(I('request.')));

        $this->assign('breadcrumb_nav', L('_MEM_BREADCRUMB_NAV_'));
        $this->display('members');
    }
}