<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/20
 * Time: 21:01
 */

namespace Bms\Model;


class CardModel extends BmsBaseModel
{
    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('card_name', 'require', '请输入套餐名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//        array('service_ids', 'require', '请选择关联商品！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('m_price', 'require', '请输入季卡价格！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('m_count', 'require', '请输入季卡点数！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('y_price', 'require', '请输入年卡价格！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('y_count', 'require', '请输入年卡点数！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cover', 'require', '请输入封面图！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('bg_cover', 'require', '请输入背景图！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('start_time', 'require', '请选择开始时间！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('end_time', 'require', '请选择结束时间！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('card')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'card');
        //获取数据
        $list  = $this->alias('card')
            ->field('card.id,card.status,card.card_name,card.font_color,card.cover,card.bg_cover,card.count,card.price,card.m_price,card.y_price,card.m_count,card.y_count,
            card.create_time,card.update_time,card.start_time,card.end_time,file.abs_url cover')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = card.cover',
            ))
            ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
    /**
     * @param array $param
     * @return mixed|void
     */
    function findRow($param = array()) {
        return $this->alias('card')
            ->field('card.id,card.status,card.card_name,card.bg_cover,card.font_color,card.cover,card.count,card.price,card.m_price,card.y_price,card.m_count,card.y_count,
            card.create_time,card.update_time,card.start_time,card.end_time,card.cover,card.keywords,card.sort')
            ->where($param['where'])
            ->join(array())
            ->find();
    }
}