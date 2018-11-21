<?php
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */
/**
 * @param int $flag 0数字字符混合 1字符 2数字
 * @param int $num 验证标识的个数
 * @return string
 */
function get_vc($num = 0, $flag = 0) {
    /**获取验证标识**/
    $arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',1,2,3,4,5,6,7,8,9,0);
    $vc  = '';
    switch($flag) {
        case 0 : $s = 0;  $e = 61; break;
        case 1 : $s = 0;  $e = 51; break;
        case 2 : $s = 52; $e = 61; break;
    }
    for($i = 0; $i < $num; $i++) {
        $vc .= $arr[rand($s, $e)];
    }
    return $vc;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param int $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param boolean $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    if($charset == 'utf-8' && strlen($str) > $length*3)
        $suffix = true;
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time():0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function think_decrypt($data, $key = '') {
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);
    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param string $sort_by 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list, $field, $sort_by = 'asc') {
   if(is_array($list)) {
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sort_by) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * @param $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param int $root
 * @return array
 * 把返回的数据集转换成Tree
 */
function list_to_tree($list, $root = 0, $pk = 'id', $pid = 'parent_id', $child = '_child') {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            //以主键为键值的数组
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            //当前分类的父级分类是否等于父根节点
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    //当前分类的伤及分类 引用
                    $parent =& $refer[$parentId];
                    //存入上级分类的子分类中
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, &$list = array(), $child = '_child', $order = 'id'){
    if(is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $refer = $value;
            //是否有子分类
            if(isset($refer[$child])) {
                unset($refer[$child]);
                //递归
                tree_to_list($value[$child], $list, $child, $order);
            }
            $list[] = $refer;
        }
        //排序
        $list = list_sort_by($list, $order, $sort_by = 'asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook,$params=array()) {
    \Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param string $name 插件名
 * @return mixed
 */
function get_plugin_class($name) {
    $class = "Plugin\\{$name}\\{$name}Plugin";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 * @return mixed
 */
function get_plugin_config($name) {
    $class = get_plugin_class($name);
    if(class_exists($class)) {
        $plugin = new $class();
        return $plugin->getConfig();
    } else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @return mixed
 */
function plugins_url($url, $param = array()) {
    $url        = parse_url($url);
    $case       = C('URL_CASE_INSENSITIVE');
    $plugins    = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');
    /* 解析URL带的参数 */
    if(isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }
    /* 基础参数 */
    $params = array(
        'plugins'    => $plugins,
        'controller' => $controller,
        'action'     => $action,
    );
    $params = array_merge($params, $param); //添加额外参数
    return U('Plugins/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @param string $format
 * @return string 完整的时间显示
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * @param $files
 * 基于数组创建目录和文件
 */
function create_dir_or_files($files) {
    foreach ($files as $key => $value) {
        if(substr($value, -1) == '/') {
            mkdir($value);
        } else {
            @file_put_contents($value, '');
        }
    }
}

/**
 * 返回input数组中键值为$columnKey的列
 */
if(!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 * @return mixed
 */
function api($name,$vars = array()) {
    $array      = explode('/',$name);
    $method     = array_pop($array);
    $class_name = array_pop($array);
    $module     = $array? array_pop($array) : 'Common';
    $callback   = $module.'\\Api\\'.$class_name.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1) {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * @param array $by_array  按照该数组排序
 * @param array $list        要排序的列表
 * @param string $key_name  键值名称
 * @return array
 */
function sort_by_array($by_array, $list, $key_name = 'id') {
    if(empty($by_array))
        return $list;
    if(empty($list))
        return array();
    foreach ($list as $key => $data) {
        if(empty($data[$key_name]))
            return array();
        $refer[$data[$key_name]] =& $list[$key];
    }
    foreach($by_array as $val) {
        if(!empty($refer[$val])) {
            $sort_list[] = $refer[$val];
        }
    }
    return $sort_list;
}

/**
 * @param $str
 * @return string
 * 过滤掉html标签
 */
function filter_html($str) {
    return stripslashes(preg_replace("/(\<[^\<]*\>|\r|\n|\s|\&nbsp;|\[.+?\])/is", '', $str));
}

/**
 * @param string $ip
 * @return string
 * 根据真实ip和淘宝ip地址库 获取城市
 * 转换后$data数据格式
 * array (
        'code' => int 0
        'data' => array (
            'country'       => string '中国'
            'country_id'    => string 'CN'
            'area'          => string '华北'
            'area_id'       => string '100000'
            'region'        => string '天津市'
            'region_id'     => string '120000'
            'city'          => string '天津市'
            'city_id'       => string '120100'
            'county'        => string ''
            'county_id'     => string '-1'
            'isp'           => string '电信'
            'isp_id'        => string '100017'
            'ip'            => string '123.150.126.228'
        )
  )
 */
function get_city_by_ip($ip = '') {
    //传值IP还是自动获取IP
    $ip     = !empty($ip) ? $ip : get_client_ip();
    //调用淘宝ip库接口获取信息
    $url    = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
    //调用淘宝接口获取信息
    $data   = file_get_contents($url);
    //json格式转换为数组
    $data   = json_decode($data, 'array');
    //城市名称
    $city   = $data['data']['city'];
    //返回数据 去掉最后一个字  去空格
    return trim(empty($city) ? '天津' : mb_substr($city, 0, strlen($city) - 3));
}

/**
 * @param $number
 * @param $type 1--每几位加入空格 2--隐藏位数
 * @return string
 * 银行卡号 格式处理
 */
function format_card_number($number, $type = 1) {
    if($type == 1) {
        preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/', $number, $match);
        unset($match[0]);
        return implode(' ', $match);
    } elseif($type == 2) {
        return substr_replace($number, '****', 0, strlen($number) - 4);
    } else {
        return '';
    }
}

/**
 * @param string $procedure  存储过程名称
 * @param array $param 参数
 * @param int $result_type 结果类型  1单行 2列表
 * @return array
 * 调用存储过程
 */
function call_procedure($procedure = '', $param = array(), $result_type = 1) {
    //获取数据库链接对象
    //$Db  =  \Think\Db::getInstance();
    $Model  =  new \Think\Model();
    //调用的sql语句
    $sql = "call " . $procedure . "(";
    //是否存在参数
    if(!empty($param)) {
        //循环参数 拼接sql语句
        foreach ($param as $k => $val) {
            //判断是否需要接逗号
            $sym = !isset($param[$k + 1]) ? '' : ',';
            //字符类型需加上引号
            $sql .= is_numeric($val) ? ($val . $sym) : ("'$val'" . $sym);
        }
    }
    $sql .= ")";
    //获取结果
    //$result = $Db->query($sql);
    $result = $Model->procedure($sql);
    //返回列表 还是单行
    return $result_type == 1 ? $result[0][0] : $result;
}

/**
 * @param $arr
 * @return mixed
 * 返回数组的维度
 */
function array_dimension($arr){
    $adi = array(0);
    function adi($arr, &$adi, $level = 0){
        if(is_array($arr)){
            $level++;
            $adi[] = $level;
            foreach($arr as $v){
                adi($v, $adi, $level);
            }
        }
    }
    adi($arr, $adi);
    return max($adi);
}

/**
 * @param $value
 * @return mixed
 * 获取配置图片
 */
function get_config_picture($value = 0) {
    if(empty($value))
        return '';
    $file = api('File/getFiles',array($value));
    return $file[0]['abs_url'];
}

/**
 * @param float $lng 经度
 * @param float $lat 纬度
 * @param int $range 查找范围(km)
 * @return array
 * 获取 以某个经纬度的点为圆心 range为半径的园的外切正方形四个点的经纬度坐标
 */
function four_point($lng = 0.000000, $lat = 0.000000, $range = 5) {
    //地球半径 //单位(km)
    $earth_radius   = 6371;
    //一定范围$range内经度的改变量  保留6位小数
    $l_lng          = number_format(($range * 180) / (3.14 * ($earth_radius * cos(deg2rad($lat)))), 6);
    //一定范围$range内纬度的改变量  保留6位小数
    $l_lat          = number_format(rad2deg($range / $earth_radius), 6);
    //获得以($lng,$lat)为圆心 $range为半径的圆形外切的正方型的四个点的经纬度坐标
    return array(
        'left-top'      => array('lat' => $lat + $l_lat, 'lng' => $lng - $l_lng),
        'right-top'     => array('lat' => $lat + $l_lat, 'lng' => $lng + $l_lng),
        'left-bottom'   => array('lat' => $lat - $l_lat, 'lng' => $lng - $l_lng),
        'right-bottom'  => array('lat' => $lat - $l_lat, 'lng' => $lng + $l_lng)
    );
}

/**
 * @param int $timestamp
 * @param string $format
 * @return bool|string
 * 时间戳转换成字符串时间
 */
function timestamp2date($timestamp = 0, $format = 'Y-m-d H:i') {
    if(empty($timestamp))
        return '--';
    return date($format, $timestamp);
}

/**
 * @param string $content
 * @return mixed|string
 * 主要处理图文详情中 图片路径 转化成绝对路径
 */
function path2abs($content = '') {
    if(empty($content))
        return '';
    //正则获取内容中的src中的路径
    preg_match_all('/src=\"\/?(.*?)\"/', $content, $match);
    //循环获取到的路径 替换
    foreach($match[1] as $src) {
        if(!strpos($src, '://')) //相对路径、本站图片 替换
            $content = str_replace('/' . $src . '"', C('FILE_HOST') . '/' . $src . '" width=100%', $content);
        else //绝对路径、站外图片
            $content = str_replace($src . '"', $src . '" width=100%' , $content);
    }
    return $content;
}

/**
 * @param string $xml
 * @return mixed
 * xml转化成数组
 */
function xml2array($xml = '') {
    if(empty($xml))
        return array();
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    //转化
    return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
}

/**
 * @param int $payment
 * @return string
 * 获取支付方式名称
 * 黑暗中的武者
 */
function get_payment_name($payment = 0) {
    if(empty($payment))
        return '---';
    switch ($payment) {
        case 1  : return    '支付宝支付';       break;
        case 2  : return    '微信支付';     break;
        case 3  : return    '余额支付';     break;
		case 4  : return    '现金支付';     break;
        default : return    '未支付';     break;
    }
}

/**
 * @param int $time
 * @return bool|string
 * 时间戳转化成模糊日期
 */
function fuzzy_date($time = 0) {
    if(empty($time))
        return '---';
    //今天零时零分零秒
    $today = strtotime(date('Y-m-d'));
    //传递时间与当前时间相差的秒数
    $diff = NOW_TIME - $time;
    //判断
    if($diff < 60)
        return '刚刚';
    if($diff < 3600)
        return floor($diff / 60) . '分钟前';
    if($diff < 28800)
        return floor($diff / 3600) . '小时前';
    if($time > $today)
        return '今天 ' . date('H:i', $time);
    else
        return date('Y-m-d H:i', $time);
}

/**
 * 生成UUID
 */
if(!function_exists('com_create_guid')) {
    function com_create_guid() {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 andup.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);//字符 "-"
        $uuid = //chr(123)//字符 "{"
            //substr($charid, 0, 8).$hyphen
            substr($charid, 0, 4).$hyphen
            //.substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            //.substr($charid,20,12);
            .substr($charid,20,4);
            //.chr(125);//字符 "}"
        return $uuid;
    }
}