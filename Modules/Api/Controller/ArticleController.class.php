<?php
namespace Api\Controller;

/**
 * Class ArticleController
 * @package Api\Controller
 * 文章控制器
 */
class ArticleController extends ApiBaseController {

    /**
     * 获取文章列表
     * 详细描述：
     * 特别注意：
     * POST参数：art_cate_id(分类ID) sort(1--最新发布 2--浏览最多 3--收藏次数降序 4--收藏次数升序)
     */
    function getArticles() {
        //文章信息
        $result = D('FrontC/Article', 'Logic')->getArticles(I('request.'));
        if($result === false)
            api_response('error', D('FrontC/Article', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 获取文章分类列表
     * 详细描述：
     * 特别注意：
     * POST参数：
     */
    function getCates() {
        //文章分类信息
        $result = D('FrontC/Article', 'Service')->getCate();
        if($result === false)
            api_response('error', D('FrontC/Article', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 获取文章详情
     * 详细描述：
     * 特别注意：
     * POST参数：m_id(用户ID) art_id(文章ID) flag(文章标记 about--关于我们)
     */
    function artInfo() {
        //文章信息
        $result = D('FrontC/Article', 'Logic')->artInfo(I('request.'));
        if($result === false)
            api_response('error', D('FrontC/Article', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 用户收藏
     * 详细描述：根据is_coll 判断是收藏还是取消收藏
     * 特别注意：
     * POST参数：*m_id(用户ID) *art_id(文章ID) *is_coll(是否收藏)
     */
    function artCollection() {
        //验证登陆
        $this->checkLogin();
        $result = D('FrontC/Article', 'Logic')->artCollection(I('request.'));
        if(!$result)
            api_response('error', D('FrontC/Article', 'Logic')->getLogicInfo());
        else
            api_response('success', D('FrontC/Article', 'Logic')->getLogicInfo());
    }
}