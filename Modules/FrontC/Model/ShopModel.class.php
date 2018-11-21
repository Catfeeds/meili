<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/10
 * Time: 17:21
 */

namespace FrontC\Model;


class ShopModel extends FrontBaseModel
{
    /**
     * [service获取指定城市店铺调用]
     * */
    public function getShop($parm=array())
    {
        $shop = $this->alias('shop')
            ->field('shop.id shop_id,shop.name,shop.address,file.abs_url logo')
            ->where($parm)
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = shop.logo',
            ))
            ->select();
        if(empty($shop)){
            return array();
        }else{
            return $shop;
        }
  }


    public function getRow($parm=array())
    {
        $shop = $this
            ->where($parm)
            ->field('id shop_id,name,address')
            ->find();
        if(empty($shop)){
            return array();
        }else{
            return $shop;
        }
    }
}