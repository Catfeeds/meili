<?php
namespace FrontC\Model;

/**
 * Class InviteLogModel
 * @package FrontC\Model
 * 邀请记录
 */
class InviteLogModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    function getList($param = array()) {
        //内关联条件
        $join = array(
            'INNER JOIN ' . C('DB_PREFIX') . 'member m ON m.id = invite.other_id',
        );
        //数据总数
        $total  = $this->alias('invite')->where($param['where'])->join($join)->count();
        //创建分页对象
        $Page   = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, 'invite', $join);
        //获取数据
        $list   = $this->alias('invite')
            ->field('invite.id invite_id,m.head,m.nickname')
            ->where($param['special_where'])
            ->join($join)
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list, 'invite_id'), 'page'=>$Page->show(), 'total'=>$total);
    }
}