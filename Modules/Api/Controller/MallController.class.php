<?php
/**
 * Created by PhpStorm.
 * Date: 2018/8/28
 * Time: 10:35
 */

namespace Api\Controller;


class MallController extends ApiBaseController
{
    /**
     * 商城首页
     * POST参数：
     */
    public function index()
    {
        $adverts = D('FrontC/Advert', 'Logic')->getAdvert(array('position' => 1));
        $cates = D('FrontC/Custom', 'Service')->getCateFirst();
        $sections = D('FrontC/Custom', 'Service')->getSection(array('position'=>1));
        $goods = D('FrontC/Goods', 'Service')->getHotGoods();
        $result = array(
            'adverts' => $adverts,
            'cates' => $cates,
            'sections' => $sections,
            'goods' => $goods
        );
        api_response('success', '', $result);
    }
}