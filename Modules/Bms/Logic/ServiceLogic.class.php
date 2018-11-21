<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/20
 * Time: 17:43
 */

namespace Bms\Logic;


class ServiceLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['service_name']))
            $param['where']['service.service_name'] = array('LIKE', '%' . $request['service_name'] . '%');
        if(!empty($request['service_cate_id']))
            $param['where']['service.service_cate_id'] = $request['service_cate_id'];
        //排序条件
        $param['order'] = 'service.id DESC';
        //返回数据
        return D('Service')->getList($param);
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['service.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        $row = D('Service')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取封面文件
        $row['cover'] = api('File/getFiles',array($row['cover']));
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
        $data['service_desc'] = $_POST['service_desc'];
        /*preg_match_all('/src=\"\/?(.*?)\"/', $_POST['goods_desc'], $match);
        foreach($match[1] as $key => $src) {
            if(!strpos($src, '://')) {
                $data['goods_desc'] = str_replace('/' . $src, C('FILE_HOST') . '/' . $src . "\" width=100%", $_POST['goods_desc']);
            } else {
                $data['goods_desc'] = str_replace($src . '"', $src . '" width=100%' , $_POST['goods_desc']);
            }
        }*/
        return $data;
    }
}