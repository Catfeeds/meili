<?php
namespace Bms\Logic;

/**
 * Class ArticleLogic
 * @package Bms\Logic
 * 文章咨询 逻辑处理
 */
class ArticleLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取文章列表
     */
    function getList($request = array()) {
        //标题模糊查询
        if(!empty($request['title'])) {
            $param['where']['art.title'] = array('LIKE','%'.$request['title'].'%');
        }
        //分类查询
        if(!empty($request['art_cate_id'])) {
            $param['where']['art.art_cate_id'] = $request['art_cate_id'];
        }
        //排序
        $param['order'] = 'art.id DESC';
        //返回数据
        return D('Article')->getList($param);
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     */
    protected function processData($data = array(), $request = array()) {
        if(empty($data['id'])) {
            //作者
            $data['admin_id'] = AID;
        }
        //替换 被特殊处理的文章内容
        $data['content'] = $_POST['content'];
        return $data;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['art.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //别名
        $param['alias'] = 'art';
        //查询的字段
        $param['field'] = 'art.*,art_cate.name cate_name';
        //关联表
        $param['join']  = array('LEFT JOIN '.C('DB_PREFIX').'article_category art_cate ON art_cate.id = art.art_cate_id',);

        $row = D('Article')->findRow($param);

        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取封面文件
        $row['cover'] = api('File/getFiles',array($row['cover']));
        //获取轮播
        //$row['pictures'] = api('File/getFiles',array($row['pictures']));
        //获取关联商品信息
//        if(!empty($row['relation_goods'])) {
//            $row['goods_list'] = sort_by_array(explode(',', $row['relation_goods']), D('Goods')->getRelationGoods($row['relation_goods']));
//            if(cookie('__relation_goods_ids__') == null)
//                cookie('__relation_goods_ids__', $row['relation_goods'] . ',');
//        }
        //返回数据
        return $row;
    }

    /**
     * @param string $field_name 隐藏文本框name名称
     * @param string $index_value 默认选中的值
     * @param string $index_field 默认选中字段
     * @return string
     * 获取分类树状下拉菜单
     */
    function getSelect($field_name = '', $index_value = '', $index_field = 'id') {
        //状态条件
        $param['where']['status'] = 1;
        //查询的字段
        $param['field'] = 'id,parent_id,name';
        //获取数据
        $result = D('ArticleCategory')->getList($param);
        //返回处理树状下拉后数据
        return api('Category/getSelect', array($result, $field_name, $index_value, $index_field));
    }

    /**
     * @param array $request
     * @return boolean
     * 彻底删除前执行 将数据存入回收站
     */
    protected function beforeRemove($request = array()) {
        if(api('Recycle/recovery',array($request,'Article','title'))) {
            return true;
        }
        $this->setLogicInfo('删除失败！');  return false;
    }

    /**
     * @param array $request
     * @return bool
     * 文章移动
     */
    function move($request = array()) {
        //判断参数
        if(empty($request['ids'])) {
            $this->setLogicInfo('未选择要移动的文章！'); return false;
        }
        if(empty($request['art_cate_id'])) {
            $this->setLogicInfo('未选择目标分类！'); return false;
        }
        //创建修改参数
        $where['id']     = array('IN', $request['ids']);
        $data['art_cate_id'] = $request['art_cate_id'];
        //更新数据
        $result = D('Article')->where($where)->data($data)->save();
        if(!$result) {
            $this->setLogicInfo('移动出错！'); return false;
        }
        $this->setLogicInfo('移动成功'); return true;
    }
}