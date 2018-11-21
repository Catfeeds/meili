<?php
namespace Bms\Model;

/**
 * Class InviteLogModel
 * @package Bms\Model
 */
class InviteLogModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array ();

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array();

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total  = $this->alias('invite')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'invite');
        //获取数据
        $list   = $this->alias('invite')
            ->field('invite.id,invite.code,invite.create_time,m1.account user_account,m1.nickname user_nickname,m2.account other_account,m2.nickname other_nickname')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m1 ON m1.id = invite.user_id',
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m2 ON m2.id = invite.other_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}