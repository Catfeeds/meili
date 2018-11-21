<?php

namespace FrontC\Service;

/**
 * Class MemberService
 * @package FrontC\Service
 * 用户相关数据 服务层
 */
class MemberService extends FrontBaseService
{

    /**
     * @param string $account
     * @return mixed|string
     * 手机账号中间四位****替换
     */
    public function accountFormat($account = '')
    {
        if (empty($account))
            return '';
        return substr_replace($account, '****', 3, 4);
    }

    /**
     * @param int $head
     * @return string
     * 获取头像地址
     */
    function getHead($head = 0)
    {
        //判断是否存在头像
        if (empty($head)) {
            $head = C('FILE_HOST') . 'Uploads/Head/default.jpg';
        } else {
            $file = api('File/getFiles', array($head, array('abs_url')));
            $head = $file[0]['abs_url'];
        }
        return $head;
    }

    /**
     * [center获取个人信息调用]
     * @param string $account
     * @param int $m_id
     * @return bool|mixed
     * 获取用户信息
     */
    function getInfo($account = '', $m_id = 0)
    {
        if (empty($account) && empty($m_id))
            return false;
        if ($account)
            $where['account'] = $account;
        if ($m_id)
            $where['id'] = $m_id;

        $info = M('Member')->where($where)->field('id m_id,account,head,nickname,balance,profit,pay_pass,member_sn')->find();
        if (!$info)
            return false;
        //获取头像
        $info['head'] = $this->getHead($info['head']);
        //账号格式化
        $info['account_format'] = $this->accountFormat($info['account']);
        //支付密码
        $info['is_pay_pass'] = empty($info['pay_pass']) ? '0' :'1';
        unset($info['pay_pass']);
        //二维码
        if (!is_file('./Uploads/Member/Code/' . MD5($info['member_sn']) . '.png')) {
            vendor('phpQrcode.phpqrcode');
            \QRcode::png($info['member_sn'], './Uploads/Member/Code/' . MD5($info['member_sn']) . '.png', QR_ECLEVEL_L, 10, 1, true);
        }
        $info['code_url'] = C('FILE_HOST') . 'Uploads/Member/Code/' . MD5($info['member_sn']) . '.png';

        //获取绑定账号信息
        //$info['qq'] = M('Interconnect')->where(array('m_id'=>$m_id,'platform'=>1))->count();
        //$info['wx'] = M('Interconnect')->where(array('m_id'=>$m_id,'platform'=>4))->count();

        return $info;
    }
}