<?php
namespace Dock\Order;

/**
 * Class OrderEntity
 * @package Dock\Order
 * 订单对象
 */
class OrderEntity
{
    /**
     * @var string
     * 订单编号，不可空，字符型
     */
    protected $orderNo      = '';

    /**
     * @var string
     * 订单摘要，字符型
     */
    protected $summary      = '';

    /**
     * @var string
     * 支付方式，不可空，字符型，取值范围 支付宝:"AP"，微信:"WP",现金:"CP",其他:"OP"
     */
    protected $payMode      = '';

    /**
     * @var string
     * 总价，不可空，数值型
     */
    protected $totalPrice   = 0.00;

    /**
     * @var string
     * 实际支付，不可空，数值型
     */
    protected $realPay      = 0.00;

    /**
     * @var string
     * 支付时间，形如：2017-06-26 13:13:02，不可空，时间型
     */
    protected $payTime      = '';

    /**
     * @var string
     * 备注，字符型
     */
    protected $remark       = '';

    /**
     * @var string
     * 送货方式，不可空，字符型，取值范围 公司配送:"C",自取:"S",快递:"E"
     */
    protected $receiveType  = '';

    /**
     * @var string
     * 客户姓名，不可空，字符型
     */
    protected $customerName = '';

    /**
     * @var string
     * 联系电话，不可空，字符型
     */
    protected $mobileNum    = '';

    /**
     * @var string
     * 邮政编码，字符型
     */
    protected $postcode     = '';

    /**
     * @var string
     * 省份，字符型
     */
    protected $province     = '';

    /**
     * @var string
     * 城市，字符型
     */
    protected $city         = '';

    /**
     * @var string
     * 行政区，字符型
     */
    protected $district     = '';

    /**
     * @var string
     * 详细地址，不可空，字符型
     */
    protected $address      = '';

    /**
     * @var array
     * 订单产品，数组型
     */
    protected $orderProducts = [];

    /**
     * @var null
     * 对象实例
     */
    public static $instance = null;

    /**
     * @param array $options
     * 构造方法
     */
    public function __construct($options = [])
    {

    }

    /**
     * @param array $options
     * @return null|static
     * 单例初始化
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * @param string $order_no
     * 设置订单编号属性
     */
    public function setOrderNo($order_no = '')
    {
        $this->orderNo = $order_no;
    }

    /**
     * @return string
     * 获取订单编号属性
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * @param string $summary
     * 设置订单摘要属性
     */
    public function setSummary($summary = '')
    {
        $this->summary = $summary;
    }

    /**
     * @return string
     * 获取订单摘要属性
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $pay_mode
     * 设置支付方式属性
     */
    public function setPayMode($pay_mode = '')
    {
        $this->payMode = $pay_mode;
    }

    /**
     * @return string
     * 获取支付方式属性
     */
    public function getPayMode()
    {
        return $this->payMode;
    }

    /**
     * @param float $total_price
     * 设置订单总价属性
     */
    public function setTotalPrice($total_price = 0.00)
    {
        $this->totalPrice = $total_price;
    }

    /**
     * @return string
     * 获取订单总价属性
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param float $real_pay
     * 设置实际支付属性
     */
    public function setRealPay($real_pay = 0.00)
    {
        $this->realPay = $real_pay;
    }

    /**
     * @return string
     * 获取实际支付属性
     */
    public function getRealPay()
    {
        return $this->realPay;
    }

    /**
     * @param string $pay_time
     * 设置支付时间属性
     */
    public function setPayTime($pay_time = '')
    {
        $this->payTime = $pay_time;
    }

    /**
     * @return string
     * 获取支付时间属性
     */
    public function getPayTime()
    {
        return $this->payTime;
    }

    /**
     * @param string $remark
     * 设置备注属性
     */
    public function setRemark($remark = '')
    {
        $this->remark = $remark;
    }

    /**
     * @return string
     * 获取备注属性
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $receive_type
     * 设置送货方式属性
     */
    public function setReceiveType($receive_type = '')
    {
        $this->receiveType = $receive_type;
    }

    /**
     * @return string
     * 获取送货方式属性
     */
    public function getReceiveType()
    {
        return $this->receiveType;
    }

    /**
     * @param string $customer_name
     * 设置客户姓名属性
     */
    public function setCustomerName($customer_name = '')
    {
        $this->customerName = $customer_name;
    }

    /**
     * @return string
     * 获取客户姓名属性
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @param string $mobile_num
     * 设置联系电话属性
     */
    public function setMobileNum($mobile_num = '')
    {
        $this->mobileNum = $mobile_num;
    }

    /**
     * @return string
     * 获取联系电话属性
     */
    public function getMobileNum()
    {
        return $this->mobileNum;
    }

    /**
     * @param string $postcode
     * 设置邮政编码属性
     */
    public function setPostcode($postcode = '')
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     * 获取邮政编码属性
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $province
     * 设置省份属性
     */
    public function setProvince($province = '')
    {
        $this->province = $province;
    }

    /**
     * @return string
     * 获取省份属性
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param string $city
     * 设置城市属性
     */
    public function setCity($city = '')
    {
        $this->city = $city;
    }

    /**
     * @return string
     * 获取城市属性
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $district
     * 设置行政区属性
     */
    public function setDistrict($district = '')
    {
        $this->district = $district;
    }

    /**
     * @return string
     * 获取行政区属性
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param string $address
     * 设置详细地址属性
     */
    public function setAddress($address = '')
    {
        $this->address = $address;
    }

    /**
     * @return string
     * 获取详细地址属性
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param array $order_products
     * 设置订单产品属性
     */
    public function setOrderProducts($order_products = [])
    {
        $this->orderProducts = $order_products;
    }

    /**
     * @return array
     * 获取订单产品属性
     */
    public function getOrderProducts()
    {
        return $this->orderProducts;
    }

    /**
     * @param null $order 订单数据对象
     * @return array
     * @throws \Exception
     * 返回订单数据数组
     */
    public function order2array($order = null)
    {
        if (empty($order)) {
            throw new \Exception('未找到订单对象！');
        }
        $order_array = [
            "OrderNo"       => $order->getOrderNo(),
            "Summary"       => $order->getSummary(),
            "PayMode"       => $order->getPayMode(),
            "TotalPrice"    => $order->getTotalPrice(),
            "RealPay"       => $order->getRealPay(),
            "PayTime"       => $order->getPayTime(),
            "Remark"        => $order->getRemark(),
            "ReceiveType"   => $order->getReceiveType(),
            "CustomerName"  => $order->getCustomerName(),
            "MobileNum"     => $order->getMobileNum(),
            "Postcode"      => $order->getPostcode(),
            "Province"      => $order->getProvince(),
            "City"          => $order->getCity(),
            "District"      => $order->getDistrict(),
            "Address"       => $order->getAddress(),
            "OrderProducts" => $order->getOrderProducts(),
        ];

        return $order_array;
    }

    /**
     * @param null $order 订单数据对象
     * @return string
     * @throws \Exception
     * 返回订单数据json
     */
    public function order2json($order = null)
    {
        if (empty($order)) {
            throw new \Exception('未找到订单对象！');
        }

        return json_encode($this->order2array($order));
    }
}