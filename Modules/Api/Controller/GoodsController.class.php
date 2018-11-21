<?php

namespace Api\Controller;

/**
 * Class GoodsController
 * @package Api\Controller
 * 商品模块控制器
 */
class GoodsController extends ApiBaseController
{

    /**
     * [mall商城获取分类商品调用]
     * 获取指定一级分类及分类下的商品
     * POST参数: cate_id-分类ID 第一次进来默认获取第一个分类下的商品 无须传category_id 若需要查其他分类 则需要再传一个category_id参数
     * category_id是cate_id的子分类
     * p-页码
     * */
    public function getCateGoods()
    {
        $result = D('FrontC/Goods', 'Logic')->getCateGoods(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }
    /**
     * 获取热门商品
     * m_id
     * */
    public function getHotGoods()
    {
        $result = D('FrontC/Goods', 'Logic')->getHotGoods(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 商品详情
     * POST参数：goods_id商品ID
     * */
    public function goodsDetail()
    {
        $result = D('FrontC/Goods', 'Logic')->goodsDetail(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 商品列表接口
     * 详细描述：
     * 特别注意：
     * POST参数：goods_cate_id(商品分类ID) keywords(关键字) *p(页号)
     */
    function goodsList()
    {
        //首页栏目
        if (!empty($_REQUEST['flag'])) {
            $_REQUEST['goods_cate_id'] = $this->_getID(I('request.flag'));
        }
        $result = D('FrontC/Goods', 'Logic')->getGoodsList(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    private function _getID($flag)
    {
        switch ($flag) {
            case 'a' :
                return 1;
                break;
            case 'b' :
                return 5;
                break;
            default :
                return 0;
                break;
        }
    }

    /**
     * 专题商品列表接口
     * 详细描述：
     * 特别注意：
     * POST参数：*spe_id(专题ID) *p(页号)
     */
    function speGoodsList()
    {
        $result = D('FrontC/Goods', 'Service')->getSpeGoods(I('request.spe_id'));
        if ($result === false)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 商品分类列表接口
     * 详细描述：
     * 特别注意：
     * POST参数：无
     */
    function getAllGoodsCate()
    {
        $list = D('FrontC/Goods', 'Logic')->getGoodsCateList(I('request.'));
        if ($list === false)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());
        api_response('success', '', $list);
    }

    /**
     * 商品分类列表接口
     * 详细描述：
     * 特别注意：
     * POST参数：goods_cate_id(商品分类ID，查下级分类或同级分类使用) flag(1--查下级，2--查同级)
     */
    function getGoodsCateByID()
    {
        $request = I('request.');
        if (empty($request['goods_cate_id'])) {
            $cate = array('name' => '全部');
            $list = D('FrontC/Goods', 'Logic')->getGoodsCateList(array('goods_cate_id' => -1, 'flag' => 1));
        } else {
            $cate = M('GoodsCategory')->where(array('id' => $request['goods_cate_id']))->field('id,name,level')->find();
            $list = D('FrontC/Goods', 'Logic')->getGoodsCateList(array('goods_cate_id' => $cate['id'], 'flag' => 2));
        }
        if ($list === false)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());

        api_response('success', '', array('list' => $list, 'cate_name' => $cate['name']));
    }

    /**
     * 商品详情
     * 详细描述：
     * 特别注意：
     * POST参数：m_id(用户ID) *goods_id(商品ID)
     */
    function detail()
    {
        $result = D('FrontC/Goods', 'Logic')->getGoodsDetail(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());
        //var_dump($result);
        M('Goods')->where(array('id' => I('request.goods_id')))->setInc('views');
        api_response('success', '', $result);
    }

    /**
     * 商品评价列表接口
     * 详细描述：
     * 特别注意：
     * POST参数：*goods_id(商品ID)
     */
    function getGoodsComments()
    {
        $result = D('FrontC/GoodsComment', 'Service')->getGoodsComments(I('request.goods_id'));
        if ($result === false)
            api_response('error', D('FrontC/GoodsComment', 'Service')->getServiceInfo());
        //获取总评数量
        $count = D('FrontC/GoodsComment', 'Service')->getLevelCount(I('request.goods_id'));
        //var_dump(array('list'=>$result,'count'=>$count));
        api_response('success', '', array('list' => $result, 'count' => $count));
    }

    /**
     * 选择商品属性后调用 获取商品货品的库存和货品价格
     * 详细描述：
     * 特别注意：
     * POST参数：*goods_id(商品ID) *goods_attr_ids(商品属性ID串 |隔开 注意ID排序)
     */
    function getStockPrice()
    {
        $result = D('FrontC/Goods', 'Service')->getStockPrice(I('request.goods_id'), I('request.goods_attr_ids'));
        if ($result === false)
            api_response('error', D('FrontC/Goods', 'Service')->getServiceInfo());
        //var_dump(array('list'=>$result,'count'=>$count));
        api_response('success', '', $result);
    }

    /**
     * 用户收藏
     * 详细描述：根据is_coll 判断是收藏还是取消收藏
     * 特别注意：
     * POST参数：*m_id(用户ID) *goods_id(商品ID) *is_coll(是否收藏)
     */
    function goodsCollection()
    {
        //验证登陆
        $this->checkLogin();
        $result = D('FrontC/Goods', 'Logic')->goodsCollection(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Goods', 'Logic')->getLogicInfo());
        else
            api_response('success', D('FrontC/Goods', 'Logic')->getLogicInfo());
    }
}