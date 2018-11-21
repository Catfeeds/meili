<?php
namespace FrontC\Model;

/**
 * Class ArticleCollectionModel
 * @package FrontC\Model
 * 文章收藏模型
 */
class ArticleCollectionModel extends FrontBaseModel {

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total = $this->alias('art_coll')->where($param['where'])->count();
        //创建分页对象
        $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'art_coll');
        //获取数据
        $list = $this->alias('art_coll')
            ->field('art_coll.id,art_coll.art_id,art.title,art.short_desc,art.cover')
            ->where($param['special_where'])
            ->join(array(
                'INNER JOIN ' . C('DB_PREFIX') . 'article art ON art.id = art_coll.art_id and art.status=1',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list' => sort_by_array($param['ids_for_sort'], $list, 'id'), 'page' => $Page->show());
    }
}