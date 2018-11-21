<?php
namespace Bms\Controller;
use Think\Controller;

/**
 * Class LoginController
 * @package Bms\Controller
 * 登录控制器
 */
class LoginController extends Controller {

    /**
     * 判断是否登录 进入登录页面
     */
    function login() {
        if(is_login()) {
            $this->redirect('Index/index');
        } else {
            //读取站点配置 先读取缓存
            $config = S('Config_Cache');
            if(!$config) {
                $config = D('Config')->parseList();
                S('Config_Cache',$config);
            }
            //添加配置到 C函数
            C($config);
            $this->display('login');
        }
    }

    /**
     * 执行登录
     */
    function doLogin() {
        $result = D('Administrator','Logic')->login(I('post.'));
        if($result) {
            $this->success(D('Administrator','Logic')->getLogicInfo(),U('Index/index'));
        } else {
            $this->error(D('Administrator','Logic')->getLogicInfo());
        }
    }

    /**
     * 退出登录
     */
    public function logout() {
        if(is_login()){
            session('admin', null);
            session('admin_sign', null);
            session('[destroy]');
            $this->success('退出成功！', U('Login/login'));
        } else {
            $this->redirect(U('Login/login'));
        }
    }

    /**
     * 验证码验证
     */
    public function verify() {
        $config = array(
            'length'    =>  5,    // 验证码长度
            'useCurve'  =>  false,// 是否画混淆曲线
            'useNoise'  =>  true, // 是否添加杂点
        );
        $verify = new \Think\Verify($config);
        $verify->entry(1);
    }
}
