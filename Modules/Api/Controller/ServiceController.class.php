<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/4
 * Time: 16:48
 */

namespace Api\Controller;


class ServiceController extends ApiBaseController
{
    /**
     * 获取首页套餐列表
     * POST参数：p-页号
     * */
    public function getPackages()
    {
        $result = D('FrontC/Package', 'Service')->getPackages(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Package', 'Service')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
    * 服务商品详情
    * POST参数：package_id服务商品ID
    * */
    public function packageDetail()
    {
        $result = D('FrontC/Service', 'Logic')->packageDetail(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Service', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 获取指定一级分类及分类下的服务商品
     * POST参数: cate_id-分类ID 第一次进来默认获取第一个分类下的服务商品 无须传category_id 若需要查其他分类 则需要再传一个category_id参数
     * category_id是cate_id的子分类
     * */
    public function getCateService()
    {
        $result = D('FrontC/Service', 'Logic')->getCateService(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Service', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }
    /**
     * 服务商品详情
     * POST参数：service_id服务商品ID
     * */
    public function serviceDetail()
    {
        $result = D('FrontC/Service', 'Logic')->serviceDetail(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Service', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 一卡通详情
     * */
    public function cardDetail()
    {
        $result = D('FrontC/Service', 'Logic')->cardDetail(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Service', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 获取开通实体店的城市
     * */
    public function getCity()
    {
        $result = D('FrontC/Service', 'Logic')->getCity(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Service', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 获取指定地区的实体店
     * POST参数:area_id 区县ID
     * */
    public function getShop()
    {
        $result = D('FrontC/Service', 'Logic')->getShop(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Service', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
    }
}