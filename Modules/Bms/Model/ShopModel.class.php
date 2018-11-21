<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/21
 * Time: 15:00
 */

namespace Bms\Model;


class ShopModel extends BmsBaseModel
{
    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '请输入店铺名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('short', 'require', '请输入店铺简述！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('account', 'require', '请输入店铺管理者账号！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('account', 'checkUnique', '该账号已经存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('account')),
        array('password', 'require', '请输入店铺管理者密码！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('contacts', 'require', '请输入联系人！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mobile', 'require', '请输入联系电话！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mobile', '/^0?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[037])[0-9]{8}$/', '电话号码格式不正确！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('address', 'require', '请输入详细地址！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('logo', 'require', '请传递Logo！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('area_id', 'require', '请选择关联城市！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        $total  = $this->alias('shop')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'shop');
        //获取数据
        $list  = $this->alias('shop')
            ->field('shop.id,shop.name,shop.contacts,shop.status,shop.mobile,shop.address,shop.short,shop.area_id,shop.create_time,shop.update_time,file.abs_url logo')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = shop.logo',
            ))
            ->select();
        //获取该店铺省市区
        foreach ($list as $k=>$v){
            $adr=M('Region')->field('merger_name')->find($v['area_id']);
            $v['all_address']=$adr['merger_name'];
            $lists[]=$v;
        }
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $lists), 'page'=>$Page->show());
    }
    /**
     * @param array $param
     * @return mixed|void
     */
    function findRow($param = array()) {
        return $this->alias('shop')
            ->field('shop.id,shop.name,shop.contacts,shop.status,shop.account,shop.password,shop.mobile,shop.area_id,shop.address,shop.short,shop.area_id,shop.create_time,shop.update_time,shop.logo,shop.sort,shop.keywords')
            ->where($param['where'])
            ->join(array())
            ->find();
    }
}