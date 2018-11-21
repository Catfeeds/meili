<?php
namespace Common\Api;

/**
 * Class SendApi
 * @package Common\Api
 * 信息发送接口
 */
class SendApi {

    /**
     * @var int
     * 多接收者情况  记录发送成功条数
     */
    public static $success  = 0;

    /**
     * @var int
     * 多接收者情况  记录发送失败条数
     */
    public static $fail     = 0;


    /**
     * @param array $param 组合参数
     *      receiver  接收账号 可多个用“,”隔开
     *      unique_code 模板标识
     *      array replaces  替换数组  键值为要替换的字符标记  值为替换为的值
     *      int type  没有模板标识时使用 发送类型 1短信 2邮件 3站内消息 4app推送消息
     *      string subject  没有模板标识时使用 标题（主要用于 邮件、站内信、推送）
     *      string content  没有模板标识时使用 内容
     *      int sender 发送者 0为系统  或者后台管理员ID
     * @return bool|mixed
     * 发送邮件、短信、站内信、推送 并记录
     */
    public static function sendMsg($param = array()) {
        if(empty($param['unique_code']) && empty($param['type'])) { //没有模板标识的情况 判断是否存在发信类型
            return '未选择发信类型！';
        } if(empty($param['receiver'])) { //判断是否发送对象
            return '发信对象未找到！';
        } if(empty($param['unique_code']) && empty($param['content'])) { //没有模板标识的情况 判断是否存在发信内容
            return '发送内容为空！';
        }
        //判断是否存在模板标识  存在模板标识情况 根据标识获取发信模板信息 调用模板的标题、内容 并根据$params替换  不存在情况直接发送 $subject $content
        if(!empty($param['unique_code'])) {
            //根据标识获取发信模板信息      //TODO 邮件附件
            $tpl = M('SendTemplate')->field('id,type,subject,template,target_rule,param,status')->where(array('unique_code'=>$param['unique_code']))->find();
            if(!$tpl) {
                return '模板不存在！';
            } if($tpl['status'] == 0) {
                return '模板已禁用！';
            }
            //存在模板情况调用模板的标题、内容 并根据$params替换
            $param['type']          = $tpl['type'];
            $param['subject']       = $tpl['subject'];
            $param['content']       = $tpl['template'];
            $param['target_rule']   = $tpl['target_rule'];
            $param['param']         = empty($param['param']) ? $tpl['param'] : $param['param'];
            //替换数组存在  替换赋值
            if(!empty($param['replaces'])) {
                foreach ($param['replaces'] as $key => $rep) {
                    $param['content'] = preg_replace("/{" . $key . "}/i", $rep, $param['content']);
                }
            }
        }
        //创建发信记录参数    //TODO 记录附件ID列表
        $data = array(
            'receiver'      => $param['receiver'],                  //接受者账号
            'sender'        => empty($param['sender']) ? 0 : $param['sender'], //发送者 0系统 其他:管理员ID
            'content'       => $param['content'],                   //发送内容
            'type'          => $param['type'],                      //发送类型
            'template_id'   => empty($tpl['id']) ? 0 : $tpl['id'],  //模板ID
            'create_time'   => NOW_TIME                             //发送时间
        );
        //发送中转
        $res = self::_transfer($param);
        //判断发信是否成功
        if($res === true) { //发送成功
            M('SendLog')->data(array_merge($data, array('status'=>1)))->add(); return true;
        } elseif(is_array($res)) {  //多接收者
            M('SendLog')->data(array_merge($data, array('status'=>1)))->add(); return $res;
        } else { //发送失败
            M('SendLog')->data($data)->add(); return $res;
        }
    }

    /**
     * @param array $param 组合参数
     *      int $type  发信类型
     *      string receiver  接收者 可多个 逗号隔开
     *      string content  内容 必须
     *      string subject  标题 可选
     *      int sender 发送者
     *      null attachment 邮件附件
     * @return array
     * 发送中转  在此处理接收者  和 判断调用哪种发送
     */
    private function _transfer($param) {
        //判断$receiver是不是带有“,” 如果有则是发送给多个接收者  没有则是单个接收者
        if(false !== strpos($param['receiver'], ',')) {
            //转换接收者为数组
            $receivers = explode(',', $param['receiver']);
            //接收上一次发送成功条数
            self::$fail     = I('request.fail');
            //接收上一次发送失败条数
            self::$success  = I('request.success');
            //短信 和 推送信息 批量发送
            if($param['type'] == 1 || $param['type'] == 4) {
                $send_res = api('DoSend/' . self::_func($param['type']), array($param));
                //发送失败
                if($send_res !== true) {
                    //失败条数 +count($receivers)
                    self::$fail += count($receivers);
                    //TODO  记录失败账号 原因 时间点等
                    M('SendFailLog')->add(array('receiver' => $param['receiver'], 'content' => $param['content'], 'reason' => $send_res, 'time' => I('request.time'),));
                } else {
                    //否则成功条数 +count($receivers)
                    self::$success += count($receivers);
                }
            } else {
                //循环每个接收者
                for ($i = 0; $i < count($receivers); $i++) {
                    //合并接收者信息
                    $param = array_merge($param, array('receiver' => $receivers[$i]));
                    //如果发送失败  记录失败条数  并做失败记录
                    $send_res = api('DoSend/' . self::_func($param['type']), array($param));
                    //发送失败
                    if ($send_res !== true) {
                        //失败条数 +1
                        self::$fail++;
                        //TODO  记录失败账号 原因 时间点等
                        M('SendFailLog')->add(array('receiver' => $receivers[$i], 'content' => $param['content'], 'reason' => $send_res, 'time' => I('request.time'),));
                        //进入下一循环
                        continue;
                    }
                    //否则成功条数+1
                    self::$success++;
                }
            }
            //返回成功失败条数
            return array('success' => self::$success, 'fail' => self::$fail);
        }
        //发送一条 返回 true or false
        return api('DoSend/' . self::_func($param['type']), array($param));
    }

    /**
     * @param $type
     * @return string
     * 获取发信类型对应的方法名称
     */
    private static function _func($type) {
        switch($type) {
            case 1 : return 'sms';      break;
            case 2 : return 'email';    break;
            case 3 : return 'siteMsg';  break;
            case 4 : return 'push';     break;
        }
    }
}