<?php
namespace Bms\Logic;

/**
 * Class GoodsCommentLogic
 * @package Bms\Logic
 * 商品评价逻辑层
 */
class GoodsCommentLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        //商品ID 查找
        if(!empty($request['goods_id'])) {
            $param['where']['g_comm.goods_id']  = $request['goods_id'];
        }
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['g_comm.m_id']  = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //时间查找
//        if(!empty($request['start_time'])) {
//            $param['where']['m.create_time']  = array('between', strtotime($request['start_time']) . "," . strtotime($request['start_time'] . '23:59'));
//        }
        //排序
        $param['order'] = 'g_comm.id DESC';
        //自定义排序
        if(!empty($request['sort'])) {
            $param['order'] = str_replace(':',' ',$request['sort']);
        }
        //返回数据
        //var_dump(D('GoodsComment')->getList($param));
        $result = D('GoodsComment')->getList($param);

        foreach($result['list'] as &$value) {
            $value['pictures'] = api('File/getFiles',array($value['pictures'],array('id','abs_url')));
        }

        return $result;
    }
}