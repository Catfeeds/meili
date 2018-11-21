<?php
/**
 * 发送相关配置
 */
return array(
    /***** 短信服务配置 *****/
    'SMS_DRIVER'    => 'juchen', //巨辰短信平台驱动
    'SMS_SIGN'      => '优道科技',
    'SMS_ACCOUNT'   => 'test2017',
    'SMS_PASSWORD'  => 'toocms2017',
    //'SMS_DRIVER'    => 'tencent', //腾讯云短信平台驱动
    //'SMS_ACCOUNT'   => '1400029651', //appid
    //'SMS_PASSWORD'  => 'ecbde820a030aaa5b1e2a4f93f1e3bed', //appkey


    /***** 邮件服务器配置 *****/
    'SMTP_HOST'     => 'smtp.163.com',
    'SMTP_PORT'     => '25',
    'SMTP_USER'     => '13302119930@163.com',
    'SMTP_PASS'     => 'lyq166273',
    'FROM_EMAIL'    => '13302119930@163.com',
    'FROM_NAME'     => '晟轩网络科技有限公司',
    'REPLY_NAME'    => '',
    'REPLY_EMAIL'   => '',


    'JPUSH_APP_KEY_2' => 'fd5fc1d9331b260cf2ee5ae6',//66c7d176fb8643e2347d11e8 de8090f58eca0af698b3ac89
    'JPUSH_MASTER_SECRET_2' => 'e90bc137fe816d0cbb0642b3',//f463ebba0633c83cc93073db add2576e8e8b03da98c08510
    /** 普通用户 **/
    'JPUSH_APP_KEY_1' => 'f2a6dc12131665eb232cabc8',//'31afb5fd1d558fd38eb55a44',
    'JPUSH_MASTER_SECRET_1' => '2514adb3fc1453bcaf249bb1',//'3f91e1b7d18fbc7920f0c107',
);