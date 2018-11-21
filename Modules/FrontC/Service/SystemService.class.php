<?php
namespace FrontC\Service;

/**
 * Class SystemService
 * @package FrontC\Service
 * 系统部分内容获取服务层
 */
class SystemService extends FrontBaseService {

    /**
     * @param array $param
     * @return array complaint_info cancel_info
     * 获取取消、退款、投诉...原因
     */
    function getReasons($param = array()) {
        switch($param['type']) {
            case 1 : return $this->_refundR(); break;
        }
    }

    /**
     * @return array|mixed
     * 获取退款原因内容
     */
    private function _refundR() {
        //获取缓存数据
        $list = S('RefundReason_Cache');
        //不存在缓存 查找数据库
        if(!$list) {
            $list = M('RefundReason')->field('reason')->select();
            //计入缓存
            S('RefundReason_Cache', $list);
        }
        if(!$list)
            return array();
        return $list;
    }

    /**
     * @return array|mixed
     * 获取银行信息列表
     */
    public function getBanks() {
        //获取缓存数据
        $list = S('Bank_Cache');
        //不存在缓存 查找数据库
        if(!$list) {
            $list = D('Bms/Bank')->getCacheList();
            //计入缓存
            S('Bank_Cache', $list);
        }
        if(!$list)
            return array();
        return $list;
    }

    /**
     * @param array $request
     * @return array
     * 根据标识符获取配置信息
     */
    function getConfig($request = array()) {
        //配置数组
        $configs = array(
            1 => C('SITE_MOBILE'),
            2 => C('WITHDRAW_DESC'),
            3 => C('THDZ'),
            4 => C('THJL'),
            5 => C('TH_BG'),
        );
        //判断标识符是一个还是多个
        $identifiers = false === strpos($request['identifier'], ',') ? $request['identifier'] : explode(',', $request['identifier']);
        //如果是一个
        if(!is_array($identifiers))
            return array($identifiers => empty($configs[$identifiers]) ? '' : $configs[$identifiers]);
        //如果是多个
        $result = array();
        //循环获取
        foreach($identifiers as $ide) {
            $result[$ide] = empty($configs[$ide]) ? '' : $configs[$ide];
        }
        return $result;
    }

    /**
     * @param $pictures
     * @return array
     * 获取图片绝对地址
     */
    function getPictures($pictures) {
        //获取图片
        $files = api('File/getFiles', array($pictures, array('id', 'abs_url')));
        //未查到信息
        if(empty($files))
            return array();
        //取出绝对地址 合成数组
        return array_column($files, 'abs_url');
    }

    /**
     * @param array $files
     * @return string
     */
    function getFileIds($files = array()) {
        if(empty($files))
            return '';
        $pictures = '';
        foreach($files as $file) {
            $pictures .= $file['id'] . ',';
        }
        return substr($pictures, 0, -1);
    }

    /**
     * @param array $request
     * @return bool
     * 意见反馈
     */
    function feedback($request = array()) {
        if(empty($request['content'])) {
            return $this->setServiceInfo('请输入反馈内容！', false);
        }
        if(empty($request['contact'])) {
            return $this->setServiceInfo('请输入联系方式！', false);
        }
        $data = array(
            'contact'       => $request['contact'],
            'content'       => $request['content'],
            'ip'            => get_client_ip(1),
            'create_time'   => NOW_TIME,
        );
        if(!M('Feedback')->data($data)->add()) {
            return $this->setServiceInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setServiceInfo('反馈成功！', true);
    }

    /**
     * @param int $number
     * @return array
     * 根据卡号获取银行信息
     */
    public function getBankByNumber($number = 0) {
        if(empty($number) || strlen($number) < 16)
            return false;
        /**
         * 根据卡号获取验证信息
         * 成功：{"bank":"ABC","validated":true,"cardType":"DC","key":"6228480028161845179","messages":[],"stat":"ok"}
         * 失败：{"validated":false,"key":"622848002816184517","stat":"ok","messages":[{"errorCodes":"CARD_BIN_NOT_MATCH","name":"cardNo"}]}
         */
        $text = file_get_contents('https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo='.$number.'&cardBinCheck=true');
        //转化为数组
        $valid_info = json_decode($text, true);
        //var_dump($valid_info);
        //验证失败
        if($valid_info['validated'] == false)
            return false;
        //通过验证获取银行信息
        $banks = $this->getBanks();
        foreach($banks as $bank) {
            if($bank['bank_short'] == $valid_info['bank']) {
                return $bank;
            }
        }
        return false;
    }
}