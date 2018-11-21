<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/18
 * Time: 15:02
 */

namespace FrontC\Model;


class OrderCardModel extends FrontBaseModel
{
    /*
     * 获取用户有效一卡通
     * */
//    public function getCards($param=array())
//    {
//        $cards=$this->alias('ordercard')
//            ->field('ordercard.id card_id,ordercard.shop_id,ordercard.card_name,ordercard.price,ordercard.type,ordercard.count,ordercard.start_time,ordercard.end_time,ordercard.cover,file.abs_url cover')
//            ->where($param)
//            ->join(array(
//                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = ordercard.cover',
//            ))
//            ->select();
//        if(!empty($cards)){
//            return $cards;
//        }else{
//            return array();
//        }
//    }
}