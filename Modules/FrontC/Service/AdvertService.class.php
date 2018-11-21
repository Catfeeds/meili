<?php

namespace FrontC\Service;

/**
 * Class AdvertService
 * @package FrontC\Service
 * 广告数据服务层
 */
class AdvertService extends FrontBaseService
{
    /**
     * [index首页调用、商城首页调用]
     * @param array $request
     * @return array
     * 获取广告列表 投放时间范围内的
     */
    function getAdvert($request)
    {
        $position = $request['position'];
        //获取缓存数据
//        $list = S('Advert_Cache' . $position);
        //不存在缓存 查找数据库
//        if ($list) {
            $list = D('FrontC/Advert')->getList();
            //计入缓存
//            S('Advert_Cache' . $position, $list);
//        }
//        $goods_cate_id  = empty($request['goods_cate_id']) ? 0 : $request['goods_cate_id'];
        //获取满足条件数据 投放时间范围内的
        foreach ($list as $ad) {
            //位置与投放时间
//            if($ad['position'] == $position && $ad['goods_cate_id'] == $goods_cate_id && $ad['start_time'] < NOW_TIME && $ad['end_time'] > NOW_TIME) {
            if ($ad['position'] == $position) {
                //网址跳转
                //$ad['link_url'] = $this->_getLinkUrl($ad['target_rule'], $ad['param']);
                unset($ad['position']);
                unset($ad['start_time']);
                unset($ad['end_time']);
                unset($ad['goods_cate_id']);
                $result[] = $ad;
            }
        }
        if(empty($result)){
            return array();
        }
        return $result;
    }

    /**
     * @param int $target_rule
     * @param string $param
     * @return string
     * 获取网址跳转链接
     */
    private function _getLinkUrl($target_rule = 0, $param = '')
    {
        if (empty($target_rule))
            return '';
        switch ($target_rule) {
            case 1:
                return C('NOW_HOST') . 'Goods/detail/goods_id/' . $param;
                break;
            case 2:
                return C('NOW_HOST') . 'Article/art/art_id/' . $param;
                break;
            case 3:
                return $param;
                break;
            default :
                return '';
                break;
        }
    }
}