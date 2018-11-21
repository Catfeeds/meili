<?php

namespace FrontC\Logic;

/**
 * Class AdvertLogic
 * @package FrontC\Logic
 * 广告逻辑层
 */
class AdvertLogic extends FrontBaseLogic
{
    /**
     * [index首页调用、商城首页调用]
     * @param array $request
     * @return array|bool
     */
    function getAdvert($request = array())
    {
        //判断参数
        if (empty($request['position'])) {
            $this->setLogicInfo('参数错误！');
            return false;
        }
        //获取广告列表
        $list = D('FrontC/Advert', 'Service')->getAdvert($request);
        //返回数据
        return $list;
    }
}