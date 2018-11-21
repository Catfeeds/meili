<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/21
 * Time: 9:53
 */

namespace Bms\Model;


class PackageModel extends BmsBaseModel
{
    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('package_name', 'require', '请输入套餐名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//        array('service_ids', 'require', '请选择关联商品！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('package_short_desc', 'require', '请输入套餐简述！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('price', 'require', '请输入套餐售价！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('market_price', 'require', '请输入套餐原价！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cover', 'require', '请上传套餐封面图！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('package_desc', 'require', '请输入套餐图文描述！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        $total  = $this->alias('package')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'package');
        //获取数据
        $list  = $this->alias('package')
            ->field('package.id,package.package_name,package.status,package.update_time,package.price,package.market_price,package.package_short_desc,package.create_time,file.abs_url cover')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = package.cover',
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
        return $this->alias('package')
            ->field('package.id,package.keywords,package.cover,package.package_desc,package.sort,package.package_name,package.price,package.market_price,package.package_short_desc,package.create_time')
            ->where($param['where'])
            ->join(array())
            ->find();
    }

    public function getNumber()
    {
        $result=M('PackageService')->field('package_id,service_id,number')->select();
        return $result;
    }
}