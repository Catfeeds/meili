<?php
/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array (
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE'    =>  array('Plugin' => PLUGIN_PATH), //扩展模块列表
    /* 调试配置 */
    'SHOW_PAGE_TRACE'       =>  false,
    /* 用户相关设置 */
    'USER_ADMINISTRATOR'    =>  1, //管理员用户ID
    /* 水印图片路径 */
    'WATER_PATH'            => './Uploads/Config/2015-12-19/water.png',
    /* 文件绝对路径 */
    'FILE_HOST'             => 'http://meili-file.uuudoo.com/',
    /* URL配置 */
    'VAR_URL_PARAMS'        =>  '', // PATHINFO URL参数变量
    /* 语言包设置 */
    'LANG_SWITCH_ON'        =>  true,    // 开启语言包功能
    'LANG_AUTO_DETECT'      =>  false,    // 自动侦测语言 开启多语言功能后有效
    'LANG_LIST'             =>  'zh-cn,en-us,zh-tw', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'          =>  'l',     // 默认语言切换变量
    /* 扩展配置 */
    'LOAD_EXT_CONFIG'       => 'regular,custom,upload',
    'LOAD_EXT_FILE'         => 'custom,bms',

    'READ_DATA_MAP'         =>  false, //开启获取数据字段映射  如果我们需要在数据获取的时候自动处理的话，设置开启READ_DATA_MAP参数，
    /* 应用设定 */
    'APP_USE_NAMESPACE'     =>  true,    // 应用类库是否使用命名空间
    'APP_SUB_DOMAIN_DEPLOY' =>  true,   // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES'  =>  array(   //子域名指向模块配置
        'meili-bms'      => 'Bms',
        'meili-shop-bms' => 'ShopBms',
        'meili-api'      => 'Api',
        'meili-home'     => 'Home',
        'meili-file'     => 'Home',
        'meili-wap'      => 'Wap',
    ), // 子域名部署规则
    'APP_DOMAIN_SUFFIX'     =>  '', // 域名后缀 如果是com.cn net.cn 之类的后缀必须设置
    'ACTION_SUFFIX'         =>  '', // 操作方法后缀
    'MULTI_MODULE'          =>  true, // 是否允许多模块 如果为false 则必须设置 DEFAULT_MODULE
    'MODULE_ALLOW_LIST'     =>  array('Home'),
    'MODULE_DENY_LIST'      =>  array('Common','Runtime','Bms','ShopBms','Api'),
    'CONTROLLER_LEVEL'      =>  1,
    'APP_AUTOLOAD_LAYER'    =>  'Controller,Model', // 自动加载的应用类库层 关闭APP_USE_NAMESPACE后有效
    'APP_AUTOLOAD_PATH'     =>  '', // 自动加载的路径 关闭APP_USE_NAMESPACE后有效

    /* 数据库设置 */
    'DB_TYPE'               => 'Mysql', // 数据库类型
    'DB_HOST'               => '127.0.0.1', // 服务器地址
    'DB_NAME'               => 'meili', // 数据库名
    'DB_USER'               => 'root', // 用户名
    'DB_PWD'                => ',wwsYDKJ2016,',  // 密码
    'DB_PORT'               => '3306', // 端口
    'DB_PREFIX'             => 'toocms_', // 数据库表前缀
    'DB_PARAMS'          	=>  array(), // 数据库连接参数
    'DB_DEBUG'  			=>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '', // 指定从服务器序号

    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX'     =>  'toocms_',     // 缓存前缀

    /* URL设置 */
    'URL_CASE_INSENSITIVE'  =>  false,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
    'DATA_AUTH_KEY'         =>  'Too%Q$L!I#P(3)%@%&J[%$D+a(v5`ni}W|N^o@4c^9<G=VK%cms', //默认数据加密KEY

    /* 静态页面缓存 */
    'HTML_CACHE_ON'         =>    false,    // 开启静态缓存
    'HTML_CACHE_TIME'       =>    0,        // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'      =>    '.html',  // 设置静态缓存文件后缀
    'HTML_CACHE_RULES'      =>     array(   // 定义静态缓存规则
        // 定义格式1 数组方式
        //'Index:index'     =>     array('{:module}/{:controller}_{:action}_{id}', 0, 'md5'),
    ),
);
