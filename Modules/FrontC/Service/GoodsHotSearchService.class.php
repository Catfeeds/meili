<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/13
 * Time: 11:11
 */

namespace FrontC\Service;


class GoodsHotSearchService extends FrontBaseService
{
    /**
     * 获取热门搜索
     * */
    public function getHotSearch()
    {
        $keywords=M('GoodsHotSearch')->where(array('is_hot'=>1))->field('keywords')->select();
        if(empty($keywords))
            return array();
        return $keywords;
  }
}