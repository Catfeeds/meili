<?php
namespace Plugin\Interconnect\Controller;

/**
 * Class QqController
 * @package Plugin\Interconnect\Controller
 * QQ互联登录 相关方法
 */
class SinaController extends BaseController {

    /**
     * @var string
     * 接口版本
     */
    protected $version             = '2.0';

    /**
     * @var string
     * 获取code的路径  跳转到登陆页面的路径
     */
    protected $getAuthCodeUrl      = 'https://api.weibo.com/oauth2/authorize';

    /**
     * @var string
     * 获取token的路径
     */
    protected $getAccessTokenUrl   = 'https://api.weibo.com/oauth2/access_token';


    /**
     * @var string
     * 获取token信息的路径  兼得用户ID
     */
    protected $getTokenInfoUrl     = 'https://api.weibo.com/oauth2/get_token_info';

    /**
     * @var string
     * 获取登陆者信息路径
     */
    protected $getUserInfo         = 'https://api.weibo.com/2/users/show.json';

    /**
     * @var string
     * 授权api接口名称
     */
    //protected $scope               = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
    protected $scope               = "all";


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
        $response = $this->oauthRequest($this->getAccessTokenUrl, 'POST', $params);
        //成功格式 '{"access_token":"2.00FMDpFEIv_ARB0ba0aa02b7OWQogC","remind_in":"157679999","expires_in":157679999,"uid":"3750580017","scope":"follow_app_official_microblog"}'
        //报错格式 '{"error":"invalid_client","error_code":21324,"request":"/oauth2/access_token","error_uri":"/oauth2/access_token","error_description":"unknow client id:0512fe338d57d5c7c1d8e03656cde503"}'
        //如果响应为空 访问路径错误 或者 curl出错
        if(empty($response)) {
            return 'CURL ERROR!';
        }
        //转换成json格式
        $tokens = json_decode($response);
        //判断请求成功还是失败
        if(isset($tokens->error)) {
            return $tokens->error_description;
        }
        //释放参数
        unset($params, $response);

        //请求获取用户信息的参数
        $params = array(
            'access_token'    => $tokens->access_token,
            'uid'             => $tokens->uid,
        );
        //构造请求用户信息的url 并访问获取内容
        $response = $this->oauthRequest($this->getUserInfo, 'GET', $params);
        //报错格式 '{"error":"source paramter(appkey) is missing","error_code":10006,"request":"/2/users/show.json"}'
        /*成功格式 '{"id":3750580017,"idstr":"3750580017","class":1,"screen_name":"我来三天了666","name":"我来三天了666","province":"11","city":"1","location":"北京 东城区","description":"",
                    "url":"","profile_image_url":"http://tp2.sinaimg.cn/3750580017/50/0/1","profile_url":"u/3750580017",
                    "domain":"","weihao":"","gender":"m","followers_count":10,"friends_count":14,"pagefriends_count":0,"statuses_count":10,"favourites_count":0,
                    "created_at":"Tue Aug 27 11:34:05 +0800 2013","following":false,"allow_all_act_msg":false,"geo_enabled":true,"verified":false,"verified_type":-1,"remark":"",
                    "status":{"created_at":"Mon Sep 16 16:14:56 +0800 2013","id":3623190092241349,"mid":"3623190092241349",
                                "idstr":"3623190092241349","text":"//@我来三天了666: 5","source_allowclick":0,"source_type":1,
                                "source":"<a href=\"http://weibo.com/\" rel=\"nofollow\">微博 weibo.com</a>","favorited":false,
                                "truncated":false,"in_reply_to_status_id":"","in_reply_to_user_id":"","in_reply_to_screen_name":"",
                                "pic_urls":[],"geo":null,"reposts_count":0,"comments_count":0,"attitudes_count":0,
                                "isLongText":false,"mlevel":0,"visible":{"type":0,"list_id":0},"biz_feature":0,"darwin_tags":[],"userType":0
                            },
                    "ptype":0,"allow_all_comment":true,"avatar_large":"http://tp2.sinaimg.cn/3750580017/180/0/1",
                    "avatar_hd":"http://tp2.sinaimg.cn/3750580017/180/0/1","verified_reason":"","verified_trade":"","verified_reason_url":"",
                    "verified_source":"","verified_source_url":"","follow_me":false,"online_status":0,"bi_followers_count":1,"lang":"zh-cn",
                    "star":0,"mbtype":0,"mbrank":0,"block_word":0,"block_app":0,"credit_score":80,"user_ability":0,"urank":1}'*/

        //json转化成对象
        $user_info = json_decode($response);
        //报错信息
        if(isset($user_info->error_code)){
            return $user_info->error;
        }

        //返回标准信息格式 openid
        return array(
            'openid'            => $user_info->id,          //必不可少
            'platform_nickname' => $user_info->screen_name,    //昵称必须
            'platform_head'     => $user_info->avatar_large, //头像必须
        );
    }
}