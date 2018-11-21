<?php

class Alipay {
	
	private $bizContent = '';
	public $require = [];
	private $appId = '';
	public $priKey = '';
	private $output = '';

	public function __construct() {

		$this->priKey = 'MIICXAIBAAKBgQDGxUL+pbsBev7GVAmJ6hbLhSCl3YUMNMJucttS5/usQNUwm6LGclrFmghgkwXnUhoRcjpnos93yplh+t6hXk60yrvlDRhr73TueAbKNedixhOFO/2OraNDvfIqwxMalTzk+5XJbExqR9H3WafYVbi3Dyb/2n8TgQTtOfmqisTumwIDAQABAoGAFHzSmfK1IsLtrb2NuiRhsxqGsfHyO77YZ4/5IUN+AlQwZTE1huTUjFeDE4kz65Lf0vqGIJZ8cel1A89a0SEqVXOkh/HqaHoUaar2u4srSMCGflThNXsPFBJVCJy36ofOXiPsLFwHTOWKEEsaQ8p32CKysCcdZSYilvcP8FA37HkCQQDpcb8UqI1RfZ6RCLS3ZVnqGnNAVId2ohw+6ZBKEcyZmn6CMWP0r1Xqyl9ZL5oFQuOuvqBF8r60CSt4eefUibb3AkEA2fnbv+FIcoBcPiR0i7Py9iaqsn82k7god0xnn4b1/qHVd0vAhzPXDP2rvYVcMBv8IlZ0DXWe1UFLrejWTJAofQJAJ2DPmb2A8SEekVqFmXYYP7wsesqHe0SHPTmK5GOyPqrn8jBAqzK0bIGsqc+0zHRnEcAIKyRydM4jLhRqPdjpKwJABrOu9PxZPOQDcgmu56i1vKm9r9VHeU09OUXJHdeJcrXJGWzj04RdhVG7WQ1jozsJCok78jn+kzH5wLQa+qmoBQJBAIVCM/YgpUxJz5x34seiVZ7vBL/nxVPvzkKeL7/SMDGP5FXL/IMTurH93QbOgY3heGefsMqBLMfYdX/sLdgMGWc=';
        $this->appId  = '2017070307629148';

	}

	public function init() {

		$set = [
			'app_id' => $this->appId,
			'biz_content' => [
				'body' => '',
//				'disable_pay_channels' => '',
//				'enable_pay_channels' => '',
//				'extend_params' => '',
//				'goods_type' => '1',
				'out_trade_no' => '',  // bill_sn
//				'passback_params' => '',  // must url encoded
				'product_code' => 'QUICK_MSECURITY_PAY', // default.
//				'promo_params' => '',
//				'seller_id' => '', // seller ali pay id
				'subject' => '',
				'timeout_express' => '10m', // pay last time
				'total_amount' => '', // amount price (after_price)
			],
			'charset' => 'utf-8', // defaulted.
			// 'format' => 'JSON',  // only setting
			'method' => 'alipay.trade.app.pay',
			'notify_url' => '',  // async notice
			// 'sign' => sha1($this->priKey),
			'sign_type' => 'RSA',  // only setting
			'timestamp' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
			'version' => '1.0',
		];
		// sort($set);
		$this->require = $set;
		
	}

	public function method($method = 'alipay.trade.app.pay') {
		$this->require['method'] = $method;
	}

	public function notify($link = '') {
		$this->require['notify_url'] = $link;
	}

	public function bizContent($body, $subject, $bill_sn, $amount, $goods_type = 1) {
		// $biz = $this->require['biz_content'];
		$this->require['biz_content']['body'] = $body;
		$this->require['biz_content']['subject'] = $subject;
		$this->require['biz_content']['out_trade_no'] = $bill_sn;
		
		$this->require['biz_content']['total_amount'] = $amount;
		
		// $this->require['biz_content']['total_amount'] = 0.01;
		
		// $this->require['biz_content']['goods_type'] = $goods_type;
	}

	/**
	 * tiled into the link parameter string
	 */
	public function setLink($isUrlEncode = true) {
		$this->output = $this->_createLinkstring02($this->require, $isUrlEncode);
	}

	public function output() {
		return $this->output;
	}

	// +---------------------------------------------
	// | private
	// +---------------------------------------------

	/**
	 * set a link string not have a sign of key. as key=value&key=value
	 */
	private function _createLinkstring02($valueMap, $isUrlEncode = true) {
		$content = '';
	    $i = 0;
	    foreach($valueMap as $key=>$value) {
			$flag = true;
	        if($key != "sign" ) {
	        	if(is_array($value)) {
	        		$value = $this->_encode_json($value);
					// $flag = false;
					// $value = json_encode($value);
	        	}
				if(!$isUrlEncode) {
					$value = urlencode($value);
				}
				if(!$flag) {
					$content .= ($i == 0 ? '' : '&').$key.'="'.$value.'"';
				} else {
					$content .= ($i == 0 ? '' : '&').$key.'='.$value;
				}
	        }
	        $i++;
	    }
	    return $content;
	}

	/**  
	 * json encode not include chinese code.
	 */
	private function _encode_json($str) {
        $strs = urldecode(json_encode($this->_url_encode($str)));
        return $strs;
    }
    private function _url_encode($str) {
        if(is_array($str)) {
            foreach($str as $key=>$value) {
                $str[urlencode($key)] = $this->_url_encode($value);
            }
        } else {
            $str = urlencode($str);
        }
        return $str;
    }
	
}