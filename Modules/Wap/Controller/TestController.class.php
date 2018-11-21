<?php
namespace Wap\Controller;

/**
 * Class IndexController
 * @package Wap\Controller
 * 首页控制器
 */
class TestController extends WapBaseController {


    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 首页
     */
    public function index() {
        /*//轮播
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
        $result = array(
            'not_read'  => $not_read,
            'adverts_1' => $adverts_1,
            'adverts_2' => $adverts_2[0],
            'articles'  => $articles,
        );
        var_dump($result);*/
        //首页栏目
//        if(!empty($_REQUEST['flag'])) {
//            $_REQUEST['goods_cate_id'] = $this->_getID(I('request.flag'));
//        }
//        $result = D('FrontC/Goods', 'Logic')->getGoodsList(I('request.'));
//        var_dump($result);
//        $result = D('FrontC/Goods', 'Logic')->getGoodsDetail(I('request.'));
//        if($result === false)
//            redirect('/e404');
//        var_dump($result);
        $result = D('FrontC/Article', 'Logic')->artInfo(I('request.'));
        var_dump($result);
    }

	public function test () {
		$replaces = array('ordersn'=>'W14876669008414','amount'=>((string)0.01),'balance'=>((string)0.08));
		echo json_encode($replaces);
	}
}