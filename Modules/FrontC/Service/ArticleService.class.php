<?php
namespace FrontC\Service;

/**
 * Class ArticleService
 * @package FrontC\Service
 * 文章数据服务层
 */
class ArticleService extends FrontBaseService {

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     */
    function getArticles($custom_param, $extra = array()) {
        //状态必须为正常状态
        $param['where']['art.status'] = 1;
        //默认分类
        $param['where']['art.art_cate_id'] = array('exp','>2');
        //默认排序
        $param['order'] = 'art.sort DESC,art.id DESC';
        //每页数量
        $param['page_size'] = 8;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/Article')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        foreach($list as &$value) {
            $file = api('File/getFiles', array($value['cover'], array('abs_url')));
            $value['cover'] = $file[0]['abs_url'];
        }
        return $list;
    }

    /**
     * 获取分类列表
     */
    function getCate() {
        //先读取缓存
        $list = S('ArticleCategory_Cache');
        //判断缓存中是否有数据  如果没有数据库中获取
        if(!$list) {
            $list = M('ArticleCategory')->field('id art_cate_id,name')->where(array('parent_id'=>3))->order('sort DESC,id DESC')->select();
            //设置为缓存
            S('ArticleCategory_Cache',$list);
        }
        return $list;
    }

    /**
     * @param int $m_id
     * @param int $art_id
     * @return int
     * 是否收藏过该文章
     */
    function isColl($m_id = 0, $art_id = 0) {
        if(M('ArticleCollection')->where(array('m_id'=>$m_id, 'art_id'=>$art_id))->count())
            return 1;
        return 0;
    }

    /**
     * @param $custom_param
     * @param array $extra
     * @return array
     * 收藏文章记录
     */
    function artColls($custom_param, $extra = array()) {
        //默认排序
        $param['order'] = 'art_coll.id DESC';
        //每页数量
        $param['page_size'] = 8;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/ArticleCollection')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        foreach($list as &$value) {
            $file = api('File/getFiles', array($value['cover'], array('abs_url')));
            $value['cover'] = $file[0]['abs_url'];
        }
        return $list;
    }
}