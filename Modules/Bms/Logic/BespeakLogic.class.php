<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/13
 * Time: 15:47
 */

namespace Bms\Logic;


class BespeakLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //手机号筛选
        if(!empty($request['mobile']))
            $param['where']['bespeak.mobile'] = array('LIKE', '%' . $request['mobile'] . '%');
        //状态筛选
        if(!empty($request['status']))
            $param['where']['bespeak.status'] = array('LIKE', '%' . $request['status'] . '%');
        //排序条件
        $param['order'] = 'bespeak.bespeak_time DESC';
        //返回数据
        $bespeaks=D('Bespeak')->getList($param);
        return $bespeaks;
    }
    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['bespeak.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        $row = D('Bespeak')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //返回数据
        return $row;
    }
}