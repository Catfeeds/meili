<?php
namespace FrontC\Service;

/**
 * Class GoodsCommentService
 * @package FrontC\Service
 * 商品评价模块服务层
 */
class GoodsCommentService extends FrontBaseService {

    /**
     * @param int $goods_id
     * @param int $m_id
     * @param int $page_size
     * @return array
     * 获取商品评价列表 、 我的评价
     */
    function getGoodsComments($goods_id = 0, $m_id = 0, $page_size = 8) {
        if(empty($goods_id) && empty($m_id))
            return array();
        //状态必须为正常可见状态
        $param['where']['g_comm.status'] = 1;
        //商品ID
        $param['where']['g_comm.goods_id'] = $goods_id;
        //排序
        $param['order'] = 'g_comm.id DESC';
        //每页数量
        $param['page_size'] = $page_size;
        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param))
            $param = $this->customParam($param, $custom_param);
        //调用数据模型层方法获取数据
        $result = D('FrontC/GoodsComment')->getList($param);
        //数据列表 //分页信息
        $list = $result['list']; $page = $result['page'];
        //如果没有数据返回空数组
        if(empty($list))
            return array();
        //处理列表数据
        array_walk($list, 'FrontC\Service\GoodsCommentService::dataFactory', $extra);

        return $list;
    }

    /**
     * @param $value
     * @param $key
     * @param $extra
     * 数据加工
     */
    public static function dataFactory(&$value, $key, $extra) {
        //头像
        $value['head']     = D('FrontC/Member', 'Service')->getHead($value['head']);
        //评价图
        $value['pictures'] = api('File/getFiles', array($value['pictures'], array('id', 'abs_url')));
        //时间
        $value['create_time'] = fuzzy_date($value['create_time']);
    }

    /**
     * @param int $goods_id
     * @return array
     * 评价 差 中 好评数量
     */
    function getLevelCount($goods_id = 0) {
        $bad_count      = M('GoodsComment')->where(array('goods_id'=>$goods_id,'level'=>1,'status'=>1))->count();
        $better_count   = M('GoodsComment')->where(array('goods_id'=>$goods_id,'level'=>array('IN','2,3,4'),'status'=>1))->count();
        $best_count     = M('GoodsComment')->where(array('goods_id'=>$goods_id,'level'=>5,'status'=>1))->count();
        return array('bad'=>$bad_count,'better'=>$better_count,'best'=>$best_count);
    }
}