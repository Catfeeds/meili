<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/20
 * Time: 17:48
 */

namespace Bms\Model;


class ServiceModel extends BmsBaseModel
{
    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('service_name', 'require', '请输入商品名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('service_short_desc', 'require', '请输入商品名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('service_sn', 'require', '请输入商品货号！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('service_sn', '/^[a-zA-Z0-9]\w{0,39}$/', '商品货号必须是英文数字！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('service_sn', 'checkUnique', '该货号已经存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('service_sn')),
//        array('goods_cate_id', 'require', '请选择商品分类！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('count', 'require', '请输入卡消耗点数！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('count', '/^(0|[1-9][0-9]*|-[1-9][0-9]*)$/', '点数必须是数字整数！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('price', 'require', '请输入商品售价！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('market_price', 'require', '请输入商品原价！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cover', 'require', '请上传商品封面图！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('service_desc', 'require', '请输入商品图文描述！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        $total  = $this->alias('service')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'service');
        //获取数据
        $list  = $this->alias('service')
            ->field('service.id,service.service_name,service.service_sn,service.count,service.cover,service.price,service.service_short_desc,service.sort,service.status,service.update_time,
            service_cate.name service_cate_name')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'service_category service_cate ON service_cate.id = service.service_cate_id',
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
        return $this->alias('service')
            ->field('service.id,service.service_name,service.count,service.service_sn,service.keywords,service.service_cate_id,service.market_price,service.service_desc,service.cover,service.price,service.service_short_desc,service.sort,service.status,service.update_time')
            ->where($param['where'])
            ->join(array())
            ->find();
    }

    /*
     * [团购商品关联调用、]
    /*
     * */
    public function getSer($param=array())
    {
        $service = $this->alias('service')
            ->field('service.id service_id,service.service_name,service.price,service.market_price,service.service_cate_id,service.sales,service.service_short_desc,file.abs_url cover')
            ->where($param)
            ->order(array('service.id desc'))
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = service.cover',
            ))
            ->select();
        if(empty($service))
            return array();
        return $service;
    }
}