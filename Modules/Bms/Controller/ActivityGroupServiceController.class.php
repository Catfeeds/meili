<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/12
 * Time: 11:20
 */

namespace Bms\Controller;


class ActivityGroupServiceController extends BmsBaseController
{
    /**
     * 获取关联服务商品
     * */
    function getUpdateRelation()
    {
//        $row = $this->get('row');
        $param=array('service.status'=>1);
        $this->assign('services', D('Service', 'Model')->getSer($param));
    }
    /**
     * 获取关联服务商品
     * */
    function getAddRelation() {
        $param=array('service.status'=>1);
        $this->assign('services', D('Service','Model')->getSer($param));
    }
}