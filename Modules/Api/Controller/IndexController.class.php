<?php

namespace Api\Controller;

/**
 * Class IndexController
 * @package Api\Controller
 * 首页控制器
 */
class IndexController extends ApiBaseController
{
    /**
     * 统一修改fle表文件绝对路径地址
     */
    public function updateFileAbs()
    {
        $data = M('File')->field('id,path')->select();
        foreach ($data as $key => $value) {
            M('File')->data(array('id' => $value['id'], 'abs_url' => C('FILE_HOST') . $value['path']))->save();
        }
    }

    /**
     * 首页接口
     * POST参数：m_id
     */
    public function index()
    {
        //首页轮播图position=2
        $adverts = D('FrontC/Advert', 'Logic')->getAdvert(array('position' => 2));
        //栏目
        $channels = D('FrontC/Custom', 'Service')->getChannel();
        //版块
        $sections = D('FrontC/Custom', 'Service')->getSection(array('position' => 2));
        //团购商品
        $groups = D('Api/ActivityGroup','Controller')->goodsListC();
        //套餐
        $package = D('FrontC/Package', 'Service')->getPackage();
        //一卡通
        $card = D('FrontC/Card', 'Service')->getCard();
        //组装数据
        $result = array(
            'adverts' => $adverts,
            'channels' => $channels,
            'sections' => $sections,
            'groups' => $groups,
            'packages' => $package,
            'card' => $card,
        );
        if(empty($result))
            $result=array();
        api_response('success', '', $result);
    }

}