<?php
namespace Bms\Logic;

/**
 * Class RechargeCardLogic
 * @package Bms\Logic
 * 充值卡逻辑层
 */
class RechargeCardLogic extends BmsBaseLogic {

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
        $result = D('RechargeCard')->getList($param);
        //处理数据
        foreach($result['list'] as &$value) {
            $value['count'] = M('RechargeCode')->where(array('rec_card_id'=>$value['id']))->count();
        }

        return $result;
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
        $row = D('RechargeCard')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //图标
        $row['bg_picture'] = api('File/getFiles',array($row['bg_picture']));

        return $row;
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(),$request = array()) {
        return $data;
    }

    /**
     * @param array $request
     * @return bool
     * 生成充值码
     */
    public function createCode($request = array()) {
        if(empty($request['rec_card_id'])) {
            $this->setLogicInfo('参数错误！'); return false;
        }
        if(empty($request['number'])) {
            $this->setLogicInfo('请输入生成的数量！'); return false;
        }

        //获取充值卡信息
        $card = M('RechargeCard')->where(array('id'=>$request['rec_card_id']))->field('name,face_value,sales_price,bg_picture')->find();

        for($i = 0; $i < intval($request['number']); $i++) {
            $data[] = array(
                'rec_card_id'   => $request['rec_card_id'],
                'name'          => $card['name'],
                'face_value'    => $card['face_value'],
                'sales_price'   => $card['sales_price'],
                'bg_picture'    => $card['bg_picture'],
                'diff_amounts'  => $card['face_value'] - $card['sales_price'],
                'code'          => com_create_guid(),
                'create_time'   => NOW_TIME,
            );
        }

        if(!M('RechargeCode')->addAll($data)) {
            $this->setLogicInfo('系统繁忙，稍后重试！'); return false;
        }

        //TODO 对接业务系统接口

        $this->setLogicInfo('生成成功！'); return true;
    }
}