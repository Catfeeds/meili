<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/12
 * Time: 21:02
 */

namespace Bms\Model;


class HelpDocModel extends BmsBaseModel
{

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('title', 'require', '请输入标题！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '请输入内容！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        $total  = $this->alias('helpdoc')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'helpdoc');
        //获取数据
        $list  = $this->alias('helpdoc')
            ->field('helpdoc.id,helpdoc.title,helpdoc.content,helpdoc.create_time,helpdoc.update_time,helpdoc.status')
            ->where($param['special_where'])
            ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}