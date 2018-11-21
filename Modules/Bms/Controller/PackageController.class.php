<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/21
 * Time: 9:40
 */

namespace Bms\Controller;


class PackageController extends BmsBaseController
{
    /*
     * 关联服务商品
     * */
    function getUpdateRelation()
    {
        //获取该套餐的ID 通过ID获取该套餐下的所有服务商品ID
        $row = $this->get('row');
        $service_ids=M('PackageService')->where(array('package_id'=>$row['id']))->field('service_id')->select();
        $service_ids_c=array_column($service_ids,'service_id');
        $this->assign('s_ids', $service_ids_c);
        $param=array('service.status'=>1);
        $this->assign('services', D('Service', 'Model')->getSer($param));
        //获取套餐商品数量
        $this->assign('number', D('Package', 'Model')->getNumber());
    }
    /*
     * 关联服务商品
     * */
    function getAddRelation() {
        $param=array('service.status'=>1);
        $this->assign('services', D('Service','Model')->getSer($param));
    }
}