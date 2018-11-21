<?php
/**
 * 配置信息
 */
return array(
    'group' =>  array(
        'title' =>  '第三方互联',
        'type'  =>  'group',
        'options'   =>  array(
            'qq'    =>  array(
                'title'     => 'QQ登陆',
                'options'   => array(
                    'qq_app_id'  =>  array(
                        'title' =>  'APP_ID',
                        'type'  =>  'text',
                        'value' =>  '',
                        'tip'   =>  '应用注册成功、创建应用后分配的 APP ID'
                    ),
                    'qq_app_key' =>  array(
                        'title' =>  'APP_KEY',
                        'type'  =>  'text',
                        'value' =>  '',
                        'tip'   =>  '应用注册成功、创建应用后分配的APP KEY'
                    ),
                )
            ),
            'sina'  =>  array(
                'title'     => '新浪微博登陆',
                'options'   => array(
                    'sina_app_key'  =>  array(
                        'title' =>  'APP_KEY',
                        'type'  =>  'text',
                        'value' =>  '',
                        'tip'   =>  '应用注册成功、创建应用后分配的 APP KEY'
                    ),
                    'sina_app_secret' =>  array(
                        'title' =>  'APP_SECRET',
                        'type'  =>  'text',
                        'value' =>  '',
                        'tip'   =>  '应用注册成功、创建应用后分配的APP SECRET'
                    ),
                )
            ),
            'wx'  =>  array(
                'title'     => '微信授权登陆',
                'options'   => array(
                    'wx_app_id'  =>  array(
                        'title' =>  'APP_ID',
                        'type'  =>  'text',
                        'value' =>  '',
                        'tip'   =>  '微信公众号AppId'
                    ),
                    'wx_app_secret' =>  array(
                        'title' =>  'APP_SECRET',
                        'type'  =>  'text',
                        'value' =>  '',
                        'tip'   =>  '微信公众号AppSecret'
                    ),
                )
            ),
        )
    )
);