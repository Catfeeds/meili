<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/26
 * Time: 17:16
 */

namespace Bms\Model;


class BespeakTimeModel extends BmsBaseModel
{
    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '请输入时间段名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('max_count', 'require', '请输入该时间段最多预约人数限制！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('start_time', 'require', '请选择开始时间（时分即可）！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('end_time', 'require', '请选择结束时间（时分即可）！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
     * @param array $param
     * @return array
     * 获取列表 公用方法
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('bespeaktime')->where($param['where'])->count();
        //获取分类对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //数据列表
        $list  = $this->alias('bespeaktime')
            ->where($param['where'])
            ->field('bespeaktime.id,bespeaktime.max_count,bespeaktime.name,bespeaktime.create_time,bespeaktime.update_time,bespeaktime.start_time,bespeaktime.end_time,bespeaktime.status')
            ->select();
        return array('list' => $list, 'page' => $Page->show());
    }
    /**
     * @param array $param
     * @return mixed|void
     */
    function findRow($param = array()) {
        return $this->alias('bespeaktime')
            ->field('bespeaktime.id,bespeaktime.max_count,bespeaktime.name,bespeaktime.start_time,bespeaktime.end_time')
            ->where($param['where'])
            ->find();
    }
}