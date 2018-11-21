<?php
namespace FrontC\Logic;

/**
 * Class ArticleLogic
 * @package FrontC\Logic
 * 文章 逻辑层
 */
class ArticleLogic extends FrontBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getArticles($request = array()) {
        //分类查询
        if(!empty($request['art_cate_id'])) {
            $param['where']['art.art_cate_id'] = $request['art_cate_id'];
        }
        //排序
        $param['order'] = $this->_getSort($request['sort']);
        //获取数据
        $result = D('FrontC/Article', 'Service')->getArticles($param, $request);
        //var_dump($result);
        return $result;
    }

    /**
     * @param int $sort
     * @return string
     * 选择排序条件
     */
    private function _getSort($sort = 0) {
        switch($sort) {
            case 1: return 'art.id DESC'; break;
            case 2: return 'art.view DESC'; break;
            case 3: return 'art.collections DESC'; break;
            case 4: return 'art.collections ASC'; break;
            default: return 'art.sort DESC'; break;
        }
    }

    private function _getFlagID($flag) {
        switch($flag) {
            case 'about': return 10; break;
            case 'register_agree': return 9; break;
            case 'recharge_rule': return 25; break;
            case 'ext_rule': return 26; break;
            default: return 0; break;
        }
    }

    /**
     * @param array $request
     * @return array
     * 文章详情
     */
    function artInfo($request = array()) {
        if(empty($request['art_id']) && empty($request['flag'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //查询条件
        $param['where']['art.id'] = $request['art_id'];
        if(!empty($request['flag'])) {
            $param['where']['art.id'] = $this->_getFlagID($request['flag']);
        }
        //查找
        $row = D('FrontC/Article')->findRow($param);
        if(!$row) {
            return $this->setLogicInfo('文章已不存在！', false);
        }
        //轮播为空  设置封面
        //if(empty($row['pictures'])) {$row['pictures'] = $row['cover'];}
        $row['content'] = path2abs($row['content']);
        //$row['pictures'] = D('FrontC/System', 'Service')->getPictures($row['pictures']);
        //$row['pictures'] = api('File/getFiles', array($row['pictures'], array('id', 'abs_url')));
        //是否收藏
        //$row['is_coll'] = D('FrontC/Article', 'Service')->isColl($request['m_id'],$request['art_id']);
        //分享地址
        //$row['share_url'] = 'http://wenf-wap.toocms.com/Article/art/art_id/'.$row['art_id'].'';
        //关联商品获取
        //$row['relation_goods'] = D('FrontC/Goods')->getRelationGoods($row['relation_goods']);
        return $row;
    }

    /**
     * @param array $request
     * @return bool|string
     * 收藏文章
     */
    function artCollection($request = array()) {
        //参数验证
        if(empty($request['m_id']) || empty($request['art_id']))
            return $this->setLogicInfo('参数错误！', false);
        //is_coll=1则是已收藏  要取消收藏操作
        if($request['is_coll'] == 1) {
            //删除收藏记录
            if(!M('ArticleCollection')->where(array('m_id'=>$request['m_id'], 'art_id'=>$request['art_id']))->delete())
                return $this->setLogicInfo('系统繁忙，稍后重试！', false);
            else
                return $this->setLogicInfo('取消收藏成功！', true);
        }
        //是否已收藏
        if(M('ArticleCollection')->where(array('m_id'=>$request['m_id'], 'art_id'=>$request['art_id']))->count()) {
            return $this->setLogicInfo('收藏成功！', true);
        }
        //添加收藏
        $data['m_id']           = $request['m_id'];
        $data['art_id']        = $request['art_id'];
        $data['create_time']    = time();
        //添加记录
        if(!m('ArticleCollection')->data($data)->add()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        } else {
            return $this->setLogicInfo('收藏成功！', true);
        }
    }
}
