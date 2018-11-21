<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/20
 * Time: 19:57
 */

namespace Bms\Controller;


class ServiceCategoryController extends BmsBaseController
{
    /**
     * 添加时关联数据
     */
    function getAddRelation() {
        $this->assign('select',D('ServiceCategory','Logic')->getSelect('parent_id',I('get.id')));
    }
    /**
     * 修改时关联数据
     */
    function getUpdateRelation() {
        $this->assign('select',D('ServiceCategory','Logic')->getSelect('parent_id',I('get.parent_id')));
    }
}