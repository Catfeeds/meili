<?php
/**
 * 阿里相关配置
 */
return array(
    'ALI_PRIVATE_KEY' => '',
    'ALI_APP_ID' => '',
    //支付宝支付配置

    'ALIPAY_CONFIG_API' => array(
        'partner'               => '2088721269201932', //合作身份者id，以2088开头的16位纯数字
        'seller_email'          => '1977920593@qq.com', //收款支付宝账号，一般情况下收款账号就是签约账号
        'seller_id'             => '', //收款支付宝账号，一般情况下收款账号就是签约账号 wap
        'private_key_path'      => './ThinkPHP_3.2.3/Library/Vendor/Pay/AliPay/JSDZWAP/key/rsa_private_key.pem', //商户的私钥（后缀是.pen）文件相对路径 wap
        'public_key_path'       => 'key/public_key.pem', //支付宝公钥（后缀是.pen）文件相对路径 wap
        'key'                   => 'vwm8juk88rvuh65grthq05ke3ela2icw', //安全检验码，以数字和字母组成的32位字符
        //'sign_type'             => strtoupper('MD5'), //签名方式 不需修改
        'sign_type'             => strtoupper('RSA'), //签名方式 不需修改 wap
        'input_charset'         => strtolower('utf-8'), //字符编码格式 目前支持 gbk 或 utf-8
        'cacert'                => getcwd().'\\cacert.pem', //ca证书路径地址，用于curl中ssl校验 //请保证cacert.pem文件在当前文件夹目录中
        'transport'             => 'http', //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        'private'               => 'MIICWwIBAAKBgQCmTXEIg/tkh9PdXaVsadCht4aPFtBP22sYL8iQDs4o1oNOyvW+lorgpDxZ9WwL1f+sR5LPlrqqcSs1JXZJkjaP3/eKE1rIbpCMmB2L8FMgsrvzNwYhdguJQV73kaAuSopBhcKwUsiOzTGc+hkPqlZvi/c0dXwdk9bM3YbQRK71vQIDAQABAoGAFYNjIfTg6opfr/1TTwkJQkJZl90dqZb67bAQxAIGTWYiqmi7DaKv6IuWexSym31di83eghg/oZjuO/vSp4XQpOqzXrpGHOoMqB5sSr6yw5jISYbv70Sj5As/SvpfQeQs+O8dKQXutlJDjh8FAg5IQnh/AHFJe4OoCtznBdu9pMECQQDQH4hlVEyGZHH1IxSu5xYT7wabqFWOIEJ0803t7/0JsJuJ392xoyXu9L8+guq9zPUN34NAoaMHHdm41AXBBubVAkEAzI8SHfdKMyGXQJaK+UTWowgJzKmFeNLrB4Dp5/vqXuYtI0ItTf6w4sVW692djCR7IlAwvqtZXD3Lrdnm4BIXSQJAHVpFJ11jSZUDCXrAIQbQc1FD0lJEdr4QAWSLOiKdwm8ZELH1F2eWIwR7sHpQVyJ/8Uvzu/rP/mH0Yf/tK9MoXQJAHhLOpA8uHnRKy1kWl20SbSeKYUdu8wN3QEQon01++HK4oh1hkbzm/n/qtoR/XBIk9Dd74xxH5/LB1g5aDgk62QJAK8hi8qI7Lw3kRq/vaBBS+gVfGJi7y6nwMEP0IVaeNw4RydR0iOKpT1/Q3zujTs8U23622G8c9LKabLyA4cLXuw=='
    ),

//    'ALIPAY_CONFIG_API' => array(
//        'partner'               => '2088721269201932', //合作身份者id，以2088开头的16位纯数字
//        'seller_email'          => '1977920593@qq.com', //收款支付宝账号，一般情况下收款账号就是签约账号
//        'seller_id'             => '', //收款支付宝账号，一般情况下收款账号就是签约账号 wap
//        'private_key_path'      => './ThinkPHP_3.2.3/Library/Vendor/Pay/AliPay/JSDZWAP/key/rsa_private_key.pem', //商户的私钥（后缀是.pen）文件相对路径 wap
//        'public_key_path'       => 'key/public_key.pem', //支付宝公钥（后缀是.pen）文件相对路径 wap
//        'key'                   => 'vwm8juk88rvuh65grthq05ke3ela2icw', //安全检验码，以数字和字母组成的32位字符
//        //'sign_type'             => strtoupper('MD5'), //签名方式 不需修改
//        'sign_type'             => strtoupper('RSA'), //签名方式 不需修改 wap
//        'input_charset'         => strtolower('utf-8'), //字符编码格式 目前支持 gbk 或 utf-8
//        'cacert'                => getcwd().'\\cacert.pem', //ca证书路径地址，用于curl中ssl校验 //请保证cacert.pem文件在当前文件夹目录中
//        'transport'             => 'http', //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
//        'private'               => 'MIICWwIBAAKBgQCmTXEIg/tkh9PdXaVsadCht4aPFtBP22sYL8iQDs4o1oNOyvW+lorgpDxZ9WwL1f+sR5LPlrqqcSs1JXZJkjaP3/eKE1rIbpCMmB2L8FMgsrvzNwYhdguJQV73kaAuSopBhcKwUsiOzTGc+hkPqlZvi/c0dXwdk9bM3YbQRK71vQIDAQABAoGAFYNjIfTg6opfr/1TTwkJQkJZl90dqZb67bAQxAIGTWYiqmi7DaKv6IuWexSym31di83eghg/oZjuO/vSp4XQpOqzXrpGHOoMqB5sSr6yw5jISYbv70Sj5As/SvpfQeQs+O8dKQXutlJDjh8FAg5IQnh/AHFJe4OoCtznBdu9pMECQQDQH4hlVEyGZHH1IxSu5xYT7wabqFWOIEJ0803t7/0JsJuJ392xoyXu9L8+guq9zPUN34NAoaMHHdm41AXBBubVAkEAzI8SHfdKMyGXQJaK+UTWowgJzKmFeNLrB4Dp5/vqXuYtI0ItTf6w4sVW692djCR7IlAwvqtZXD3Lrdnm4BIXSQJAHVpFJ11jSZUDCXrAIQbQc1FD0lJEdr4QAWSLOiKdwm8ZELH1F2eWIwR7sHpQVyJ/8Uvzu/rP/mH0Yf/tK9MoXQJAHhLOpA8uHnRKy1kWl20SbSeKYUdu8wN3QEQon01++HK4oh1hkbzm/n/qtoR/XBIk9Dd74xxH5/LB1g5aDgk62QJAK8hi8qI7Lw3kRq/vaBBS+gVfGJi7y6nwMEP0IVaeNw4RydR0iOKpT1/Q3zujTs8U23622G8c9LKabLyA4cLXuw=='
//    ),

//    'ALIPAY_CONFIG_API' => array(
//        'partner'               => '2088121900317297', //合作身份者id，以2088开头的16位纯数字  2088721269201932
//        'seller_email'          => '2534876634@qq.com', //收款支付宝账号，一般情况下收款账号就是签约账号
//        'seller_id'             => '', //收款支付宝账号，一般情况下收款账号就是签约账号 wap
//        'private_key_path'      => './ThinkPHP_3.2.3/Library/Vendor/Pay/AliPay/JSDZWAP/key/rsa_private_key.pem', //商户的私钥（后缀是.pen）文件相对路径 wap
//        'public_key_path'       => 'key/public_key.pem', //支付宝公钥（后缀是.pen）文件相对路径 wap
//        'key'                   => '4bgsvlekjq2blbvo6k6qkpt31obfnelf', //安全检验码，以数字和字母组成的32位字符  vwm8juk88rvuh65grthq05ke3ela2icw
//        //'sign_type'             => strtoupper('MD5'), //签名方式 不需修改
//        'sign_type'             => strtoupper('RSA'), //签名方式 不需修改 wap
//        'input_charset'         => strtolower('utf-8'), //字符编码格式 目前支持 gbk 或 utf-8
//        'cacert'                => getcwd().'\\cacert.pem', //ca证书路径地址，用于curl中ssl校验 //请保证cacert.pem文件在当前文件夹目录中
//        'transport'             => 'http', //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
//        'private'               => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBANbNNhScdoFKAoC1a5/3XzW2tYTs6xZb65b0TAib2D93X2VKO6Zs4WMzK/cjCStRKnZKCdL8u42PnxhU0nKt2TpHlOubNC/PJTLqZZpQKaMk85Cb70EI+Fh+QUPKIuarAdYYZqr+/2kfFitPVRPztwtP/H/b3wPenp5NK1yeoRWbAgMBAAECgYAoey4o/j+7J/aGySoKULVpyxA0h+3aHeKtZUb7DFvZwiaBUVciizyC1H8BqWGt/zLwbg2h7K1wBVQnYrzyjd71K2dtWdT3/6O3LstOObvpiSo9nTL5GpJS8gDiGr8Tm+ituoSmZj0AVo9zlu+7HRxUK62g8w1Ev8y9oxRcT3CXqQJBAO7MfkPdwcRdJ4OWbJgzvW3jnJ5Z229W4RcJecM8QMgZdj+HZaxDIRmbW1n9E7qEnGUvkIxAbWbBDsqI9GwgwE8CQQDmRjQ7k9OyHfDxe+WWa9l2uuodiFUhrKCp+72ZMFiYbe0pBBAjzRQ7yfyTYYjG0N15p9dQ0S/I8ImbV1NUSdb1AkEA1HkPP8NoTRe1uNd2+FXRDp2fFSZOoNpknOLJfHV4DpLZK92FEakJIoeg2IjdrO+hWEbiDmik7vCIAJ2rHSpm8QJAF/lBIN7AEHArkIiMm3948XJ+QzrZWhsl0uyhjZxJ7PyszzNcFs4YCC18PT/PRJukIzFFKmXM6seYG/MYetMBLQJBAOi1aN+PQ1Sh4uFk1jP/4xxXwYal/kueOzvl/kg/8SPC78lgUAZew5tBBhRSbemeBucDCr2k4ln3T7kuB94Vc10=', //APP签名私钥
//    ),

    /*'ALIPAY_CONFIG_HOME' => array(
        'partner'               => '2088002051381781', //合作身份者id，以2088开头的16位纯数字
        'seller_email'          => 'zjml2005@163.com', //收款支付宝账号，一般情况下收款账号就是签约账号
        'seller_id'             => '', //收款支付宝账号，一般情况下收款账号就是签约账号 wap
        'private_key_path'      => 'key/rsa_private_key.pem', //商户的私钥（后缀是.pen）文件相对路径 wap
        'public_key_path'       => 'key/public_key.pem', //支付宝公钥（后缀是.pen）文件相对路径 wap
        'key'                   => '1pfq0bsz6n7ig4qvilq1uj7zksufn74r', //安全检验码，以数字和字母组成的32位字符
        'sign_type'             => strtoupper('MD5'), //签名方式 不需修改
        //'sign_type'             => strtoupper('RSA'), //签名方式 不需修改 wap
        'input_charset'         => strtolower('utf-8'), //字符编码格式 目前支持 gbk 或 utf-8
        'cacert'                => getcwd().'\\cacert.pem', //ca证书路径地址，用于curl中ssl校验 //请保证cacert.pem文件在当前文件夹目录中
        'transport'             => 'http', //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    ),*/
);