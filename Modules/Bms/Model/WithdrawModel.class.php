<?php
namespace Bms\Model;

/**
 * Class WithdrawModel
 * @package Bms\Model
 * 提现管理数据模型
 */
class WithdrawModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('trade_no', 'require', '请填写转账流水号！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('remark', 'require', '请填写操作备注！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total  = $this->alias('wd')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'wd');
        //获取数据
        $list   = $this->alias('wd')
            ->field('wd.id,wd.amounts,wd.bank_short,wd.bank_name,wd.bank_logo,wd.mobile,wd.open_name,wd.card_number,wd.card_type,wd.create_time,
            wd.complete_time,wd.status,wd.trade_no,m.account m_account,admin.account admin_account')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = wd.user_id',
                'LEFT JOIN ' . C('DB_PREFIX') . 'administrator admin ON admin.id = wd.admin_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }

    /**
     * @param array $param
     * @return array|mixed
     *
     */
    function findRow($param = array()) {
        return $this->alias('wd')
            ->field('wd.*,m.account m_account,admin.account admin_account')
            ->where($param['where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = wd.user_id',
                'LEFT JOIN ' . C('DB_PREFIX') . 'administrator admin ON admin.id = wd.admin_id',
            ))
            ->find();
    }
}