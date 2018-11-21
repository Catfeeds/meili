<?php
namespace Common\Api;

/**
 * Class StatisticsApi
 * @package Common\Api
 * 统计数据处理接口
 * 线型/柱形
 * 饼状
 */
class StatisticsApi {


    /************************************* 时间段统计 ****************************************************/
    /**
     * 获取线型/柱形统计数据  根据日期
     * @param array $date_param 时间参数
     * @param array $parameter  相关参数 包含 (标题  查询条件  对象)
     * @return mixed
     */
    public static function getLineDataByDate($date_param = array(), $parameter = array()) {
        //起始时间 时间戳转化
        $start_time = strtotime($date_param['start_date']);
        $end_time   = strtotime($date_param['end_date']);
        //获取每个点的时间筛选区间
        $between    = self::_getBetween($start_time, $end_time, $date_param['unit']);
        //循环参数  where  reckon(计算方式 与 字段)  obj
        foreach($parameter as $param) {
            //但前时间段 每个时间点的统计
            $counts = array();
            //循环每个时间点
            for($i = 0; $i < count($between); $i++) {
                //获取缓存
                $cache = S($param['cache'] . '_' . $date_param['unit']);
                //如果不存在缓存  或者  该时间段没有缓存  则查询数据库
                if (empty($cache) || empty($cache[$between[$i][0] . $between[$i][1]])) {
                    //当前时间段
                    $param['where']['create_time'] = array('between', $between[$i][0] . "," . $between[$i][1]);
                    //获取该条件下该点的总数统计
                    $count = self::_getCount($param);
                    //存在缓存名称  并且当前时间 大于 时间段的大值  设置缓存
                    if (!empty($param['cache']) && time() > $between[$i][1]) {
                        $cache[$between[$i][0] . $between[$i][1]] = $count;
                        S($param['cache'] . '_' . $date_param['unit'], $cache);
                    }
                } else {
                    //存在缓存
                    $count = $cache[$between[$i][0] . $between[$i][1]];
                }
                $counts[] = $count;
            }
            //添加标题
            $result[$param['title']] = $counts;
        }
        return $result;
    }

    /**
     * 创建横坐标
     * @param $date_param
     * @return array
     */
    public static function createXByDate($date_param) {
        //起始时间 转化为时间戳
        $start_time = strtotime($date_param['start_date']);
        $end_time   = strtotime($date_param['end_date']);
        //获取每个点的时间筛选区间
        $between    = self::_getBetween($start_time,$end_time,$date_param['unit']);
        //创建横坐标显示
        $x          = "";
        if($date_param['unit'] == 'd') {
            for ($i = 0; $i < count($between); $i++) {
                //每天的日期 底部横坐标显示
                $x .= "'" . date('m-d', $between[$i][0]) . "',";
            }
        }
        if($date_param['unit'] == 'm') {
            for($i = 0; $i < count($between); $i++) {
                //每月的日期 底部横坐标显示
                $x .= "'" . date('Y-m',$between[$i][0]) . "',";
            }
        }
        if($date_param['unit'] == 'q') {
            for($i = 0; $i < count($between); $i++) {
                //每季度的日期 底部横坐标显示
                $x .= "'" . date('Y',$between[$i][0]) . "/" . (($i % 4) + 1) . "季度',";
            }
        }
        if($date_param['unit'] == 'y') {
            for($i = 0; $i < count($between); $i++) {
                //每年的日期 底部横坐标显示
                $x .= "'" . date('Y',$between[$i][0]) . "',";
            }
        }
        //step为相隔区间 隐藏的横坐标数量  当横坐标点太多时有效
        return array('x'=>substr($x,0,-1),'step'=>floor(count($between)/10));
    }

    /**
     * @param $start_time
     * @param $end_time
     * @param $unit
     * @return array
     * 时间区间
     */
    private static function _getBetween($start_time,$end_time,$unit) {
        //以天为单位
        if($unit == 'd') {
            //总天数 下同
            $amount = ($start_time == $end_time) ? 1 : ($end_time - $start_time) % 86400 > 0 ? floor(($end_time - $start_time) / 86400) + 1 : floor(($end_time - $start_time) / 86400);
            for($i = 0; $i < $amount; $i++) {
                //每个点的时间区间  下同
                $between[] = array(strtotime('+' . $i . ' Day',$start_time),strtotime('+' . ($i + 1) . ' Day',$start_time));
            }
        }
        //以月为单位
        if($unit == 'm') {
            $amount = ($start_time == $end_time) ? 1 : ceil(($end_time - $start_time) / 2678400);
            for($i = 0; $i < $amount; $i++) {
                $between[] = array(strtotime('+' . $i . ' Month',$start_time),strtotime('+' . ($i + 1) . ' Month',$start_time));
            }
        }
        //以季度为单位
        if($unit == 'q') {
            $amount = ($start_time == $end_time) ? 4 : (date('Y',$end_time) - date('Y',$start_time)) * 4;
            for($i = 0; $i < $amount; $i++) {
                $between[] = array(strtotime('+' . ($i * 3) . ' Month',$start_time),strtotime('+' . (($i + 1) * 3) . ' Month',$start_time));
            }
        }
        //以年为单位
        if($unit == 'y') {
            $amount = ($start_time == $end_time) ? 1 : date('Y',$end_time) - date('Y',$start_time);
            for($i = 0; $i < $amount; $i++) {
                $between[] = array(strtotime('+' . ($i * 12) . ' Month',$start_time),strtotime('+' . (($i + 1) * 12) . ' Month',$start_time));
            }
        }
        return $between;
    }



    /***************************************** 字段统计 ****************************************************************/
    /**
     * 获取线型/柱形统计数据  根据某个字段获取统计
     * @param array $field_param 字段参数
     * @param array $parameter  相关参数 包含 (标题  查询条件  对象)
     * @return mixed
     */
    public static function getLineDataByField($field_param = array(), $parameter = array()) {
        //循环参数  where  reckon(计算方式 与 字段) obj
        foreach($parameter as $param) {
            //每个点的统计
            $counts = array();
            //循环每个点
            for($i = 0; $i < count($field_param); $i++) {
                //获取缓存
                $cache = S($param['cache']);
                //如果不存在缓存  或者  该时间段没有缓存  则查询数据库
                if (empty($cache) || empty($cache[$field_param[$i]['value']])) {
                    //当前字段对应值
                    $param['where'][$param['field']] = $field_param[$i]['value'];
                    //获取该条件下该点的总数统计
                    $count = self::_getCount($param);
                    //存在缓存名称  存在设置缓存
                    if (!empty($param['cache'])) {
                        $cache[$field_param[$i]['value']] = $count;
                        S($param['cache'], $cache);
                    }
                } else {
                    //存在缓存
                    $count = $cache[$field_param[$i]['value']];
                }
                $counts[] = $count;
            }
            //添加标题
            $result[$param['title']] = $counts;
        }
        return $result;
    }

    /**
     * @param $field_param
     * @return array
     * 创建横坐标 根据字段
     */
    public static function createXByField($field_param) {
        $x = '';
        for ($i = 0; $i < count($field_param); $i++) {
            //每天的日期 底部横坐标显示
            $x .= "'" . $field_param[$i]['x'] . "',";
        }
        //step为相隔区间 隐藏的横坐标数量  当横坐标点太多时有效
        return array('x'=>substr($x,0,-1),'step'=>1);
    }

    /**
     * @param $param
     * @return string
     * 获取统计数量
     */
    private static function _getCount($param) {
        //是否需要函数运算统计
        if (empty($param['reckon'])) {
            //统计总数
            $count = $param['obj']->where($param['where'])->count();
        } else {
            //函数统计  函数计算统计
            $field  = $param['reckon'][0] . "(" . $param['reckon'][1] . ")";
            $reckon = $param['obj']->where($param['where'])->getField($field);
            $count  = sprintf("%.2f", $reckon);
        }
        return $count;
    }

    /**
     * 折线/柱形参数处理
     * 2014-6-3
     * @param $data 数据格式   $data["商铺统计 **折线名称**"] = array(4,5,...)数组中存入每个时间段的统计数量;
     * @return string
     */
    public static function createLine($data){
        //创建折线参数字符串
        $line = '';
        foreach($data as $key => $value) {
            $line_data = '';
            $line     .= "{name: '" . $key . "',data:[";
            foreach($value as $v) {
                $line_data .= $v . ',';
            }
            $line   .= substr($line_data,0,strlen($line_data)-1);
            $line   .= "]},";
        }
        //去除字符串末尾的逗号
        $line = substr($line,0,strlen($line)-1);
        //返回high charts格式的字符串
        return $line;
    }

    /**
     * @param string $cache
     *  @param $parameter
     * @return array
     * 饼状图  获取数据
     */
    public static function getPieData($parameter, $cache  ='') {
        //获取数据 缓存
        $data = S($cache);
        //不存在缓存
        if(empty($data)) {
            //总数
            $sum = 0;
            //每个分区的统计值
            foreach ($parameter as $key => $value) {
                //该部分总数
                $count = $value['obj']->where($value['where'])->count();
                $parameter[$key]['count'] = $count;
                //合计加
                $sum   = $sum + $count;
            }
            //每个分区所占比例
            foreach ($parameter as $value) {
                //每个分区所占比例
                $data_per   = number_format(($value['count'] / $sum) * 100, 1);
                //添加标题
                $data[]     = array($value['title'] . "({$value['count']})", $data_per);
            }
            //存在缓存名称 设置缓存
            !empty($cache) ? S($cache,$data,86400) : '';
        }
        return $data;
    }

    /**
     * 饼状图参数处理
     * 2014-6-3
     * @param $data $data数据格式    $data = array(array('18岁以下 **饼状图分区的标题** ',$per **该分区所占的比例值** ));
     * @return string
     */
    public static function createPie($data) {
        //创建饼状图参数字符串
        $pie = '';
        foreach($data as $key => $value) {
            if($key == 0) {
                $pie .= "{name: '".$value[0]."', y:".$value[1].", sliced:true,selected:true},";
            } else {
                $pie .= "['".$value[0]."', ".$value[1]."],";
            }
        }
        //去除字符串末尾的逗号
        $pie = substr($pie,0,strlen($pie)-1);
        //返回high charts格式的字符串
        return $pie;
    }

    /**
     * @param $request
     * @return array
     * 时间段筛选  设置默认时间段
     */
    public static function setDateParam($request) {
        //日期参数
        $dateParam = array(
            'unit'          => empty($request['unit'])         ? 'd' : $request['unit'],
            'start_date'    => empty($request['start_date'])   ? date('Y-m-d',strtotime('-10 Day')) : $request['start_date'],
            'end_date'      => empty($request['end_date'])     ? date('Y-m-d') : $request['end_date'],
        );
        //月份 默认
        if(I('request.unit') == 'm') {
            $dateParam = array_merge($dateParam,array(
                'start_date'    => empty($request['start_date'])   ? date('Y-m',strtotime('-5 Month',time())) : $request['start_date'],
                'end_date'      => empty($request['end_date'])     ? date('Y-m') : $request['end_date'],
            ));
        }
        //季度 年份 默认
        if(I('request.unit') == 'q' || I('request.unit') == 'y') {
            $dateParam = array_merge($dateParam,array(
                'start_date'    => empty($request['start_date'])   ? date('Y').'-01-01' : $request['start_date'],
                'end_date'      => empty($request['end_date'])     ? date('Y').'-01-01' : $request['end_date'],
            ));
        }
        return $dateParam;
    }
}