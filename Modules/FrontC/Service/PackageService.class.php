<?php
/**
 * Created by PhpStorm.
 * Date: 2018/8/28
 * Time: 15:58
 */

namespace FrontC\Service;


class PackageService extends FrontBaseService
{
    /*
     * [index首页调用]
     * 获取商城首页套餐
     * */
    public function getPackage($request = array())
    {
        $packages = M('Package')->alias('package')
            ->field('package.id package_id,package.package_name,package.price,package.market_price,package.package_short_desc,package.sales,package.cover,file.abs_url cover')
            ->where(array('package.status' => 1))
            ->order(array('package.id asc'))
            ->limit(3)
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = package.cover',
            ))
            ->select();
        if(empty($packages))
            return array();
        return $packages;
    }

    /*
      * 套餐详情
      * */
    public function getRow($parm = array())
    {
        $package = M('Package')->alias('package')
            ->field('package.id package_id,package_desc,package.package_name,package.price,package.market_price,package.package_short_desc,package.sales,package.cover,file.abs_url cover')
            ->where($parm)
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = package.cover',
            ))
            ->find();
        if(empty($package))
            return array();
        return $package;
    }

    /*
     * [service获取套餐列表调用]
     * 获取套餐列表
     * */
    public function getPackages()
    {
        $packages = M('Package')->alias('package')
            ->field('package.id package_id,package.package_name,package.price,package.market_price,package.package_short_desc,package.sales,package.cover,file.abs_url cover')
            ->where(array('package.status' => 1))
            ->order(array('package.id asc'))
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = package.cover',
            ))
            ->select();
        $Page = new \Think\Page(count($packages), 9, $_REQUEST);
        $result = array_slice($packages, $Page->firstRow, $Page->listRows);
        if (empty($result)) {
            return array();
        } else {
            return $result;
        }
    }
}