<?php
namespace ShopBms\Logic;

/**
 * Class SectionLogic
 * @package Bms\Logic
 * 首页版块逻辑层
 */
class SectionLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //排序条件
        $param['where'] = array();
        //返回数据
        return D('Section')->getList($param);
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(), $request = array()) {
        //默认排序
        if(empty($request['id']))
            $data['sort'] = 9999;
        // layout布局类型、小版块配置数量对应关系
        $config_count_arr = [1 => 1, 2 => 3, 3 => 3,4=>4];
        // 小版块配置数量
        $config_count = $config_count_arr[$data['layout']];
        // 拼接 record 数据
        $configure = array();
        //小版块数量循环
        for($i = 0; $i < $config_count; $i++) {
            // 验证数据信息 如果展示图位上传报错
            if(empty($request['cover'][$i])) {
                $this->setLogicInfo('第 '.($i+1).' 小版块展示图未上传！'); return false;
            }
            $configure[] = array(
                'cover'         => $request['cover'][$i],
                'target_rule'   => !empty($request['target_rule'][$i]) ? $request['target_rule'][$i] : '',
                'param'         => !empty($request['param'][$i]) ? $request['param'][$i] : '',
            );
        }
        // 转为json数据字符串
        $data['configure'] = json_encode($configure);

        return $data;
    }

    /**
     * @param array $request
     * @return mixed
     * 详情
     */
    function findRow($request = array()) {
        //参数判断
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //获取数据
        $row = D('Section')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //配置处理
        $configures = json_decode($row['configure'], true);
        foreach($configures as &$conf) {
            $conf['cover'] = api('File/getFiles', array($conf['cover']));
        }
        $row['configures'] = $configures;

        return $row;
    }

    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 新增、更新、修改状态、删除后执行
     */
    protected function afterAll($result = 0, $request = array()) {
        //清缓存
        S('Section_Cache1', null);
        S('Section_Cache2', null);
        return true;
    }
}