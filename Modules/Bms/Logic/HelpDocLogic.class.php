<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/12
 * Time: 20:59
 */

namespace Bms\Logic;


class HelpDocLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['title']))
            $param['where']['helpdoc.title'] = array('LIKE', '%' . $request['title'] . '%');
        //排序条件
        $param['order'] = 'helpdoc.id DESC';
        //返回数据
        $docs=D('HelpDoc')->getList($param);
        return $docs;
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