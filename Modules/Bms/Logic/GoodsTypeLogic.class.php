<?php
namespace Bms\Logic;

/**
 * Class GoodsTypeLogic
 * @package Bms\Logic
 * 商品类型逻辑层
 */
class GoodsTypeLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'id DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //返回数据
        return D('GoodsType')->getList($param);
    }

    /**
     * @param $result
     * @param array $request
     * @return boolean
     * 更新后执行
     */
    protected function afterUpdate($result = 0, $request = array()) {
        header('Content-type:text/html; charset=utf-8');
        //是否有分组提交
        if(empty($request['type_group']))
            return true;
        //类型ID 判断是添加还是修改
        $type_id = empty($request['id']) ? $result : $request['id'];
        //提交的分组
        $group_arr = explode(',', $request['type_group']);
        //查询分组
        $type_groups = M('GoodsTypeGroup')->where(array('type_id'=>$type_id))->field('group_name')->select();
        //
        foreach($type_groups as $value) {
            //查询到的分组 不存在提交的分组中 则删除
            if(!in_array($value['group_name'], $group_arr)) {
                M('GoodsTypeGroup')->where(array('group_name'=>$value['group_name']))->delete();
            } else {
                //存在的提出 提交的分组数组  不参与插入
                $key = array_search($value['group_name'], $group_arr);
                if($key !== false)
                    array_splice($group_arr, $key, 1);
            }
        }
        //无插入内容
        if(empty($group_arr))
            return true;
        //插入新的数据
        foreach($group_arr as $value) {
            $data[] = array(
                'type_id'    => $type_id,
                'group_name' => $value,
            );
        }
        M('GoodsTypeGroup')->addAll($data);

        return true;
    }
}