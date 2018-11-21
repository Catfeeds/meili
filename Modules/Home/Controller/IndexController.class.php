<?php
namespace Home\Controller;
use Think\Exception;

/**
 * Class IndexController
 * @package Home\Controller
 * 首页控制器
 */
class IndexController extends HomeBaseController {


	public function _initialize(){
        parent::_initialize();
	}



    function inQueue() {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        //$redis->lRem("call_goods");

        //$good = M('Goods')->where(array('id'=>33))->field('stock')->find();
        $redis->delete('call_goods');
        $good['stock'] = 5;
        for($i=0;$i<$good['stock'];$i++) {
            $redis->rPush("call_goods", ($i+1));
        }
        //$timestamp = NOW_TIME . get_vc(4, 2);        5 4 3 2 1

        //$redis->lPush("call_goods", $timestamp);

        //$t = $redis->rPop("call_goods");
        $count = $redis->lLen("call_goods");

        var_dump($count);

//        if($t) {
//            $good = M('Goods')->where(array('id'=>33))->field('stock')->find();
//
//            if($good['stock'] < 1) {
//                echo '库存不足！';
//            } else {
//                echo "1<br>";
//                $res = M('Goods')->where(array('id'=>33))->setDec('stock');
//            }
//        }


        $redis->close();

    }

    function test() {
        //连接本地的 Redis 服务
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);

        //$redis->delete('call_goods');

        //$count = $redis->lLen("call_goods");

        //var_dump($count);

        //$res = $redis->lPop('call_goods');

        $res = $redis->lIndex('call_goods', 0);

        var_dump($res);


    }

    function queueTest() {
        //echo 'home';
        header('Content-type:text/html; charset=utf-8');
        //连接本地的 Redis 服务
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);


        $t = $redis->rPop("call_goods");

        if(false !== $t) {
//            $good = M('Goods')->where(array('id'=>33))->field('stock')->find();
//
//            if($good['stock'] < 1) {
//                echo '库存不足！';
//            } else {
//                echo "1<br>";
            $res = M('Goods')->where(array('id'=>33))->setDec('stock');
            //}
        } else {
            echo '库存不足！';
        }
        // 获取现有消息队列的长度
        // $count = 0;
        //self::$count = $redis->lLen("call_goods");

        //var_dump(self::$count);

        // 回滚数组
        //$roll_back_arr = array();

        //$t = $redis->rPop("call_goods");

        //var_dump($t);
        /*while (self::$count) {

            $t = $redis->rPop("call_goods");
            self::$count = $redis->lLen("call_goods");
            //var_dump(self::$count);

            $roll_back_arr[] = $t;

            //echo "1<br>";

            $good = M('Goods')->where(array('id'=>33))->field('stock')->find();

            if($good['stock'] < 1) {
                echo '库存不足！';
            } else {
                echo "1<br>";
                $res = M('Goods')->where(array('id'=>33))->setDec('stock');
            }
        }*/

        // 释放redis
        $redis->close();
    }

    public static $count = 0;
    /**
     * 首页
     */
    public function index(){
        //var_dump(sprintf("%.2f", C('PROFIT_FEE')));
        //var_dump(sprintf("%.2f", 100 * (floatval(C('PROFIT_FEE')) / 100)));

        //$file = fopen('./log.txt', 'a+');
        //fwrite($file, self::$count."nihao\r\n");,consignee
        //fclose($file);

        header('content-type:text/html;charset=utf-8');
echo 1;
//        $order_info = M('OrderInfo')->where(array('id'=>27))->field('id order_id,order_sn,province_name,city_name,area_name,address,mobile,order_amounts,pay_amounts,payment,status')->find();
//
//        $result = D('FrontC/OrderInfo','Service')->dockOrder($order_info);
//
//        var_dump($result);
//
//        $str = '{"OrderNo":"A14982012240318","Summary":"","PayMode":"AP","TotalPrice":"0.06","RealPay":"0.06","PayTime":"2017-06-27 15:04:45","Remark":"","ReceiveType":"E","CustomerName":"\u534e\u4e1c","MobileNum":"13527457487","Postcode":"","Province":"","City":"","District":"","Address":"\u91cd\u5e86\u91cd\u5e86\u5357\u5cb8\u533a\u4e07\u8fbe\u5e7f\u573a\u94ed\u90b811\u697c","OrderProducts":[{"ProductNo":"sn0003","ProductName":"\u8f6f\u8f6f\u7684\u5927\u72d7\u718a","Quantity":"1","Unit":"\u4e2a","Price":"0.01"},{"ProductNo":"sn0004","ProductName":"\u52a8\u542c\u7684\u97f3\u4e50\u52a8\u542c\u7684\u97f3\u4e50\u76d2","Quantity":"1","Unit":"\u4e2a","Price":"0.01"},{"ProductNo":"sn0002","ProductName":"\u6b63\u7248\u591a\u5566A\u68a6","Quantity":"4","Unit":"\u4e2a","Price":"0.01"}]}';

        //var_dump(json_decode($str, 1));
        //$orderE = \Dock\Order\OrderEntity::instance();

//        $orderE->setOrderNo('A201702031234321');
//        $orderE->setPayMode('AP');
//        $orderE->setTotalPrice(100.00);
//        $orderE->setRealPay(100.00);
//        $orderE->setPayTime(date('Y-m-d H:i:s'));
//        $orderE->setReceiveType('E');
//        $orderE->setCustomerName('周杰伦');
//        $orderE->setMobileNum('13312341234');
//        $orderE->setAddress('南天门');
//        $orderE->setOrderProducts([['ProductNo'=>'sn0001','ProductName'=>'流氓兔','Quantity'=>'2','Unit'=>'个','Price'=>'50']]);
//
//        $order = \Dock\Order\Order::instance();
//
//        try {
//            $result = $order->placeOrder($orderE);
//        } catch(\Exception $e) {
//            echo $e->getMessage();
//        }
//
//        var_dump($result);

        //$this->display('index');
    }

    function __destruct() {

        $file = fopen('./log.txt', 'a+');

        fwrite($file, self::$count."nihao\r\n");

        fclose($file);
    }


    function t1() {
        header('Content-type:text/html; charset=utf-8');
        //连接本地的 Redis 服务
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);

        $redis->delete('k1');
//        for($i=0; $i<5; $i++) {
//            $res = $redis->lPush('k1', $i);
//            var_dump($res);
//        }
//        $i = 0;//exit;
//        while($i < 10) {
//            $i++;
//            $res = $redis->lPush('k1', $i);
//            var_dump($res);
//            //sleep(2);
//        }
// 释放redis
        $redis->close();
    }

    function t2() {
        header('Content-type:text/html; charset=utf-8');
        //连接本地的 Redis 服务
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->delete('k1');
        $redis->delete('call_goods');
        $len = $redis->lLen('k1');

        var_dump($len);

//        $res = $redis->brPop('k1', 10);
//        var_dump($res);
//
//        // 释放redis
//        $redis->close();
//        exit;
//        while(true) {
//            $res = $redis->brPop('k1', 10);
//            echo $res . '<br>';
//            sleep(2);
//        }

        // 释放redis
        $redis->close();
    }
}