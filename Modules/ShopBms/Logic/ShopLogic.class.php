<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/21
 * Time: 15:00
 */

namespace ShopBms\Logic;


class ShopLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['name']))
            $param['where']['shop.name'] = array('LIKE', '%' . $request['name'] . '%');
        //排序条件
        $param['where']['shop.id'] = session('admin.a_id');
        $param['order'] = 'shop.id DESC';
        //返回数据
        $shops=D('Shop')->getList($param);
        return $shops;
    }
    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['shop.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        $row = D('Shop')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取封面文件
        $row['logo'] = api('File/getFiles',array($row['logo']));
        //返回数据
        return $row;
    }
}