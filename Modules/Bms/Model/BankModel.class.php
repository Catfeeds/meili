<?php
namespace Bms\Model;

/**
 * Class BankModel
 * @package Bms\Model
 * 银行信息 数据层
 */
class BankModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('name', 'require', '请输入银行名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('reason', '1,15', '原因在15个字符以内！', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('short', 'require', '请输入银行简称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('logo', 'require', '请上传银行LOGO！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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
        $total  = $this->alias('bank')->where($param['where'])->count();
        //获取分类对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //数据列表
        $list  = $this->alias('bank')
            ->where($param['where'])
            ->field('bank.id,bank.name,bank.short,bank.update_time,bank.status,file.abs_url')
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = bank.logo',
            ))
            ->order('id DESC')
            ->limit($Page->firstRow, $Page->listRows)
            ->select();

        return array('list' => $list, 'page' => $Page->show());
    }

    function getCacheList() {
        return $this->alias('bank')
            ->where(array('bank.status'=>1))
            ->field('bank.id,bank.name bank_name,bank.short bank_short,file.abs_url bank_logo')
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = bank.logo',
            ))
            ->order('id DESC')
            ->select();
    }
}