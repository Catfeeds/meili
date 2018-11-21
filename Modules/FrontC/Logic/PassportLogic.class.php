<?php

namespace FrontC\Logic;

/**
 * Class PassportLogic
 * @package FrontC\Logic
 * 登陆注册 处理逻辑层
 */
class PassportLogic extends FrontBaseLogic
{

    /**
     * [小程序登陆注册调用、]
     * 微信小程序登录
     * 详细描述：
     * 特别注意：
     * POST参数：*code--临时登录凭证 *head--微信头像--files *nickname--昵称
     */
    function autoLogin($request = array())
    {
        //字符编码
        // header('Content-type:text/html; charset=utf-8');
        //判断凭证是否为空
        if (empty($request['code']))
            return $this->setLogicInfo('请先登录微信！', false);
        //开发者服务器使用 临时登录凭证code 获取 session_key 和 openid 等
        $login = file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=wx52cab2cbe8233c2c&secret=34cf56a89988406c194020582270f631&js_code=" . $request['code'] . "&grant_type=authorization_code");
        //转换为数组
        $login_data = json_decode($login, true);
        //接口请求 数据失败
        if (!$login_data['openid'])
            return $this->setLogicInfo('请重新请求code！', false);
        //判断账号是否禁用 有数据且状态为0
        if (M('Member')->where(array('openid' => $login_data['openid']))->getField('status') === 0)
            return $this->setLogicInfo('您的账号已禁用，如有疑问请联系管理员！', false);
        //根据openid 查找用户表 有则更新 没有就添加
        $m_info = M('Member')->where(array('openid' => $login_data['openid']))->find();
        //用户信息
        $member_data = array(
            'login' => array('exp', '`login`+1'),
            'last_login_ip' => get_client_ip(1),//登录IP
            'update_time' => NOW_TIME, // 更新时间
        );
        //默认 0-为注册登陆 1 --登陆
//        $m_infos_status = 1;
        //更新时  ID
        if ($m_info) {
            $member_data['id'] = $m_info['id'];//id
            $member_data['last_login_time'] = NOW_TIME;// 登录时间
            $result = M('Member')->save($member_data);
        }
        //新用户 头像和昵称
        if (!$m_info) {
//            $m_infos_status = 0;
            $member_data['create_time'] = NOW_TIME;//  创建时间
            $member_data['openid'] = $login_data['openid'];//  openid
            $member_data['register_ip'] = get_client_ip(1);// 注册IP
            $member_data['nickname'] = 'meili' . range(999, 9999); //昵称
            $member_data['head'] = C('FILE_HOST') . C('DEFAULT_HEAD');  //头像
            $result = M('Member')->data($member_data)->add();
//            call_procedure('_generate_code_', array($result, 'm'));//生成唯一标识
        }
        // 增加或更新
        if ($result) {
            //登录返回
            $m_infos = M('Member')->where(array('account' => $login_data['openid']))->field('id m_id,nickname,head,mobile')->find();
            return $m_infos;
        } else {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
    }

    /**
     * @param array $request
     * @return array|bool
     * 用户登陆
     */
    function doLogin($request = array())
    {
        //判断是账号密码登陆  还是 第三方登陆
        if (empty($request['openid'])) {
            //账号密码判空
            if (empty($request['account']) || empty($request['password']))
                return $this->setLogicInfo('参数错误！', false);
            //查询用户信息
            $where['account'] = $request['account'];
            $where['password'] = MD5($request['password']);
            $m_info = M('Member')->where($where)->field('id m_id,account,nickname,head')->find();
            //判断是否存在信息
            if (!$m_info)
                return $this->setLogicInfo('账号或密码错误！', false);
        } else {
            //查询绑定表
            $where['openid'] = $request['openid'];
            $conn = M('Interconnect')->where($where)->field('m_id')->find();
            //是否存在绑定信息
            if (!$conn)
                return array('is_bind' => strval(0));
            //获取用户信息
            $m_info = M('Member')->where(array('id' => $conn['m_id']))->field('id m_id,account,nickname,head')->find();
            //加入已绑定状态
            $m_info = array_merge($m_info, array('is_bind' => strval(1)));
        }

        //更新登录信息
        $this->_updLoginInfo($m_info['m_id']);
        //如果是网页则设置session
        if (TERMINAL == 'wap') {
            $this->_setSession($m_info['m_id']);
        }
        //处理用户信息
        $this->_infoFactory($m_info);
        //返回用户信息
        return $m_info;
    }

    /**
     * @param array $m_info
     * @return array
     * 用户信息加工
     */
    private function _infoFactory(&$m_info = array())
    {
        //获取头像信息
        $m_info['head'] = D('FrontC/Member', 'Service')->getHead($m_info['head']);
        //处理手机号中间4位
        $m_info['account_format'] = D('FrontC/Member', 'Service')->accountFormat($m_info['account']);
    }

    /**
     * @param int $m_id
     * 更新登陆信息
     */
    private function _updLoginInfo($m_id = 0)
    {
        //更新登录信息
        $data = array(
            'login' => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
        );
        M('Member')->where(array('id' => $m_id))->data($data)->save();
    }

    /**
     * @param $m_id
     * 设置session
     */
    private function _setSession($m_id)
    {
        /* 记录登录SESSION和COOKIES */
        $session = array(
            'm_id' => $m_id,
        );
        session('member', $session);
        session('member_sign', data_auth_sign($session));
    }

    /**
     * [app注册调用、]
     * @param array $request
     * @return array|bool
     * 用户注册
     */
    function doRegister($request = array())
    {
        //参数判空
        if (empty($request['account']) || empty($request['password']) || empty($request['re_password']) || empty($request['verify']))
            return $this->setLogicInfo('参数错误！', false);
        //验证验证码
        $result = api('Verify/checkVerify', array($request['account'], $request['verify'], 'register'));
        if ($result !== true) {
            return $this->setLogicInfo($result, false);
        }
        //判断账号格式
        if (!preg_match(C('MOBILE'), $request['account']))
            return $this->setLogicInfo('账号格式不正确！', false);
        //判断账号是否注册过
        if (M('Member')->where(array('account' => $request['account']))->count())
            return $this->setLogicInfo('该账号已经注册过！', false);
        //验证密码
        if (!$this->_passwordCheck($request['password'], $request['re_password']))
            return false;
        //如果存在邀请码，验证邀请码是否存在
        if (!empty($request['code'])) {
            $user_id = M('Member')->where(array('member_sn' => $request['code']))->getField('id');
            if (!$user_id)
                return $this->setLogicInfo('邀请码不存在！', false);
        }
        //用户数据准备
        $data['account'] = $request['account'];
        $data['mobile'] = $request['account'];
        $data['password'] = MD5($request['password']);
        $data['nickname'] = D('FrontC/Member', 'Service')->accountFormat($request['account']);
        $data['head'] = C('FILE_HOST') . C('DEFAULT_HEAD');
        $data['register_ip'] = get_client_ip(1);
        $data['create_time'] = NOW_TIME;
        $data['update_time'] = NOW_TIME;
        //注册用户
        $result = M('Member')->data($data)->add();
        if (!$result)
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        //存在openid 记录互联登录绑定信息
        if (!empty($request['openid'])) {
            $this->_interconnect($result, $request['openid'], $request['platform']);
        }
        //存在邀请码
        if ($user_id)
            $this->_invite($result, $user_id, $request['code']);

        //todoo 用户编码添加
//        call_procedure('_generate_code_', array($result, 'm'));
        //生成二维码并保存
//        $member_sn = M('Member')->where(array('id'=>$result))->getField('member_sn');
//        vendor('phpQrcode.phpqrcode');
//        \QRcode::png($member_sn,'./Uploads/Member/Code/'.MD5($member_sn).'.png',QR_ECLEVEL_L,10,1,true);

        //todoo 开通了注册赠送优惠券，则赠送优惠券
        //D('FrontC/Finance', 'Service')->giveCoupon($result, 'register');
        //todoo 发一条站内信问候
//        api('Send/sendMsg',array(['receiver'=>$result,'unique_code'=>'register_notify','replaces'=>array()]));
        //如果是网页则设置session
        if (TERMINAL == 'wap') {
            $this->_setSession($result);
        }
        //清楚互联信息
        //cookie('__interconnect__', null);
        //返回用户信息
        return array('m_id' => $result, 'account' => $request['account'], 'account_format' => $data['nickname'], 'nickname' => $data['nickname'], 'head' => D('FrontC/Member', 'Service')->getHead());
    }

    /**
     * @param int $m_id
     * @param string $openid
     * @param int $platform
     * 互联绑定
     */
    private function _interconnect($m_id = 0, $openid = '', $platform = 0)
    {
        $conn_data['m_id'] = $m_id;
        $conn_data['openid'] = $openid;
        $conn_data['platform'] = $platform;
        M('Interconnect')->data($conn_data)->add();
    }

    /**
     * @param int $other_id
     * @param int $user_id
     * @param string $code
     * @return bool
     * 邀请处理
     */
    private function _invite($other_id = 0, $user_id = 0, $code = '')
    {
        $data['user_id'] = $user_id;
        $data['other_id'] = $other_id;
        $data['code'] = $code;
        $data['create_time'] = NOW_TIME;
        M('InviteLog')->data($data)->add();
        return true;
    }

    /**
     * @param array $request
     * @return array|bool
     * 找回密码
     */
    function doFindPass($request = array())
    {
        //参数判空
        if (empty($request['account']) || empty($request['password']) || empty($request['re_password']) || empty($request['verify'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //验证验证码
        $result = api('Verify/checkVerify', array($request['account'], $request['verify'], 'retrieve'));
        if ($result !== true) {
            return $this->setLogicInfo($result, false);
        }
        //判断账号格式
        if (!preg_match(C('MOBILE'), $request['account'])) {
            return $this->setLogicInfo('账号格式不正确！', false);
        }
        //判断账号是否注册过
        if (!M('Member')->where(array('account' => $request['account']))->count()) {
            return $this->setLogicInfo('该账号不存在！', false);
        }
        //验证密码
        if (!$this->_passwordCheck($request['password'], $request['re_password'])) {
            return false;
        }

        $where['account'] = $request['account'];
        $data['password'] = MD5($request['password']);
        $data['update_time'] = NOW_TIME;

        if (!M('Member')->where($where)->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('密码已重置，请重新登陆！', true);
    }

    /**
     * @param string $password
     * @param string $re_password
     * @return bool|string
     * 密码长度  一致性验证
     */
    private function _passwordCheck($password = '', $re_password = '')
    {
        //判断密码长度
        if (strlen($password) < 6 || strlen($password) > 18) {
            return $this->setLogicInfo('密码长度在6-18位之间！', false);
        }
        if (strlen($re_password) < 6 || strlen($re_password) > 18) {
            return $this->setLogicInfo('确认密码长度在6-18位之间！', false);
        }
        if ($password != $re_password) {
            return $this->setLogicInfo('确认密码和密码不一致！', false);
        }
        return true;
    }
}