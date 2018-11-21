<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/21
 * Time: 9:53
 */

namespace Bms\Model;


class ActivitiesModel extends BmsBaseModel
{
    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('title', 'require', '请输入活动标题！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('picture', 'require', '请输入活动内容！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('target_rule', 'require', '请输入跳转规则！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('param', 'require', '请输入跳转参数！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '请输入活动内容！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        $total  = $this->alias('activities')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'activities');
        //获取数据
        $list  = $this->alias('activities')
            ->field('activities.id,activities.update_time,activities.title,activities.status,activities.param,activities.target_rule,activities.create_time,file.abs_url picture')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = activities.picture',
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
        return $this->alias('activities')
            ->field('activities.id,activities.title,activities.status,activities.param,activities.target_rule,activities.picture,activities.sort,activities.content')
            ->where($param['where'])
            ->join(array())
            ->find();
    }
}