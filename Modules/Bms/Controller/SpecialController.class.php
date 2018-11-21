<?php
namespace Bms\Controller;

/**
 * Class SpecialController
 * @package Bms\Controller
 * 商品专题控制器
 */
class SpecialController extends BmsBaseController {

    /**
     * 获取专题商品
     */
    function getSpeGoods() {
        //验证权限
        $this->checkRule(self::$rule);
        // 记录当前列表页的cookie
        cookie('__forward__', U('Special/getSpeGoods', $_REQUEST));
        //查询结果
        $result = self::$logicObject->getSpeGoods(I('request.'));
        if($result) {
            $this->assign('page', $result['page']); //分页信息
            $this->assign('list', $result['list']); //数据信息
            $this->assign('result', $result); //所有数据信息
            //获取商品分类
            $this->assign('select', D('GoodsCategory','Logic')->getSelect('goods_cate_id', I('request.goods_cate_id')));
            $this->assign('breadcrumb_nav', L('_LIST_BREADCRUMB_NAV_')); //面包屑导航
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
        $this->display('speGoods');
    }

    /**
     * 添加专题商品
     */
    function addSpeGoods() {
        $result = self::$logicObject->addSpeGoods(I('request.'));
        if($result) {
            $this->success(self::$logicObject->getLogicInfo(), cookie('__forward__'));
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }
}