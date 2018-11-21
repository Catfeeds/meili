<?php
namespace Bms\Logic;

/**
 * Class ToUsersLogic
 * @package Bms\Logic
 * 给用户发送信息、优惠券等 批量发送 逻辑层
 */
class ToUsersLogic extends BmsBaseLogic {

    /**
     * @var int
     * 每次查询条数
     */
    public $pageSize    = 50;

    /**
     * @var array
     * 筛选条件
     */
    public $where       = array();

    /**
     * @var string
     * 排序
     */
    public $order       = '';

    /**
     * @var mixed|string
     * 数据模型
     */
    public $model       = '';

    /**
     * 初始化
     */
    public function _initialize() {
        //模型名称 访问其他模块 MsC:Member
        $this->model = str_replace(':', '/', I('request.model'));
        //筛选条件
        $this->where = $this->_proWhere(I('request.'));
        //排序
        $this->order = $this->_proOrder(I('request.'));
    }

    /**
     * @param array $request
     * @return array
     * 获取接收者列表
     */
    function getReceivers($request = array()) {
        return array('list' => D($this->model, 'Logic')->getToUsersList($this->where, $this->order, 15 * ($request['p'] - 1), 15));
    }

    /**
     * @param array $request
     * @return array
     * 发送操作  返回进度  进度是根据  当前页号和总页数来算的
     */
    function toUsers($request = array()) {
        //记录总数
        $count  = empty($request['count']) ? D($this->model,'Logic')->getToUsersCount($this->where) : $request['count'];
        //页号
        $p      = $request['p'];
        //返回数据格式
        $return = array (
            'p'       => 1,      //页号
            'count'   => $count, //接收者记录总数
            'rate'    => 100,    //进度
            'status'  => 0,      //发送状态
            'log_num' => array('success' => 0,'fail' => 0), //成功失败记录条数
        );
        if(!$count) {
            $return['info']     = '未查到接收对象！';
        }
        //总页数
        $pages = ceil($count / $this->pageSize);
        //判断 当前页数 与 总页数关系  如果小于等于总页数  继续发送  否则发送完成
        if($p <= $pages) {
            //获取发信 响应信息
            if($request['do'] == 'give') {
                $return = $this->_give($return,$p,$request);
            } else {
                $return = $this->_send($return,$p,$request);
            }
            //返回信息
            return array_merge($return, array('p' => $p + 1, 'rate' => ($p / $pages) * 100));
        }
        return $return;
    }

    /**
     * @param $return
     * @param $p
     * @param $request
     * @return mixed
     * 发送信息方法
     */
    private function _send($return, $p, $request) {
        //获取 id/mobile/email的字符串
        $ime = $this->_getIME(D($this->model,'Logic')->getToUsersList($this->where, $this->order, $this->pageSize * ($p - 1), $this->pageSize), $request['type']);
        //发送内容  短信 站内信需过滤html
        $content = $request['type'] == 1 || $request['type'] == 3 || $request['type'] == 4 ? filter_html($_REQUEST['content']) : $_REQUEST['content'];
        //发送 参数：接收者 模板标识 替换参数 发送者（管理员ID） 发送内容 发信类型  标题
        $param = array_merge(I('request.'), array(
            'receiver'  => $ime,
            'user_type' => get_user_type($this->model),
            'content'   => $content,
            'sender'    => AID, //发送者
        ));
        //$send_res = Api('Send/sendMsg',array($ime, $request['unique_code'], array(), AID, $content, $request['type'], $request['subject']));
        $send_res = Api('Send/sendMsg',array($param));
        //判断发送结果
        if($send_res === true) { //发送一条
            $return['status']   = 1;
            $return['info']     = '发送成功！';  //提示信息
        } elseif(is_array($send_res)) { //发送多条
            $return['status']   = 1;
            $return['log_num']  = $send_res;
            $return['info']     = '发送成功：' . $send_res['success'] .'　--　发送失败：' . $send_res['fail'];
        } else { //失败
            $return['info']     = $send_res;
        }
        return $return;
    }

    /**
     * @param $return
     * @param $p
     * @param $request
     * @return array
     * 赠送优惠券方法
     */
    private function _give($return, $p, $request) {
        //模板优惠券
        if(!empty($request['unique_code'])) {
            //获取优惠券信息
            $param = M('Coupon')->where(array('unique_code'=>$request['unique_code']))->field('unique_code,goods_cate_id,face_value,use_condition,effective_date,valid_term')->find();
            if(!$param) {
                $return['info'] = '优惠券模板不存在！'; return $return;
            }
        } else {
            if(empty($request['face_value'])) {
                $return['info'] = '请输入优惠券面值！'; return $return;
            } if(empty($request['use_condition'])) {
                $return['info'] = '请输入可用条件！'; return $return;
            } if(empty($request['valid_term'])) {
                $return['info'] = '请输入有效期！'; return $return;
            }
            $param = array(
                'unique_code'       => '',
                'goods_cate_id'     => $request['goods_cate_id'],
                'face_value'        => $request['face_value'],
                'use_condition'     => $request['use_condition'],
                'effective_date'    => strtotime($request['effective_date']),
                'valid_term'        => $request['valid_term'],
            );
        }
        //获取 id/mobile/email的字符串
        $ime = $this->_getIME(D($this->model,'Logic')->getToUsersList($this->where, $this->order, $this->pageSize * ($p - 1), $this->pageSize), 3);
        $ids = explode(',', $ime);

        foreach($ids as $id) {
            $effective_date = empty($param['effective_date']) ? NOW_TIME : $param['effective_date']; //生效日期
            $invalid_date   = $effective_date + $param['valid_term'] * 86400; //失效日期
            $data[] = array_merge($param, array(
                'm_id'              => $id,
                'effective_date'    => $effective_date,
                'invalid_date'      => $invalid_date,
                'create_time'       => NOW_TIME,
            ));
        }

        if(!M('MemberCoupon')->addAll($data)) {
            $return['info']     = '系统繁忙，稍后重试！';  //提示信息
        } else {
            $return['status']   = 1;
            $return['info']     = '发送成功！';  //提示信息

            //TODO-SEND 发信
        }
        return $return;
    }

    /**
     * @param $list
     * @param $type
     * @return string
     * 根据发信类型 获取 需要的id/mobile/email的字符串
     *
     */
    private function _getIME($list = array(), $type = 0) {
        //结果用英文逗号隔开
        $ids        = ''; //id字符串
        $mobiles    = ''; //手机号字符串
        $emails     = ''; //电子邮箱字符串
        $pushes     = ''; //推送号
        //循环获取拼接字符串 判空和判格式
        foreach($list as $value) {
            $ids        .= $value['id'] . ',';
            $mobiles    .= !empty($value['mobile']) && preg_match(C('MOBILE'),$value['mobile']) ? $value['mobile']. ','   : '';
            $emails     .= !empty($value['email'])  && preg_match(C('EMAIL'), $value['email'])  ? $value['email'] . ','   : '';
            $pushes     .= $value['id'] . ',';
        }
        //根据发信类型返回相应的字符串 判空
        switch($type) {
            case 1  : $res = !empty($mobiles) ? substr($mobiles, 0, -1) : '';   break;
            case 2  : $res = !empty($emails)  ? substr($emails,  0, -1) : '';   break;
            case 4  : $res = !empty($pushes)  ? substr($pushes,  0, -1) : '';   break;
            case 3  : $res = substr($ids, 0, -1);   break;
            default : $res = '';                    break;
        }
        return $res;
    }

    /**
     * @param array $request
     * @return mixed
     * 根据不同发送规则  获取筛选条件
     * 1 全部
     * 2 当前筛选
     * 3 已选中目标
     */
    private function _proWhere($request = array()) {
        //全部发送
        if($request['receive_rule'] == 1) {
            //从 model的逻辑层获取筛选条件
            $where = D($this->model,'Logic')->getToUsersWhere();
        } elseif($request['receive_rule'] == 2) {  //当前筛选
            //处理表单序列化的参数 解析字符串到变量  去除转义字符
            //获取筛选条件
            $where = D($this->model,'Logic')->getToUsersWhere($this->_parseStr($request['where']));
        } elseif($request['receive_rule'] == 3) {  //已选中对象
            //ID 筛选条件  多对象去除末尾逗号
            $where_arr['id'] = array('IN', false !== strpos($request['where'], ',') ? substr($request['where'], 0, -1) : $request['where']);
            //获取筛选条件
            $where = D($this->model,'Logic')->getToUsersWhere($where_arr);
        }
        //返回筛选条件
        return $where;
    }

    /**
     * @param array $request
     * @return mixed|string
     * 处理排序
     */
    private function _proOrder($request = array()) {
        //处理表单序列化的参数 解析字符串到变量  去除转义字符 &
        $where_arr = $this->_parseStr($request['where']);
        //自定义排序
        if(empty($where_arr['sort']))
            return '';
        else
            return str_replace(':',' ',$where_arr['sort']);
    }

    /**
     * @param string $where
     * 处理表单序列化的参数 解析字符串到数组变量  去除转义字符 &
     */
    private function _parseStr($where = '') {
        parse_str(str_replace('amp;', '', $where), $where_arr);
        return $where_arr;
    }
}