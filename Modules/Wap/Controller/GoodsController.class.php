<?php
namespace Wap\Controller;

/**
 * Class GoodsController
 * @package Wap\Controller
 * 商品模块控制器
 */
class GoodsController extends WapBaseController {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
    }

    /**
     * 商品列表页面控制
     */
    function goodsList() {
        cookie('__forward__', U('' . CONTROLLER_NAME . '/' . ACTION_NAME . '', $_REQUEST));
        if(!empty($_REQUEST['flag'])) {
            $this->display('goodsList_1');
        } elseif(isset($_REQUEST['keywords'])) {
            $history = cookie('__history__');
            if (!in_array($_REQUEST['keywords'], $history))
                $history[] = $_REQUEST['keywords'];
            cookie('__history__', $history);
            $this->display('goodsList_3');
        } else {
            $result = D('FrontC/Goods', 'Logic')->getGoodsCateList(I('request.'));
            //var_dump($result);
            $this->assign('cates', $result);
            $this->display('goodsList_2');
        }
    }

    /**
     * 商品列表接口
     * 详细描述：
     * 特别注意：
     * POST参数：goods_cate_id(商品分类ID) keywords(关键字) *p(页号)
     */
    function getGoodsList() {
        //首页栏目
        if(!empty($_REQUEST['flag'])) {
            $_REQUEST['goods_cate_id'] = $this->_getID(I('request.flag'));
        }
        $result = D('FrontC/Goods', 'Logic')->getGoodsList(I('request.'));
        if(empty($result))
            $this->error('无结果');
        $this->success('', '', true, $result);
    }
    private function _getID($flag) {
        switch($flag) {
            case 'a' : return 1; break;
            case 'b' : return 5; break;
            default :  return 0; break;
        }
    }

    function search() {
        $list = M('GoodsHotSearch')->where(array('is_hot'=>1))->field('keywords')->select();
        $this->assign('hots', $list);
        $this->assign('history', cookie('__history__'));
        $this->display('search');
    }

    function clearHistory() {
        cookie('__history__', null);
        $this->success('清除成功！');
    }

    /**
     * 商品详情
     * 详细描述：
     * 特别注意：
     * POST参数：m_id(用户ID) *goods_id(商品ID)
     */
    function detail() {
        cookie('__forward__', U('' . CONTROLLER_NAME . '/' . ACTION_NAME . '', $_REQUEST));
        $result = D('FrontC/Goods', 'Logic')->getGoodsDetail(I('request.'));
        if($result === false)
            redirect(U('System/error404'));
        $this->assign('goods', $result);

        $ticket = api('WeChat/getSign');
        $this->assign('noncestr',$ticket['noncestr']);
        $this->assign('timestamp',$ticket['timestamp']);
        $this->assign('signature',$ticket['sign']);

        $this->display('detail');
    }

    function comments() {
        //获取总评数量
        $count = D('FrontC/GoodsComment', 'Service')->getLevelCount(I('request.goods_id'));
        $this->assign('count', $count);
        $this->display('comments');
    }

    /**
     * 商品评价列表接口
     * 详细描述：
     * 特别注意：
     * POST参数：*goods_id(商品ID)
     */
    function getGoodsComments() {
        $result = D('FrontC/GoodsComment', 'Service')->getGoodsComments(I('request.goods_id'));
        if(empty($result))
            $this->error(D('FrontC/GoodsComment', 'Service')->getServiceInfo());
        //var_dump(array('list'=>$result,'count'=>$count));
        $this->success('', '', true, $result);
    }

    /**
     * 选择商品属性后调用 获取商品货品的库存和货品价格
     * 详细描述：
     * 特别注意：
     * POST参数：*goods_id(商品ID) *goods_attr_ids(商品属性ID串 |隔开 注意ID排序)
     */
    function getStockPrice() {
        $result = D('FrontC/Goods', 'Service')->getStockPrice(I('request.goods_id'), I('request.goods_attr_ids'));
        if($result === false)
            $this->error(D('FrontC/Goods', 'Service')->getServiceInfo());
        //var_dump(array('list'=>$result,'count'=>$count));
        $this->success('', '', true, $result);
    }

    /**
     * 用户收藏
     * 详细描述：根据is_coll 判断是收藏还是取消收藏
     * 特别注意：
     * POST参数：*m_id(用户ID) *goods_id(商品ID) *is_coll(是否收藏)
     */
    function goodsCollection() {
        //验证登陆
        $this->checkLogin();
        $result = D('FrontC/Goods', 'Logic')->goodsCollection(I('request.'));
        if(!$result)
            $this->error(D('FrontC/Goods', 'Logic')->getLogicInfo());
        else
            $this->success(D('FrontC/Goods', 'Logic')->getLogicInfo());
    }
}