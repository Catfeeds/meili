<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/6
 * Time: 10:59
 */

namespace FrontC\Service;


class CardService extends FrontBaseService
{
    /*
     * [index首页调用]
     * 获取一卡通列表
     * */
    public function getCard()
    {
        //获取缓存中数据
        $list = S('Card_Cache');
        //不存在缓存 查找数据库
        if (!$list) {
            $list = M('Card')->alias('card')
                ->field('card.id card_id,card.card_name,card.m_price,card.m_count,card.y_price,card.y_count,card.sales,card.font_color,card.cover,file.abs_url cover')
                ->where(array('card.status' => 1))
                ->join(array(
                    'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = card.cover',
                ))
                ->select();
            //存入缓存
            S('Card_Cache', $list);
        }
        return $list;
    }
}