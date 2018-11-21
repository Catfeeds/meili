<?php
namespace Bms\Controller;

/**
 * Class ArticleController
 * @package Bms\Controller
 * 文章 控制器
 */
class ArticleController extends BmsBaseController {

    function getIndexRelation() {
        $this->assign('select',D('Article','Logic')->getSelect('art_cate_id',I('request.art_cate_id')));
    }

    function getUpdateRelation() {
        $row = $this->get('row');
        $this->assign('select',D('Article','Logic')->getSelect('art_cate_id',$row['art_cate_id']));
        //cookie('__relation_goods_ids__', null);
    }

    function getAddRelation() {
        $this->assign('select',D('Article','Logic')->getSelect('art_cate_id',I('get.art_cate_id')));
    }

    /**
     * 移动文章
     */
    function move() {
        //权限验证
        $this->checkRule(self::$rule);
        $result = self::$logicObject->move(I('request.'));
        if($result) {
            $this->success(self::$logicObject->getLogicInfo());
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }

    /**
     * 选择关联商品
     */
    function choiceGoods() {
        $ids = cookie('__relation_goods_ids__');
        //判断是否已存在
        if(false === strpos($ids, I('request.goods_id')))
            cookie('__relation_goods_ids__', $ids . I('request.goods_id') . ',', array('expire'=>1800));
        $this->success('操作成功！');
    }

    /**
     * 取消选择关联商品
     */
    function cancelGoods() {
        $ids = cookie('__relation_goods_ids__');
        cookie('__relation_goods_ids__', str_replace(I('request.goods_id') . ',', '', $ids), array('expire'=>1800));
        $this->success('操作成功！');
    }

    function saveRG() {
        if(!isset($_REQUEST['relation_goods']))
            $data['relation_goods'] = substr(cookie('__relation_goods_ids__'), 0, -1);
        else
            $data['relation_goods'] = I('request.relation_goods');
        if(false === M('Article')->where(array('id'=>I('request.art_id')))->data($data)->save())
            $this->error('保存失败！');

        cookie('__relation_goods_ids__', null);
        $this->success('操作成功！', cookie('__forward__'));
    }
}