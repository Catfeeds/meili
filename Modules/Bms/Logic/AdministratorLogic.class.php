<?php
namespace Bms\Logic;

/**
 * Class AdministratorLogic
 * @package Bms\Logic
 * 管理员逻辑处理类
 */
class AdministratorLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return mixed
     */
    function getList($request = array()) {
        //按管理员账号查询
        if(!empty($request['account'])) {
            $param['where']['account'] = $request['account'];
        }
        //排序条件
        $param['order']     = 'create_time DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //查询的字段
        $param['field']     = 'id,account,login,group_name,group_status,last_login_time,last_login_ip,status';
        //返回数据
        return D('AdministratorView')->getList($param);
    }

    /**
     * @param array $request
     * @return mixed
     * 获取管理员分组列表
     */
    function getGroupList($request = array()) {
        //查询状态条件
        $param['where']['status']   = 1;
        //查询的字段
        $param['field']             = 'id,title';
        //返回数据
        return D('AuthGroup')->getList($param);
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理data数据 密码MD5加密 创建唯一标示
     */
    protected function processData($data = array(),$request = array()) {
        //添加管理员的情况 密码MD5处理
        if(empty($data['id'])) {
            $data['password'] = MD5(MD5($data['password']));
        }
        return $data;
    }

    /**
     * @param $result
     * @param array $request
     * @return boolean
     * 更新后执行
     */
    protected function afterUpdate($result, $request = array()) {
        //添加管理员之后 生成唯一标示
        if(empty($request['id'])) {
            call_procedure('_generate_code_', array($result, 'admin'));
        }
        return true;
    }

    /**
     * @param array $request
     * @return bool
     * 修改管理员状态之前 验证是否超级管理员
     */
    protected function beforeSetField($request = array()) {
        //在修改状态前 判断是否是对超级管理员的操作 如果是则禁止
        if($request['field'] == 'status' && $request['ids'] == C('USER_ADMINISTRATOR')) {
            $this->setLogicInfo('不允许对超级管理员进行此操作！'); return false;
        }
        return true;
    }

    /**
     * @param array $request
     * @return boolean
     * 彻底删除前执行 将数据存入回收站
     */
    protected function beforeRemove($request = array()) {
        //删除之前判断 是否删除的是超级管理员
        if($request['ids'] == C('USER_ADMINISTRATOR')) {
            $this->setLogicInfo('不允许对超级管理员进行此操作！'); return false;
        }
        //回收站处理
        if(api('Recycle/recovery',array($request,'Administrator','account'))) {
            return true;
        }
        $this->setLogicInfo('删除失败！');  return false;
    }

    /**
     * @param array $request
     * @return bool
     * 登录函数
     */
    function login($request = array()) {
        if(empty($request['account'])) {
            $this->setLogicInfo('请输入登录账号！'); return false;
        }
        if(empty($request['password'])) {
            $this->setLogicInfo('请输入登录密码！'); return false;
        }
        //检测验证码
        if(!check_verify($request['verify'])) {
            $this->setLogicInfo('验证码输入错误！'); return false;
        }
        //调用存储过程
        $admin  = call_procedure('_administrator_login_', array($request['account'], MD5(MD5($request['password']))));
        
        if($admin) {
			//判断该账号是否正常
			if($admin['status'] != 1) {
				$this->setLogicInfo('登录失败，您的账号已不可用！'); return false;
			}
			//判断组是否被禁用或删除
			if($admin['group_status'] != 1) {
				$this->setLogicInfo('登录失败，分组已删除或被禁用！'); return false;
			}
			
			//添加日志
            D('ActionLog', 'Logic')->add('login', 'Administrator', $admin['id'], $admin['id']);
            //登录成功 修改登录信息 设置session
            $this->autoCZ($admin);

            $this->setLogicInfo('登陆成功！'); return true;
        } else {
            //登录失败
            $this->setLogicInfo('账号或密码错误！'); return false;
        }
    }

    /**
     * @param $admin
     * 更新登录信息
     * 设置session
     */
    private function autoCZ($admin) {
        /* 更新登录信息 */
        $data = array(
            'model'           => 'Administrator',
            'id'              => $admin['id'],
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->update($data);

        /* 记录登录SESSION和COOKIES */
        $session = array(
            'a_id'            => $admin['id'],
            'account'         => $admin['account'],
            'last_login_time' => $admin['last_login_time'],
        );
        session('admin', $session);
        session('admin_sign', data_auth_sign($session));
    }

    /**
     * @param array $request
     * @return bool|mixed
     * 修改密码
     */
    function rePass($request = array()) {
        //判断参数
        if(empty($request['old_password'])) {
            $this->setLogicInfo('请输入原密码！'); return false;
        } if(empty($request['new_password'])) {
            $this->setLogicInfo('请输入新密码！'); return false;
        } if(strlen($request['new_password']) < 6 || strlen($request['new_password']) > 18) {
            $this->setLogicInfo('新密码长度在6--18位之间！'); return false;
        } if($request['re_new_password'] != $request['new_password']) {
            $this->setLogicInfo('确认新密码与新密码不一致！'); return false;
        }
        //获取原密码
        $password = D('Administrator')->where(array('id'=>AID))->getField('password');
        //验证原密码是否正确
        if(!($password == MD5(MD5($request['old_password'])))) {
            $this->setLogicInfo('原密码不正确！'); return false;
        }
        //修改
        $data['password']   = MD5(MD5($request['new_password']));
        $where['id']        = AID;
        //判断成败
        if(!D('Administrator')->where($where)->data($data)->save()) {
            $this->setLogicInfo('修改失败！'); return false;
        }
        $this->setLogicInfo('修改成功！'); return true;
    }
}