<?php
namespace Plugin\Interconnect\Controller;

/**
 * Class QqController
 * @package Plugin\Interconnect\Controller
 * QQ互联登录 相关方法
 */
class QqController extends BaseController {

    /**
     * @var string
     * 接口版本
     */
    protected $version             = '2.0';

    /**
     * @var string
     * 获取code的路径  跳转到登陆页面的路径
     */
    protected $getAuthCodeUrl      = 'https://graph.qq.com/oauth2.0/authorize';

    /**
     * @var string
     * 获取token的路径
     */
    protected $getAccessTokenUrl   = 'https://graph.qq.com/oauth2.0/token';

    /**
     * @var string
     * 获取openid的路径
     */
    protected $getOpenidUrl        = 'https://graph.qq.com/oauth2.0/me';

    /**
     * @var string
     * 获取登陆者信息路径
     */
    protected $getUserInfo         = 'https://graph.qq.com/user/get_user_info';

    /**
     * @var string
     * QQ授权api接口名称
     */
    //protected $scope               = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
    protected $scope               = "get_user_info,add_share";


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
            "response_type"     => "code",
            "client_id"         => $apps[0],
            "redirect_uri"      => $callback, //回调地址  加上平台类型
            "state"             => $state,
            "scope"             => $this->scope
        );
        //跳转到登陆页
        redirect($this->getAuthCodeUrl . '?' . http_build_query($params));
    }


    /**
     * @param $apps
     * @param $callback
     * @return array|mixed
     * 回调方法
     * 分三次请求  第一次获取token  第二次获取openid  第三次获取用户信息
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
            "grant_type"    => "authorization_code",
            "client_id"     => $apps[0],  //app_id
            "redirect_uri"  => $callback, //回调地址  加上平台类型
            "client_secret" => $apps[1], //app_key
            "code"          => I('get.code')
        );
        //构造请求access_token的url 并访问获取内容
        $response = $this->oauthRequest($this->getAccessTokenUrl, 'GET', $params);
        //成功格式 'access_token=1B78A35DE5B6DD7FF5AD924FF193243E&expires_in=7776000&refresh_token=C27C2A582E5975D40B521D4FCBC4B9E6'
        //报错格式 'callback( {"error":100010,"error_description":"redirect uri is illegal"} );'
        //如果响应为空 访问路径错误 或者 curl出错
        if(empty($response)) {
            return 'CURL ERROR!';
        }
        //判断请求成功还是失败 存在callback则请求失败
        if(strpos($response, "callback") !== false) {
            //处理失败信息 去掉 括号左右两边内容
            $response   = substr($response, strpos($response, "(") + 1, strrpos($response, ")") - strpos($response, "(") - 1);
            //json转化成对象
            $error      = json_decode($response);
            //报错
            if(isset($error->error)){
                return $error->error_description;
            }
        }
        //解析字符串为数组
        parse_str($response, $tokens);
        //释放参数
        unset($params, $response);

        //请求openid的参数
        $params = array(
            "access_token" => $tokens['access_token'],
        );
        //构造请求openid的url 并访问获取内容
        $response = $this->oauthRequest($this->getOpenidUrl, 'GET', $params);
        //报错格式 'callback( {"error":100007,"error_description":"param access token is wrong or lost "} );'
        //成功格式 'callback( {"client_id":"101276783","openid":"D7227FFD0E63E5A102FC25585D88B2D9"} );'

        //检测错误是否发生
        if(strpos($response, "callback") !== false){
            //处理失败信息 去掉 括号左右两边内容
            $response = substr($response, strpos($response, "(") + 1, strrpos($response, ")") - strpos($response, "(") -1);
        }
        //json转化成对象
        $opens = json_decode($response);
        //报错信息
        if(isset($opens->error)){
            return $error->error_description;
        }
        //释放参数
        unset($params, $response);

        //请求获取用户信息的参数
        $params = array(
            'oauth_consumer_key' => $apps[0], //app_id
            'access_token'       => $tokens['access_token'],
            'openid'             => $opens->openid,
            'format'             => 'json'
        );
        //构造请求用户信息的url 并访问获取内容
        $response = $this->oauthRequest($this->getUserInfo, 'GET', $params);
        //报错格式 '{"ret":-1,"msg":"client request's parameters are invalid, invalid openid"}'
        /*成功格式 '{"ret": 0,"msg": "","is_lost":0,"nickname": "黑暗中的武者","gender": "男","province": "河北","city": "保定","year": "1989",
                    "figureurl": "http://qzapp.qlogo.cn/qzapp/101276783/D7227FFD0E63E5A102FC25585D88B2D9/30",
                    "figureurl_1": "http://qzapp.qlogo.cn/qzapp/101276783/D7227FFD0E63E5A102FC25585D88B2D9/50",
                    "figureurl_2": "http://qzapp.qlogo.cn/qzapp/101276783/D7227FFD0E63E5A102FC25585D88B2D9/100",
                    "figureurl_qq_1": "http://q.qlogo.cn/qqapp/101276783/D7227FFD0E63E5A102FC25585D88B2D9/40",
                    "figureurl_qq_2": "http://q.qlogo.cn/qqapp/101276783/D7227FFD0E63E5A102FC25585D88B2D9/100",
                    "is_yellow_vip": "0","vip": "0","yellow_vip_level": "0","level": "0","is_yellow_year_vip": "0"}'*/

        //json转化成对象
        $user_info = json_decode($response);
        //报错信息
        if($user_info->ret == -1){
            return $user_info->msg;
        }

        //返回标准信息格式 openid
        return array(
            'openid'            => $opens->openid,          //必不可少
            'platform_nickname' => $user_info->nickname,    //昵称必须
            'platform_head'     => $user_info->figureurl_2, //头像必须
        );
    }
}