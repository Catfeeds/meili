<?php
namespace Plugin\Interconnect\Controller;
use Think\Controller;

/**
 * Class LoginController
 * @package Plugin\Interconnect\Controller
 * 互联登录中转控制器
 */
class LoginController extends Controller {

    /**
     * @var string
     * 第三方平台标识名称
     */
    public $platform    = '';

    /**
     * @var string
     * 回调路径 不要删除和改动
     */
    protected $callback = '';

    /**
     * @var array
     * array(app_id,app_key,...) 不要删除和改动
     */
    protected $apps     = array();

    /**
     * 初始化执行
     */
    function _initialize() {
        //平台标识名称
        $this->platform = ucfirst(I('request.platform'));
        //回调路径 加 平台名称 加密后的 app_id app_key  不要删除和改动
        $this->callback = C('INTERCONNECT_CALLBACK') . '&encrypt=' . I('request.encrypt') . '&platform=' . $this->platform;
        //解析后  app_id app_key   不要删除和改动
        $this->apps     = explode('|', think_decrypt(I('request.encrypt')));
    }

    /**
     * 互联登录 中转方法
     */
    function transfer() {
        //识别 跳转
        A('Plugin://Interconnect/' . $this->platform . '')->login($this->apps, $this->callback);
    }

    /**
     * 回调方法
     * 配合 member表  Interconnect互联登录绑定表
     */
    function callback() {
        //识别 跳转
        $info = A('Plugin://Interconnect/' . $this->platform . '')->callback($this->apps, $this->callback);
        //判断获取成功还是失败  $info为数组 则获取成功
        if(!is_array($info)) {
            //获取失败 报错
            $this->error($info);
        }
        //获取成功 合并 平台标识名称
        $info   = array_merge($info, array('platform'=>$this->platform));
        //根据openid 获取用户信息 能查到设置session 跳到当前页  未查到 设置cookie 跳到注册页
        $member = D('FrontC/Passport', 'Logic')->doLogin(array('openid'=>$info['openid']));
        //未绑定设置cookie  跳到注册页
        if($member['is_bind'] == 0) {
            //设置cookie
            cookie('__interconnect__', $info, array('expire'=>1800));
            //跳转到注册页
            if(!IS_AJAX)
                redirect(U('Passport/register', array('step'=>1)));
            else
                $this->success('互联登录成功-1！', U('Passport/register', array('step'=>1)));
        }
        //已绑定直接登录
        //跳转到当前页
        if(!IS_AJAX)
            redirect(cookie('__forward__'));
        else
            $this->success('互联登录成功-2！', cookie('__forward__'));
    }
}