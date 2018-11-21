<?php
namespace Wap\Controller;

/**
 * Class IndexController
 * @package Wap\Controller
 * 首页控制器
 */
class IndexController extends WapBaseController {


	public function _initialize(){
        parent::_initialize();
	}

    /**
     * 首页
     */
    public function index() {
        cookie('__forward__', U('' . CONTROLLER_NAME . '/' . ACTION_NAME . '', $_REQUEST));
        //轮播
        $adverts_1 = D('FrontC/Advert', 'Logic')->getAdvert(array('position'=>1));
        //中间广告
        $adverts_2 = D('FrontC/Advert', 'Logic')->getAdvert(array('position'=>2));
        //是否有未读消息
        if(!empty($_REQUEST['m_id']))
            $not_read = M('SiteMessage')->where(array('m_id'=>I('request.m_id'),'status'=>0))->count();
        else
            $not_read = 0;
        //推荐文章列表
        $param['where']['is_best'] = 1;
        $articles = D('FrontC/Article', 'Service')->getArticles($param);
        //频道列表
        $channels = D('FrontC/Channel', 'Service')->getChannels(I('request.'));
        $result = array(
            'not_read'  => $not_read,
            'adverts_1' => $adverts_1,
            'adverts_2' => $adverts_2[0],
            'articles'  => $articles,
            'channels'  => $channels,
        );
        $this->assign('result', $result);
        $this->display('index');
    }
}