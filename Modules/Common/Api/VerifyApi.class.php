<?php
namespace Common\Api;

/**
 * Class VerifyApi
 * @package Common\Api
 * 验证码操作接口
 */
class VerifyApi {

    /**
     * @param string $account     用户账号(手机号、邮箱)
     * @param string $unique_code  发送时机标识 与 发信模板的unique_code对应
     * @return mixed|string
     * 获取短信验证码 并发送
     */
    public static function getVerify($account = '', $unique_code = '') {
        //验证号码合法性
        $check = self::_checkAccount($account);
        if(!$check)
            return '账号格式不正确！';
        //判断标识是否为空
        if(empty($unique_code))
            return '标识为空！';
        //如果是注册的情况要验证账号是否已经被注册，其他情况需验证账号是否存在
        $member = M('Member')->where(array('account'=>$account))->count();
        //注册验证码 先验证账号是否已经注册
        if($unique_code == 'register') {
            //验证账号是否已经注册
            if($member)
                return '该账号已经注册！';
            //没有注册过
            $member = true;
        }
        //账号不存在
        if(!$member)
            return '您输入的账号不存在！';
        //是否进行过此操作
        $verify = M('Verify')->where(array('account'=>$account, 'unique_code'=>$unique_code))->find();
        //验证码
        $vc     = get_vc(6,2);
        //数据
        $data = array(
            'verify'        => $vc,
            'expire_time'   => NOW_TIME + 600, //过期时间
            'times'         => 1, //次数
            'create_time'   => NOW_TIME,
        );
        //是否存在验证码记录
        if($verify) {
            //每天只能进行三次操作
            if($verify['create_time'] > strtotime(date('Y-m-d')) && $verify['create_time'] < strtotime(date('Y-m-d 23:59:59')) && intval($verify['times']) % 3 == 0){
                return '操作次数超限！';
            } else {
                if($verify['create_time'] < strtotime(date('Y-m-d')))
                    $times = 1; //后一天操作  次数置一 否则次数加一
                else
                    $times = intval($verify['times']) + 1; //操作次数加一
                //修改记录
                $result = M('Verify')->where(array('id'=>$verify['id']))->data(array_merge($data, array('times'=>$times)))->save();
            }
        } else {
            //添加记录
            $result = M('Verify')->data(array_merge($data, array('account'=>$account,'unique_code'=>$unique_code)))->add();
        }
        if($result){
            //发送信息并记录
            return api('Send/sendMsg', array(['receiver'=>$account,'unique_code'=>$unique_code,'replaces'=>array('vc' => $vc)]));
        } else {
            return '获取验证码失败！';
        }
    }

    /**
     * @param string $account
     * @return string
     * 验证账号类型(手机号 邮箱)
     */
    private static function _checkAccount($account = '') {
        if(preg_match(C('MOBILE'), $account)) {
            return 'phone';
        }
//        if(preg_match(C('EMAIL'), $account)) {
//            return 'email';
//        }
        return false;
    }

    /**
     * @param string $account
     * @param string $verify
     * @param string $unique_code
     * @return bool|string
     * 验证短信验证码 存在、是否使用、有效期
     */
    public static function checkVerify($account = '', $verify = '', $unique_code = '') {
        $where['account']       = $account;
        $where['verify']        = $verify;
        $where['unique_code']   = $unique_code;
        $verify = M('Verify')->field('expire_time,status')->where($where)->find();
        //判断是否存在 并且未使用
        if($verify && $verify['status'] != 1) {
            //是否过期
            if($verify['expire_time'] < NOW_TIME)
                return '验证码已过期！';
            return true;
        } else {
            return '验证码无效！';
        }
    }

    /**
     * @param $verify
     * @return bool
     * 销毁验证码 （置为已使用）
     */
    public static function destroyVerify($verify) {
        return M('Verify')->where(array('verify'=>$verify))->setField('status',1);
    }
}