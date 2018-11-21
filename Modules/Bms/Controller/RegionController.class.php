<?php
namespace Bms\Controller;

/**
 * Class RegionController
 * @package Bms\Controller
 * 开通地区 控制器
 * 逻辑层、服务层、数据层 在Msc模块存放   将与Region模块共用
 */
class RegionController extends BmsBaseController {

    //TODO 待完成
    public function sAdd($id)
    {
        //通过城市ID添加到shop_region 注意：验证上级省市是否开通
        $info_one=M('Region')->find($id);
        $info_two=M('Region')->find($info_one['parent_id']);
        $info_three=M('Region')->find($info_two['parent_id']);
        $count_three=M('ShopRegion')->where(array('id'=>$info_three['id']))->count();
        $count_two=M('ShopRegion')->where(array('id'=>$info_two['id']))->count();
        $count_one=M('ShopRegion')->where(array('id'=>$info_one['id']))->count();
        if($count_three <= 0){
           //添加省级数据
            M('ShopRegion')->data($info_three)->add();
        }
        if($count_two <= 0){
            //添加市级数据
            M('ShopRegion')->data($info_two)->add();
        }
        if($count_one <= 0){
            //添加区县级数据
            $result_one=M('ShopRegion')->data($info_one)->add();
        }
        if($result_one){
            $this->success('开通成功！','/ShopRegion/index');
        }else{
            $this->error('系统繁忙','/ShopRegion/index');
        }
    }

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
//    protected function _initialize() {
//        //执行 父类_initialize()的方法体
//        parent::_initialize();
//        //逻辑层对象
//        self::$logicObject = D('MsC/Region', 'Logic');
//    }

    /**
     * 获取地区列表
     */
//    function getRegions() {
//        $param['where']['parent_id'] = I('request.reg_id');
//        $this->ajaxReturn(array('status'=>1,'info'=>'','data'=>D('Region','Service')->select($param)), JSON);
//    }
}