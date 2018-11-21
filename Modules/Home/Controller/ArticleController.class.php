<?php
namespace Home\Controller;

/**
 * Class CenterController
 * @package Home\Controller
 * 文档
 */
class ArticleController extends HomeBaseController {

    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
    }

    function artList() {
        $cates = D('FrontC/Article', 'Logic')->getCates();

        foreach($cates as &$cate) {
            $cate['arts'] = D('FrontC/Article', 'Logic')->getArts(array('cate_id'=>$cate['cate_id']));
        }

        $this->assign('cates', $cates);

        $this->assign('title', '服务中心');
        $this->display('artList');
    }

    function art() {
        //获取文章
        $art = D('FrontC/Article', 'Logic')->getArt(array('art_id'=>I('request.art_id')));
        //文章列表
        $arts = D('FrontC/Article', 'Logic')->getArts(array('cate_id'=>I('request.cate_id')));
        //var_dump($art);
        $this->assign('art', $art);
        $this->assign('arts', $arts);

        $this->assign('title', '详情');
        $this->display('art');
    }

    function price() {
//设置城市ID
        $_REQUEST['city_id'] = cookie('city_id');
        //该城市服务项目列表
        $items = D('FrontC/RegionItem', 'Logic')->getRegItemL(array_merge(I('request.')));
        $this->assign('type_items', $items);
        //城市开通服务项目ID
        $_REQUEST['reg_item_id']    = empty($_REQUEST['reg_item_id'])   ? $items[0]['list'][0]['reg_item_id'] : $_REQUEST['reg_item_id'];
        //服务项目ID
        $_REQUEST['item_id']        = empty($_REQUEST['item_id'])       ? $items[0]['list'][0]['item_id']     : $_REQUEST['item_id'];
        //判断服务项目ID是否真实
        if(!M('RegionItem')->where(array('id'=>I('request.reg_item_id')))->count() || !M('ServiceItem')->where(array('id'=>I('request.item_id')))->count())
            redirect('/e404');
        //服务产品列表
        $details = D('FrontC/RegionDetails', 'Logic')->getRegDetailL(array_merge(I('request.')));
        $this->assign('cate_details', $details);
        //服务项目信息
        $item = D('FrontC/ServiceItem', 'Logic')->getItemL(array_merge(I('request.')));
        //充值优惠说明
        $item['recharge_rule'] = D('FrontC/System', 'Service')->getRechargeRule();
        $this->assign('current_item', $item);
        /*var_dump($items);
        var_dump($item);
        var_dump($details);*/
        $this->assign('title', '价目中心');
        $this->display('price');
    }

    /**
     * 服务范围
     */
    function range() {
        //获取城市列表
        $this->assign('cities', D('Region','Service')->select(array('where'=>array('parent_id'=>0))));
        //获取当前城市的服务范围
        $where['id'] = empty($_REQUEST['city_id']) ? cookie('city_id') : $_REQUEST['city_id'];
        $this->assign('region', M('Region')->where($where)->field('name,service_area')->find());

        $this->assign('title', '服务范围');
        $this->display('range');
    }
}