<?php
namespace ShopBms\Logic;

/**
 * Class MemberLogic
 * @package Bms\Logic
 * 会员管理逻辑层
 */
class MemberLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //ID 查找
        if(!empty($request['id'])) {
            $param['where']['m.id']       = $request['id'];
        }
        //账号 查找
        if(!empty($request['account'])) {
            $param['where']['m.account']  = $request['account'];
        }
        //时间查找
        if(!empty($request['start_time'])) {
            $param['where']['m.create_time']  = array('between', strtotime($request['start_time']) . "," . strtotime($request['start_time'] . '23:59'));
        }
        //排序
        $param['order'] = 'm.id DESC';
        //别名
        $param['alias'] = 'm';
        //自定义排序
        if(!empty($request['sort'])) {
            $param['order'] = str_replace(':',' ',$request['sort']);
        }
        //返回数据
        return D('Member')->getList($param);
    }

    /**
     * @param array $request
     * @return mixed
     * 会员详情
     */
    function findRow($request = array()) {
        //参数判断
        if(!empty($request['id'])) {
            $param['where']['m.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        //别名
        $param['alias'] = 'm';
        //查询的字段
        $param['field'] = 'm.*,file.path head_path';
        //关联查询
        $param['join']  = array(
            'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = m.head',
        );
        //获取数据
        $row = D('Member')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //二维码
        $row['code_url'] = C('FILE_HOST') . 'Uploads/Member/Code/' . MD5($row['member_sn']) .'.png';

        return $row;
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(), $request = array()) {
        unset($data['password'], $data['pay_pass']);
        //如果编辑密码则修改
        if(!empty($request['password'])) {
            $data['password'] = MD5($request['password']);
        }
        //如果编辑密码则修改
        if(!empty($request['pay_pass'])) {
            $data['pay_pass'] = MD5($request['pay_pass']);
        }
        return $data;
    }

    /**
     * @param array $request
     * @return boolean
     * 彻底删除前执行 将数据存入回收站
     */
    protected function beforeRemove($request = array()) {
        if(api('Recycle/recovery',array($request, 'Member', 'nickname'))) {
            return true;
        }
        $this->setLogicInfo('删除失败！');  return false;
    }

    /********************发信时信息获取*********************/
    /**
     * @param array $where_arr
     * @return array
     * 生成查询条件
     */
    function getToUsersWhere($where_arr = array()) {
        $where = array();
        //ID查询
        if(!empty($where_arr['id'])) {
            $where['id'] = $where_arr['id'];
        }
        //账号查询
        if(!empty($where_arr['account'])) {
            $where['account'] = $where_arr['account'];
        }
        return $where;
    }

    /**
     * @param $where
     * @return mixed
     * 获取记录总数
     */
    function getToUsersCount($where = array()) {
        return D('Member')->where($where)->count();
    }

    /**
     * @param $where
     * @param $order
     * @param $first_row
     * @param $page_size
     * @return mixed
     * 获取数据列表
     */
    function getToUsersList($where = array(), $order = '', $first_row = 0, $page_size = 200) {
        return D('Member')->field('id,account,mobile,email,nickname')->where($where)->order($order)->limit($first_row,$page_size)->select();
    }
    /********************发信时信息获取*********************/




    /**
     * @param array $request
     * @return bool
     * 导出数据excel表格
     */
    function export($request = array()) {
        //字段数据
        $fields_data = $request['fields_data'];
        //获取会员列表
        $list = D('Member')->select();
        //转换一些数据格式  例如：时间-字符串 性别-字符串等
        foreach($list as $key => $value) {
            //时间转换
            $list[$key]['create_time']  = date('Y-m-d H:i',$value['create_time']);
            //性别转换
            $list[$key]['sex']          = $value['sex'] == 1 ? '男' : '女';
        }
        //执行导出函数
        $result = api('Excel/exportToExcel',array($fields_data, $list, 'MEMBER'));
        //判断成功失败
        if($result === false) {
            $this->setLogicInfo('导出文件出错！'); return false;
        }
        return true;
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
            $data[$key]['create_time']  = time();                   //创建时间
            $data[$key]['password']     = MD5($value['password']);  //密码加密
        }
        //存入数据库  //TODO 是否要验证重复
        $result = D('Member')->addAll($data);
        if($result) {
            //删除文件记录
            return true;
        } else {
            $this->setLogicInfo('数据导入失败！'); return false;
        }
    }
}