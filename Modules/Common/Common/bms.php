<?php
/**
 * 后台公共相关方法
 */

/**
 * @return mixed
 * 处理菜单列表 加入高亮状态
 */
function get_menus() {
    $menus = C('MENUS');
    //处理每个菜单的信息
    foreach($menus as $key_a => &$menu) {
        //该判断主要是 针对没有子菜单的父菜单
        if (false !== strpos($menu['group']['url'], CONTROLLER_NAME)) {
            $menu['group']['class'] = 'active'; //高亮设置
            break; //结束当前循环
        }
        //存在子菜单
        if (!empty($menu['_child'])) {
            //循环子菜单 判断高亮
            foreach ($menu['_child'] as $key_b => &$child) {
                //如果当前控制器和方法在  映射设置里边 则设置其映射菜单为高亮
                if(in_array(CONTROLLER_NAME . '/' . ACTION_NAME, C('CURRENT')) || in_array(CONTROLLER_NAME, C('CURRENT'))) {
                    //获取 当前控制器或者控制器+方法 在CURRENT设置中的键值 以获取映射中的值
                    $c_k1 = array_keys(C('CURRENT'), CONTROLLER_NAME . '/' . ACTION_NAME);
                    $c_k2 = array_keys(C('CURRENT'), CONTROLLER_NAME);
                    //控制器+方法 优先
                    $m_k  = !empty($c_k1) ? $c_k1[0] : $c_k2[0];
                    //设置其映射菜单为高亮
                    $mapping = C('MAPPING');
                    if(false !== strpos($child['url'], $mapping[$m_k])) {
                        $menu['group']['class'] = $child['class'] = 'active';
                        break 2; //结束两层循环
                    }
                }
                //拆分路径为 控制器和方法的数组
                $urls = explode('/', $child['url']);
                //如果check_level级别为1 则判断 控制器+方法  菜单高亮
                if($child['check_level'] == 1) {
                    //控制器和方法都能对应上
                    if(in_array(CONTROLLER_NAME, $urls) && in_array(ACTION_NAME, $urls)){
                        $menu['group']['class'] = $child['class'] = 'active';
                        break 2;
                    }
                    continue;
                }
                //判断当前控制器在那个菜单路径下 该菜单高亮
                if (in_array(CONTROLLER_NAME, $urls)) {
                    $menu['group']['class'] = $child['class'] = 'active';
                    break 2;
                }
            }
        }
    }
    return $menus;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 */
function get_status_title($status = null) {
    switch ($status) {
        case null : return    '---';   break;
        case 0  : return    '<span style="color:#E5504D">禁用</span>';     break;
        case 1  : return    '正常';     break;
        case 2  : return    '被拒绝';     break;
        case 3  : return    '审核中';     break;
        case 4  : return    '未申请认证';     break;
        default : return    false;      break;
    }
}

/**
 * @param $status
 * @return bool|string
 * 获取数据的状态操作
 */
function show_status_name($status) {
    switch ($status) {
        case 0  : return    '启用';     break;
        case 1  : return    '禁用';     break;
        case 2  : return    '审核';	   break;
        default : return    false;     break;
    }
}

/**
 * @param $status
 * @return bool|string
 * 获取数据的状态操作
 */
function show_status_label($status) {
    switch ($status) {
        case 0  : return    'label-info label-info-hover';       break;
        case 1  : return    'label-inverse label-inverse-hover';    break;
        case 2  : return    '';		       break;
        default : return    false;               break;
    }
}

/**
 * @param $flag
 * @return string
 */
function show_on_off_icon($flag) {
    switch ($flag) {
        case 0  : return    'fa-minus-circle';  break;
        case 1  : return    'fa-check';      break;
        default : return    '';             break;
    }
}

/**
 * @param $value
 * @param $config
 * @return mixed
 * 获取标记对应的数组类型配置信息
 */
function get_config_title($value, $config) {
    $list = C(''.$config.'');
    return empty($list[$value]) ? '---' : $list[$value];
}

/**
 * @param $string
 * @return array
 * 分析枚举类型配置值 格式 a:名称1,b:名称2
 */
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')) {
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    } else {
        $value  =   $array;
    }
    return $value;
}

/**
 * @param $string
 * @return array
 * 分析枚举类型字段值 格式 a:名称1,b:名称2
 * 暂时和 parse_config_attr功能相同
 * 但请不要互相使用，后期会调整
 */
function parse_field_attr($string) {
    if(0 === strpos($string,':')) {
        // 采用函数定义
        return   eval(substr($string,1).';');
    }
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')) {
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    } else {
        $value  =   $array;
    }
    return $value;
}

/**
 * @param $str  要执行替换的字符串
 * @param $rep_flag 替换标记
 * @param $tar_str 目标字符
 * @return mixed
 */
function replace($str, $rep_flag, $tar_str) {
    return $str = preg_replace("/{".$rep_flag."}/i", ''.$tar_str.'', $str);
}

/**
 * @param $type
 * @return string
 * 地区级别名称
 */
function get_region_type_name($type) {
    switch ($type) {
        case 1       : return    '城市';    break;
        case 2       : return    '区域';    break;
        case 3       : return    '商圈';    break;
        default      : return    '';       break;
    }
}

/**
 * @param $type
 * @return string
 * 管理员类型名称
 * */
function get_admin_type_name($type) {
    switch ($type) {
        case 1       : return    '平台';    break;
        case 2       : return    '分站';    break;
        default      : return    '';       break;
    }
}

/**
 * @param $status
 * @return bool|string
 * 获取服务项目标签颜色
 */
function show_item_type_badge($status) {
    switch ($status) {
        case 1  : return    'badge-info';       break;
        case 2  : return    'badge-warning';    break;
        case 3  : return    'badge-success';    break;
        case 4  : return    'badge-primary';    break;
        default : return    false;              break;
    }
}

/**
 * @param $reg_id
 * @param $reg_type
 * @return mixed|string
 * 获取地区名称
 */
function get_region_name($reg_id, $reg_type = 1) {
    if(empty($reg_id))
        return $reg_type == 1 ? '--选择城市--' : $reg_type == 2 ? '--选择区域--' : '--选择商圈--';
    //获取地区名称
    $name = M('Region')->where(array('id'=>$reg_id))->getField('name');
    if(!$name)
        return $reg_type == 1 ? '--选择城市--' : $reg_type == 2 ? '--选择区域--' : '--选择商圈--';
    else
        return $name;
}

/**
 * @param $terminal
 * @return bool|string
 * 获取终端名称
 */
function get_terminal_name($terminal) {
    switch ($terminal) {
        case 1  : return    '安卓手机';     break;
        case 2  : return    '苹果手机';     break;
        case 3  : return    '电脑网站';	   break;
        case 4  : return    '手机网站';	   break;
        default : return    '未知';     break;
    }
}

/**
 * @param $type
 * @return string
 */
function controller_by_order_type($type) {
    switch ($type) {
        case 1  : return    'OrderInfo';     break;
        case 2  : return    'RefundOrder';     break;
        case 3  : return    'CancelOrder';	   break;
        case 4  : return    'ComplaintOrder';	   break;
        default : return    'OrderInfo';     break;
    }
}
/**
 * @param $type
 * @return string
 */
function controller_by_order_type_ser($type) {
    switch ($type) {
        case 1  : return    'OrderInfoSer';     break;
        case 2  : return    'RefundOrderSer';     break;
        case 3  : return    'CancelOrderSer';	   break;
        case 4  : return    'ComplaintOrderSer';	   break;
        default : return    'OrderInfoSer';     break;
    }
}