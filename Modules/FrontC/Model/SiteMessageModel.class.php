<?php
namespace FrontC\Model;

/**
 * Class SiteMessageModel
 * @package FrontC\Model
 * 站内消息 数据层
 */
class SiteMessageModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total = $this->alias('site_msg')->where($param['where'])->count();
        //创建分页对象
        $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'site_msg');
        //获取数据
        $list = $this->alias('site_msg')
            ->field('site_msg.id site_msg_id,site_msg.subject,site_msg.content,site_msg.create_time,site_msg.status')
            ->where($param['special_where'])
            /*->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = f_ship.friend_id',
            ))*/
            ->select();
        //返回记录 根据ID顺序排序
        return array('list' => sort_by_array($param['ids_for_sort'], $list, 'site_msg_id'), 'page' => $Page->show());
    }
}