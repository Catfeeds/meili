<?php
namespace Bms\Logic;

/**
 * Class AuthGroupLogic
 * @package Bms\Logic
 * 分组权限 逻辑层
 */
class AuthGroupLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取分组列表
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'id DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //查询的字段
        $param['field']     = 'id,title,description,status';
        //返回数据
        return D('AuthGroup')->getList($param);
    }

    /**
     * @param array $request
     * @return bool
     * 删除分组前操作 验证是否该分组下存在管理员
     */
    protected function beforeRemove($request = array()) {
        //判断该分组下是否存在管理员
        $where['group_id'] = $request['ids'];
        $count = D('Administrator')->where($where)->count();
        if($count > 0) {
            $this->setLogicInfo('该分组下存在管理员，请先删除该分组下的全部管理员！'); return false;
        }
        if(api('Recycle/recovery', array($request, 'AuthGroup', 'title'))) {
            return true;
        }
        $this->setLogicInfo('删除失败！');  return false;
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理data数据 转化规则ID数组为字符串
     */
    protected function processData($data = array(), $request = array()) {
        //转化规则ID数组为字符串
        $data['rules'] = !empty($data['rules']) ? implode(',', $data['rules']) : '';
        return $data;
    }

    /**
     * @param array $request
     * @return array
     * 获取权限规则列表
     */
    function getAccess($request = array()) {
        //根据ID 获取组的权限ID rules
        $rules = D('AuthGroup')->where(array('id'=>$request['id']))->getField('rules');
        //是否存在缓存
        $list  = S('AuthRule_Cache');
        if(!$list) {
            //获取所有权限规则
            $list = D('AuthRule')->field('id,name,title,parent_id')->where(array('status'=>1))->select();
            //处理成树状结构
            $list = list_to_tree($list);
            S('AuthRule_Cache',$list);
        }
        return array('all_rules' => $list, 'have_rules' => explode(',',$rules));
    }
}