<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/21
 * Time: 14:59
 */

namespace ShopBms\Controller;


class ShopController extends BmsBaseController
{
    function getAddRelation() {
        $this->assign('regions',D('ShopRegion','Logic')->getRegions());
    }
    function getUpdateRelation() {
        $this->assign('regions',D('ShopRegion','Logic')->getRegions());
    }
}