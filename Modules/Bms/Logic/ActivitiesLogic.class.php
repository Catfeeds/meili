<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/21
 * Time: 9:49
 */

namespace Bms\Logic;


class ActivitiesLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['title']))
            $param['where']['activities.title'] = array('LIKE', '%' . $request['title'] . '%');
        //排序条件
        $param['order'] = 'activities.id DESC';
        //返回数据
        return D('Activities')->getList($param);
    }
    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['activities.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        $row = D('Activities')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取封面文件
        $row['picture'] = api('File/getFiles',array($row['picture']));
        //获取轮播
//        $row['pictures'] = api('File/getFiles',array($row['pictures']));
        //返回数据
        return $row;
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(),$request = array()) {
        $data['content'] = $_POST['content'];
        return $data;
    }

}