<?php
namespace Plugin\Interconnect\Controller;

/**
 * Class WeChatController
 * @package Plugin\Interconnect\Controller
 * 微信登陆
 */
class WeChatController extends BaseController {

    /**
     * @var string
     * 接口版本
     */
    protected $version             = '2.0';

    /**
     * @var string
     * 获取code的路径  跳转到登陆页面的路径
     */
    protected $getAuthCodeUrl      = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    /**
     * @var string
     * 获取token的路径
     */
    protected $getAccessTokenUrl   = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * @var string
     * 获取openid的路径
     */
    protected $getOpenidUrl        = 'https://graph.qq.com/oauth2.0/me';

    /**
     * @var string
     * 获取登陆者信息路径
     */
    protected $getUserInfo         = 'https://api.weixin.qq.com/sns/userinfo';

    /**
     * @var string
     * QQ授权api接口名称
     */
    //protected $scope               = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
    protected $scope               = "snsapi_userinfo";

    /**
     * @param $apps
     * @param $callback
     * @return mixed|void
     * 登陆跳转
     */
    function login($apps, $callback) {
        //生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
        //设置随机串到session 以便验证
        session('state', $state);
        //构造请求参数列表  Oauth 标准参数
        $params = array(
            'appid'             => $apps[0],
            "redirect_uri"      => $callback, //回调地址  加上平台类型
            "response_type"     => "code",
            "scope"             => $this->scope,
            "state"             => $state,
            //"client_id"         => $apps[0],
        );
        //跳转到登陆页
        redirect($this->getAuthCodeUrl . '?' . http_build_query($params));
    }

    /**
     * @param $apps
     * @param $callback
     * @return mixed|string
     * 回调
     */
    function callback($apps, $callback) {
        //验证state防止CSRF攻击
        if(I('get.state') != session('state')) {
            return 'STATE ERROR!';
        }
        //释放验证state
        session('state', null);
        //请求access_token的参数
        $params = array(
            'appid'         => $apps[0],
            'secret'        => $apps[1],
            "code"          => I('get.code'),
            "grant_type"    => "authorization_code"
        );
        //构造请求access_token的url 并访问获取内容
        $response = $this->oauthRequest($this->getAccessTokenUrl, 'POST', $params);
        //成功格式 '{"access_token":"tncIWX7D7lzB8gqg9xv-T7jS_N6JP5vhPm4U4YcDYr1ieam1iqb3JYavlt_aShPJmhqBb4eS0WlhVNN2Sq5TimwocGjZ5E7n6oEwcyr3h9I","expires_in":7200,"refresh_token":"LOyaPI0kPPnQQUW0xNUFN7UVB_-CMleeScTkF0o11ynRbLmiAJFitqBADzWdsu-Y6X21a8siujgo9n2iLMUvDcAPo-9WHkSkjbg2nTBEx8Y","openid":"orT6ewbXvXED8LH2HqFuzGDOeECs","scope":"snsapi_userinfo"}'
        //报错格式 '{"errcode":41004,"errmsg":"appsecret missing, hints: [ req_id: _cJmna0761s110 ]"}'
        //如果响应为空 访问路径错误 或者 curl出错
        if(empty($response)) {
            return 'CURL ERROR!';
        }
        //转换成json格式
        $tokens = json_decode($response);
        //判断请求成功还是失败
        if(isset($tokens->errcode)) {
            return $tokens->errmsg;
        }
        //释放参数
        unset($params, $response);

        //请求获取用户信息的参数
        $params = array(
            'access_token'    => $tokens->access_token,
            'openid'          => $tokens->openid,
            'lang'            => 'zh_CN'
        );
        //构造请求用户信息的url 并访问获取内容
        $response = $this->oauthRequest($this->getUserInfo, 'GET', $params);
        //报错格式 '{"errcode":41004,"errmsg":"appsecret missing, hints: [ req_id: _cJmna0761s110 ]"}'
        /*成功格式 '{"openid":"orT6ewbXvXED8LH2HqFuzGDOeECs","nickname":"黑暗中的武者","sex":2,"language":"zh_CN","city":"林芝","province":"西藏","country":"中国",
        "headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/ajNVdqHZLLCTGvQMDBn6MwEPWa9HhCZgtcEibkpKk1SrNbtib13EV9QQ7hkfvVz7ic6gnPLWGBeHb6icGWh5mcT97g\/0","privilege":[]}'*/

        //json转化成对象
        $user_info = json_decode($response);
        //报错信息
        if(isset($user_info->errcode)) {
            return $user_info->errmsg;
        }

        //返回标准信息格式 openid
        return array(
            'openid'            => $user_info->openid,          //必不可少
            'platform_nickname' => $user_info->nickname,    //昵称必须
            'platform_head'     => $user_info->headimgurl, //头像必须
        );
    }
}