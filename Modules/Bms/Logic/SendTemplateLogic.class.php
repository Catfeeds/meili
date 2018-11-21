<?php
namespace Bms\Logic;

/**
 * Class SendTemplateLogic
 * @package Bms\Logic
 * 发信模板 逻辑层
 */
class SendTemplateLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     */
    function getList($request = array()) {
        //排序条件
        $param['order']     = 'id DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //查询的字段
        $param['field']     = 'id,cate,unique_code,description,type,status';
        //返回数据
        return D('SendTemplate')->getList($param);
    }

    /**
     * @return mixed
     * 获取模板信息 自定义模板
     */
    function getSendTemplate() {
        return D('SendTemplate')->where(array('cate'=>2))->field('id,unique_code,description,type,status')->select();
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 模板内容 根据类型判断是否过滤
     */
    protected function processData($data = array(), $request = array()) {
        $data['template'] = $data['type'] == 1 || $data['type'] == 3 || $data['type'] == 4 ? filter_html($_POST['template']) : $_POST['template'];
        return $data;
    }

    /**
     * @param array $request
     * @return boolean
     * 修改状态前执行方法
     */
    protected function beforeSetField($request = array()) {
        if($request['field'] == 'status' && D('SendTemplate')->where(array('id'=>array('IN',$request['ids']),'cate'=>1))->find()) {
            $this->setLogicInfo('查到您的操作对象中存在系统模板，操作禁止！'); return false;
        }
        return true;
    }

    /**
     * @param array $request
     * @return boolean
     * 彻底删除前执行 将数据存入回收站
     */
    protected function beforeRemove($request = array()) {
        if(D('SendTemplate')->where(array('id'=>array('IN',$request['ids']),'cate'=>1))->find()) {
            $this->setLogicInfo('查到您的操作对象中存在系统模板，操作禁止！'); return false;
        }
        //回收站处理
        if(api('Recycle/recovery',array($request, 'SendTemplate', 'unique_code'))) {
            return true;
        }
        $this->setLogicInfo('删除失败！');  return false;
    }

    /**
     * @return array
     */
    function getPushTargetRules() {
        return array(
            '1' => '网址',
            '2' => '商品详情',
            '3' => '文章详情',
            '4' => '分类商品列表',
            '5' => '专题商品列表',
            '6' => '搜索商品列表',
        );
    }
}