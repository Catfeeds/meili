<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/12
 * Time: 11:21
 */

namespace Bms\Model;


class ActivityGroupServiceModel extends BmsBaseModel
{
    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('service_id', 'require', '请选择服务商品', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('group_price', 'require', '请填写团购价格', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('people_limit', 'require', '请填写团购人数', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('join_time_limit', 'require', '请填写团购时间限制', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('group_start_time', 'require', '请填写团购开始时间', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('group_end_time', 'require', '请选择团购结束时间', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array (
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
        $total  = $this->alias('groupser')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'groupser');
        //获取数据
        $list  = $this->alias('groupser')
            ->field('groupser.id,groupser.service_id,groupser.people_limit,groupser.group_start_time,groupser.group_end_time,groupser.join_time_limit,groupser.create_time,groupser.update_time,groupser.status,groupser.group_price')
            ->where($param['special_where'])
            ->select();
        //获取该商品图片
        foreach ($list as $k=>$v){
            $ser_info=M('Service')->where(array('id'=>$v['service_id']))->field('cover,price')->find();
            $cover_path=M('File')->field('abs_url cover')->find($ser_info['cover']);
            $v['cover']=$cover_path['cover'];
            $v['price']=$ser_info['price'];
            $result[]=$v;
        }
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $result), 'page'=>$Page->show());
    }
}