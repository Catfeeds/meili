<?php
namespace Wap\Controller;

/**
 * Class CenterController
 * @package Wap\Controller
 * 文档
 */
class ArticleController extends WapBaseController {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
    }

    /**
     * 页面
     */
    function artList() {
        $result = D('FrontC/Article', 'Service')->getCate();
        $this->assign('cates', $result);
        $this->display('artList');
    }

    /**
     * 获取文章列表
     * 详细描述：
     * 特别注意：
     * POST参数：art_cate_id(分类ID) sort(1--最新发布 2--浏览最多 3--收藏次数降序 4--收藏次数升序)
     */
    function getArticles() {
        //文章信息
        $result = D('FrontC/Article', 'Logic')->getArticles(I('request.'));
        if(empty($result))
            $this->error(D('FrontC/Article', 'Logic')->getLogicInfo());
        $this->success('', '', true, $result);
    }

    /**
     * 获取文章详情
     * 详细描述：
     * 特别注意：
     * POST参数：m_id(用户ID) *art_id(文章ID) flag(文章标记 about--关于我们 itg_rule--积分规则)
     */
    function art() {
        cookie('__forward__', U('' . CONTROLLER_NAME . '/' . ACTION_NAME . '', $_REQUEST));
        //文章信息
        $result = D('FrontC/Article', 'Logic')->artInfo(I('request.'));
        if(empty($result))
            redirect(U('System/error404'));

        $this->assign('art', $result);
        $this->display('art');
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
            $this->error(D('FrontC/Article', 'Logic')->getLogicInfo());
        else
            $this->success(D('FrontC/Article', 'Logic')->getLogicInfo());
    }
}