<?php
namespace Bms\Logic;

/**
 * Class ActionLogLogic
 * @package Bms\Logic
 * 行为日志 逻辑层
 */
class ActionLogLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取日志列表
     */
    function getList($request = array()) {
        //管理员账号查询条件
        if(!empty($request['account'])) {
            //按管理员账号查询
            $param['where']['act_log.admin_id'] = array('exp', ' IN (SELECT id FROM ' . C('DB_PREFIX') . 'administrator WHERE account=\'' . $request['account'] . '\')');
        }
        //排序条件
        $param['order'] = 'id DESC';
        //返回数据
        return D('ActionLog')->getList($param);
    }

    /**
     * 记录行为日志
     * @param string $unique_code 行为标识
     * @param string $record_model 触发行为的模型名 model
     * @param int $record_id 触发行为的记录id 操作的数据记录的ID
     * @param int $admin_id 执行行为的管理员id
     * @return boolean
     */
    function add($unique_code = '', $record_model = '', $record_id = 0, $admin_id = 0) {
        //参数检查
        if(empty($unique_code) || empty($record_model) || empty($record_id)) {
            $this->setLogicInfo('参数错误！');  return false;
        }
        //查询行为,判断是否正常
        $action = D('Action')->where(array('unique_code'=>$unique_code))->field('id,status')->find();
        //判断行为状态是否可用
        if($action['status'] != 1) {
            $this->setLogicInfo('该行为被禁用或删除！');  return false;
        }
        //插入行为日志
        $data['act_id']         =   $action['id']; //行为ID
        $data['admin_id']       =   empty($admin_id) ? is_login() : $admin_id; //管理员ID
        $data['action_ip']      =   ip2long(get_client_ip()); //IP
        $data['record_model']   =   $record_model;
        $data['record_id']      =   $record_id;
        $data['create_time']    =   NOW_TIME;
        $data['remark']         =   '操作url：' . $_SERVER['REQUEST_URI'];
        //插入行为日志
        D('ActionLog')->data($data)->add();
    }
}