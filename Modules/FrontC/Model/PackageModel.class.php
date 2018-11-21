<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/12
 * Time: 15:25
 */

namespace FrontC\Model;


class PackageModel extends FrontBaseModel
{
    /**
     * [service套餐详情调用]
     * */
    public function getRow($param=array())
    {
        $package=$this->alias('package')
            ->field('package.id package_id,package.package_name,package.price,package.market_price,package.package_short_desc,package.sales,package.cover,file.abs_url cover')
            ->where($param)
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = package.cover',
            ))
            ->find();
        return $package;
  }

  /**
   * [服务商品确认订单调用、]
   * */
    public function getRowSe($param=array())
    {
        $package=$this->alias('package')
            ->field('package.id,package.package_name name,package.price,package.market_price,package.package_short_desc short_desc,package.sales,package.cover,file.abs_url cover')
            ->where($param)
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = package.cover',
            ))
            ->find();
        return $package;
    }

    public function getRowSeS($param=array())
    {
        $package=$this->alias('package')
            ->field('package.package_name name,package.price,package.market_price,package.package_desc,package.package_short_desc short_desc,package.sales,package.cover,file.abs_url cover')
            ->where($param)
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = package.cover',
            ))
            ->find();
        return $package;
    }

    /**
     * [一卡通提交订单调用]
     * */
    public function getPackageServices($param=array())
    {
        $services=M('PackageService')->alias('packageser')
            ->field('packageser.id,packageser.service_id,packageser.service_name,packageser.price,packageser.number,packageser.cover,file.abs_url cover')
            ->where($param)
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = packageser.cover',
            ))
            ->select();
        return $services;
   }

    /**
      * [首页搜索调用]
      * */
    public function getListSearch($param_p = array())
    {
        $packages = $this->alias('package')
            ->field('package.id,package.package_name name,package.price,package.market_price,package.package_short_desc short_desc,package.sales,file.abs_url cover')
            ->where($param_p)
            ->order(array('package.id asc'))
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = package.cover',
            ))
            ->select();
        if(empty($packages))
            return array();
        foreach ($packages as $k=>$v){
            $v['flag']='2';
            $result[]=$v;
        }
        return $result;
    }
}