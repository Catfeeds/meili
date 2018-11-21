<?php
namespace Bms\Logic;

/**
 * Class WithdrawLogic
 * @package Bms\Logic
 * 提现管理逻辑层
 */
class WithdrawLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //状态查询
        if(isset($request['status']) && $request['status'] != '') {
            $param['where']['wd.status'] = $request['status'];
        }
        //会员账号查询
        if(!empty($request['account'])) {
            $param['where']['wd.user_id'] = M('Member')->where(array('account'=>$request['account']))->getField('id');
        }
        //排序条件
        $param['order']  = 'wd.status ASC,wd.id DESC';
        //返回数据
        $result = D('Withdraw')->getList($param);

        //统计信息
        $total_1 = M('Withdraw')->where(array('status'=>1))->sum('amounts');
        $total_2 = M('Withdraw')->where(array('status'=>0))->sum('amounts');
        $result['total_1'] = $total_1;
        $result['total_2'] = $total_2;

        return $result;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //参数判断
        if(!empty($request['id'])) {
            $param['where']['wd.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //获取数据
        $row = D('Withdraw')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        return $row;
    }

    /**
     * @param array $request
     * @return bool|mixed
     * 新增 或 修改
     */
    /*function update($request = array()) {
        //执行前操作
        if(!$this->beforeUpdate($request)) { return false; }
        //获取数据对象
        $data = D('Withdraw')->create($request);
        if(!$data) {
            $this->setLogicInfo(D('Withdraw')->getError()); return false;
        }
        //处理数据
        $data = $this->processData($data);
        //开启事务操作
        $model = new \Think\Model();
        $model->startTrans();
        //修改信息
        $result = $model->table(C('DB_PREFIX').'withdraw')->where(array('id'=>$request['id']))->data($data)->save();
        if(!$result) {
            $this->setLogicInfo('操作出错，请重试！'); return false;
        }
        //获取提现用户ID 金额
        $with = D('Withdraw')->where(array('id'=>$request['id']))->field('m_id,amounts')->find();
        //处理冻结金额
        $edit_data = array(
            'freeze_balance' => array('exp', '`freeze_balance`-'.$with['amounts'].''),
        );
        if(!empty($with['m_id'])) {
            $result = $model->table(C('DB_PREFIX') . 'member')->where(array('id' => $with['m_id']))->data($edit_data)->save();
        }
        if(!$result) {
            //回滚
            $model->rollback();
            $this->setLogicInfo('操作出错，请重试！'); return false;
        }
        if(!$result) {
            //回滚
            $model->rollback();
            $this->setLogicInfo('操作出错，请重试！'); return false;
        }
        //提交
        $model->commit();
        $this->setLogicInfo('操作成功！'); return true;
    }*/

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(), $request = array()) {
        //操作者ID
        $data['admin_id']       = AID;
        //修改状态
        $data['status']         = 1;
        //完成时间
        $data['complete_time']  = NOW_TIME;

        return $data;
    }

    //TODO  导出批量转账 格式文件
    //TODO  导入转账后的文件 带有每条转账的流水号
    /**
     * @param array $request
     * @return bool
     * 导出数据excel表格
     */
    function export($request = array()) {
        //TODO 是否标记为已导出   重新导出
        //获取提现列表
        $list = D('Withdraw')->getList(array('where'=>array('wd.status'=>0)));
        //执行导出函数
        $result = api('Excel/exportToExcel',array($request['fields_data'], $list['list'], 'WITHDRAW', array($this, 'setHasExport')));
        //判断成功失败
        if($result === false) {
            $this->setLogicInfo('导出文件出错！'); return false;
        }
        return true;
    }

    function setHasExport($row) {
        //D('Withdraw')->where(array())->save(array('status'=>1));
    }

    /**
     * @param array $request
     * @return bool
     * 导入数据
     */
    function import($request = array()) {
        //判断是否上传了导入文件
        if(empty($request['import_file'])) {
            $this->setLogicInfo('您未上传导入文件！'); return false;
        }
        //获取导入文件中数据
        $data = api('Excel/readExcelToData',array($request['import_file']));
        //文件错误
        if($data === false) {
            $this->setLogicInfo('导入文件格式有误！'); return false;
        }
        //数据为空
        if(empty($data)) {
            $this->setLogicInfo('导入数据为空！'); return false;
        }
        //生成一些其他数据
        foreach($data as $key => $value){

        }

        return true;
    }
}