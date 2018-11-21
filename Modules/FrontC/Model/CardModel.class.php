<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/10
 * Time: 17:17
 */

namespace FrontC\Model;


class CardModel extends FrontBaseModel
{
    /**
     * [service获取一卡通品项调用]
     * 一卡通获取品项
     * */
    public function getSer($request = array())
    {
        $arr_ids =M('CardService')
            ->where(array('card_id'=>$request['card_id']))
            ->field('service_id')
            ->select();
        $result=array_column($arr_ids,'service_id');
        if(empty($result)){
            return array();
        }else{
            return $result;
        }
    }

    /**
     * [service获取一卡通详情调用]
     * */
    public function getCard($parm=array())
    {
        $card = $this
            ->where($parm)
            ->field('id card_id,card_name,font_color,m_price,m_count,y_price,y_count,sales,start_time,end_time,cover')
            ->find();
        $cover=M('File')->field('abs_url cover')->find($card['cover']);
        $card['cover_url']=$cover['cover'];
        if(empty($card)){
            return array();
        }else{
            return $card;
        }
    }
}