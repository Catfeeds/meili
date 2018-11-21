<?php
namespace Bms\Model;

/**
 * Class ArticleModel
 * @package Bms\Model
 * 文章咨询模型
 */
class ArticleModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('title', 'require', '文章标题未填写', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '0,90', '标题长度在0--90位', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('art_cate_id', 'require', '文章分类未选择', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '文章内容不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('link', '/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', '连接地址非法', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
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
        $total  = $this->alias('art')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //关联条件
        $join   = array(
            'LEFT JOIN '.C('DB_PREFIX').'article_category art_cate ON art.art_cate_id = art_cate.id',
        );
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'art', !empty($_REQUEST['title']) ? $join : array());
        //获取数据
        $list  = $this->alias('art')
                      ->field('art.id,art.title,art.art_cate_id,art.sort,art.view,art.collections,art.status,art.update_time,art.is_best,
                      art_cate.name cate_name,art_cate.status cate_status,art.is_about_us')
                      ->where($param['special_where'])
                      ->join($join)
                      ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}