<?php

namespace FrontC\Logic;

/**
 * Class CenterLogic
 * @package FrontC\Logic
 * 个人中心 部分功能逻辑层
 */
class CenterLogic extends FrontBaseLogic
{

    /**
     * 获取充值金额
     * */
    public function getRechargeAmounts()
    {
        $result=D('FrontC/RechargeAmounts','Model')->getRows();
        if (empty($result))
            return array();
        return $result;
    }

    /*
     * 用户添加银行卡
     * */
//    public function addBankcard($request = array())
//    {
//        if(empty($request['m_id'])) {
//            return $this->setLogicInfo('请先登录哦！', false);
//        }elseif (empty($request['open_name'])){
//            return $this->setLogicInfo('请正确填写持卡人姓名哦！', false);
//        }elseif (empty($request['card_number'])){
//            return $this->setLogicInfo('请正确填写卡号哦！', false);
//        }elseif (!preg_match('/^\d{14,20}$/', $request['card_number'])){
//            return $this->setLogicInfo('卡号格式错误哦！', false);
//        }elseif (empty($request['mobile'])){
//            return $this->setLogicInfo('请正确填写手机号哦！', false);
//        }elseif (!preg_match('/^0?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[0379])[0-9]{8}$/', $request['mobile'])){
//            return $this->setLogicInfo('手机号格式错误哦！', false);
//        }elseif(empty($request['bank_id'])){
//            return $this->setLogicInfo('请选择银行哦！', false);
//        }
//        $param=array('bank.id'=>$request['bank_id'],'bank.status'=>1);
//        $bank=D('FrontC/Bank','Model')->getRow($param);
//        $data=array(
//            'user_id'=>$request['m_id'],
//            'user_type'=>1,
//            'open_name'=>$request['open_name'],
//            'card_number'=>$request['card_number'],
//            'mobile'=>$request['mobile'],
//            'bank_name'=>$bank['name'],
//            'bank_short'=>$bank['short'],
//            'bank_logo'=>$bank['logo'],
//            'create_time'=>NOW_TIME,
//        );
//        $result=M('UserBankcard')->data($data)->add();
//        if($result){
//            return $this->setLogicInfo('添加成功！', true);
//        }else{
//            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
//        }
//    }

    /**
     * 获取所有银行信息
     * */
    public function getBanks($request = array())
    {
        if (empty($request['m_id'])) {
            return $this->setLogicInfo('请先登录哦！', false);
        }
        $param = array('bank.status' => 1);
        $banks = D('FrontC/Bank', 'Model')->getBanks($param);
        if (!empty($banks)) {
            return $banks;
        } else {
            return $this->setLogicInfo('暂时不支持哦！', true);
        }
    }

    /*
     * 我的信息
     * */
//    public function myInfo($request = array())
//    {
//        //参数判空
//        if(empty($request['m_id'])) {
//            return $this->setLogicInfo('请先登录哦！', false);
//        }
//        $param=array('member.id'=>$request['m_id'],'member.status'=>1);
//        $member_info=D('FrontC/Member', 'Model')->getOneInfo($param);
//        if(!empty($member_info)){
//            return $member_info;
//        }else{
//            return $this->setLogicInfo('系统繁忙，请稍后再试！', false);
//        }
//    }

    /**
     * 意见反馈
     * */
    public function submitOpinion($request = array())
    {
        //参数判空
        if (empty($request['content']) || empty($request['link']) || empty($request['m_id'])) {
            return $this->setLogicInfo('请填写完整信息哦！', false);
        }
        //添加
        $data['content'] = $request['content'];
        $data['link'] = $request['link'];
        $data['m_id'] = $request['m_id'];
        $data['create_time'] = NOW_TIME;
        $data['update_time'] = NOW_TIME;
        $result = M('Opinion')->data($data)->add();
        if (!$result) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        } else {
            return $this->setLogicInfo('反馈成功！', true);
        }
    }

    /**
     * [center获取帮助文档调用]
     * 帮助文档列表
     * */
    public function getHelpDocs()
    {
        $docs = D('FrontC/HelpDoc','Model')->getRows();
        $Page = new \Think\Page(count($docs), 9, $_REQUEST);
        $result = array_slice($docs, $Page->firstRow, $Page->listRows);
        if (empty($result))
            return array();
        return $result;
    }

    /**
     * [center帮助文档详情调用、]
     * 帮助文档详情
     * */
    public function getDocDetail($request = array())
    {
        //参数判空
        if (empty($request['doc_id'])) {
            return $this->setLogicInfo('参数错误哦！', false);
        }
        $param = array('id' => $request['doc_id'], 'status' => 1);
        $doc = D('FrontC/HelpDoc','Model')->getRow($param);
        $doc['content'] = path2abs($doc['content']);
        if (empty($doc['title'])) {
            return array();
        } else {
            return $doc;
        }
    }

    /**
     * [center关于我们调用]
     * */
    public function aboutUs()
    {
        $about_us_one =  D('FrontC/Article','Model')->getRow();;
        $about_us_one['content'] = path2abs($about_us_one['content']);
        if (empty($about_us_one['title'])) {
            return array();
        } else {
            return $about_us_one;
        }

    }

    /**
     * @param array $request
     * @return bool
     * 修改头像
     */
    function modifyHead($request = array())
    {
        //TODO 图片处理  选择即上传  统一上传
        //M('Feedback')->data(array('content'=>$_FILES['head']['name']))->add();
        $result = api('UpDownLoad/upload', array(I('request.')));
        if (TERMINAL == 'wap')
            $result = array_dimension($result) == 1 ? $result : $result['file'];
        if (TERMINAL == 'api')
            $result = array_dimension($result) == 1 ? $result : $result['head'];
        //上传失败返回
        if ($result['status'] == 0) {
            return $this->setLogicInfo($result['info'], false);
        }
        //上传成功 修改头像
        if (!M('Member')->where(array('id' => $request['m_id']))->data(array('head' => $result['id']))->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        } else {
            return array('head' => $result['abs_url']);
        }
    }

    /**
     * @param array $request
     * @return bool
     * 修改信息
     */
    function modifyInfo($request = array())
    {
        //参数判空
        if (empty($request['field']) || empty($request['value'])) {
            return $this->setLogicInfo('参数错误！', false);
        }
        //获取要修改的字段
        $field = $this->_getField($request['field']);
        if (!$field) {
            return $this->setLogicInfo('字段出错！', false);
        }

        $data[$field] = $request['value'];
        $data['update_time'] = NOW_TIME;

        if (!M('Member')->where(array('id' => $request['m_id']))->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('修改成功！', true);
    }

    private function _getField($field = 0)
    {
        switch ($field) {
            case 1:
                return 'nickname';
                break;
            default :
                return '';
                break;
        }
    }

    /**
     * @param array $request
     * @return bool
     * 修改登陆密码
     */
    function modifyPass($request = array())
    {
        //判断参数
        if (empty($request['password'])) {
            return $this->setLogicInfo('请输入原密码！', false);
        }
        if (empty($request['new_password'])) {
            return $this->setLogicInfo('请输入新密码！', false);
        }
        if (strlen($request['new_password']) < 6 || strlen($request['new_password']) > 18) {
            return $this->setLogicInfo('新密码长度在6--18位之间！', false);
        }
        if ($request['re_new_password'] != $request['new_password']) {
            return $this->setLogicInfo('确认新密码与新密码不一致！', false);
        }
        //获取原密码
        $password = M('Member')->where(array('id' => $request['m_id']))->getField('password');
        //验证原密码是否正确
        if (!($password == MD5($request['password']))) {
            return $this->setLogicInfo('原密码不正确！', false);
        }
        //如果未修改
        if ($password == MD5($request['new_password'])) {
            return $this->setLogicInfo('修改成功！', true);
        }
        //修改
        $data['password'] = MD5($request['new_password']);
        $where['id'] = $request['m_id'];
        //判断成败
        if (!M('Member')->where($where)->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('修改成功！', true);
    }

    /**
     *[center修改手机号调用]
     * @param array $request
     * @return bool
     * 修改账号
     */
    function modifyAccount($request = array())
    {
        if (empty($request['account']) || empty($request['new_account']) || empty($request['verify']))
            return $this->setLogicInfo('参数错误！', false);
        //验证原手机号和用户ID的一致性
        if (!M('Member')->where(array('id' => $request['m_id'], 'account' => $request['account']))->count()) {
            return $this->setLogicInfo('非法修改！', false);
        }
        //验证新手机号 验证码 新手机号收验证码 用 register
        $result = api('Verify/checkVerify', array($request['new_account'], $request['verify'], 'register'));
        if ($result !== true) {
            return $this->setLogicInfo($result, false);
        }
        //账号一致 直接修改成功
        if ($request['new_account'] == $request['account']) {
            return $this->setLogicInfo('修改成功！', true);
        }
        $data['account'] = $request['new_account'];
        $data['mobile'] = $request['new_account'];
        $data['update_time'] = NOW_TIME;

        if (!M('Member')->where(array('id' => $request['m_id']))->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('修改成功！', true);
    }

    /**
     * [center设置修改支付密码调用]
     * @param array $request
     * @return bool
     * 修改支付密码
     */
    function modifyPayPass($request = array())
    {
        if (empty($request['pay_pass'])) {
            return $this->setLogicInfo('请输入支付密码！', false);
        }
        if (!preg_match('/^\d{6}$/', $request['pay_pass'])) {
            return $this->setLogicInfo('支付密码格式为6位的数字！', false);
        }
        if ($request['re_pay_pass'] != $request['pay_pass']) {
            return $this->setLogicInfo('确认密码与支付密码不一致！', false);
        }
        //修改
        $data['pay_pass'] = MD5($request['pay_pass']);
        $data['update_time'] = NOW_TIME;
        $where['id'] = $request['m_id'];
        //判断成败
        if (false === M('Member')->where($where)->data($data)->save()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('修改成功！', true);
    }

    /**
     * @param array $request
     * @return bool
     * 签到
     */
    function sign($request = array())
    {
        //判断今天是否已签到
        $where['m_id'] = $request['m_id'];
        $where['sign_in_time'] = array('exp', '>' . strtotime(date('Y-m-d')) . ' and sign_in_time<' . NOW_TIME);
        //判断今天是否已签到  已签到提醒
        if (M('Sign')->where($where)->count()) {
            return $this->setLogicInfo('您今日已签到！', false);
        }
        //添加签到记录
        $data = array(
            'm_id' => $request['m_id'],
            'sign_in_time' => NOW_TIME,
            'sign_in_ip' => get_client_ip(1),
        );
        //是否签到成功
        if (!M('Sign')->data($data)->add()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        //操作用户积分
        D('FrontC/Finance', 'Service')->addItgLog($request['m_id'], 1, 1, C('SIGN_INTEGRAL'));
        //发送站内消息
        $param = array(
            'receiver' => $request['m_id'],
            'unique_code' => 'sign_msg',
            'replaces' => array('itg' => C('SIGN_INTEGRAL')),
        );
        api('Send/sendMsg', array($param));

        return $this->setLogicInfo('签到成功！', true, array('sign_integral' => C('SIGN_INTEGRAL')));
    }

    /**
     * @param array $request
     * @return array
     * 我的积分
     */
    function myIntegrals($request = array())
    {
        //获取用户积分剩余
        $integral = M('Member')->where(array('id' => $request['m_id']))->getField('integral');
        //获取积分记录
        $param['where']['itg_log.m_id'] = $request['m_id'];
        $result = D('FrontC/Finance', 'Service')->integralLogs($param);
        //var_dump($result);
        return array('integral' => $integral, 'list' => $result);
    }

    /**
     * @param array $request
     * @return array
     * 我的优惠券
     */
    function myCoupons($request = array())
    {
        //获取优惠券
        $param['where']['m_cpn.m_id'] = $request['m_id'];
        $result = D('FrontC/Finance', 'Service')->memCoupons($param);
        //var_dump($result);
        return $result;
    }

    /**
     * @param array $request
     * @return array
     * 商城充值卡
     */
    function recCards($request = array())
    {
        //获取优惠券
        $param['where'] = array();
        $result = D('FrontC/Finance', 'Service')->recCards($param);
        return $result;
    }

    /**
     * @param array $request
     * @return array
     * 我的文章收藏
     */
    function myCollArt($request = array())
    {
        $param['where']['art_coll.m_id'] = $request['m_id'];
        $result = D('FrontC/Article', 'Service')->artColls($param);
        //var_dump($result);
        return $result;
    }

    /**
     * @param array $request
     * @return array
     * 我的商品收藏
     */
    function myCollGoods($request = array())
    {
        $param['where']['g_coll.m_id'] = $request['m_id'];
        //默认排序
        $param['order'] = 'g_coll.id DESC';
        //每页数量
        $param['page_size'] = 8;
        //调用数据模型层方法获取数据
        $result = D('FrontC/GoodsCollection')->getList($param);
        //数据列表 //分页信息
        $list = $result['list'];
        $page = $result['page'];
        //如果没有数据返回空数组
        if (empty($list))
            return array();
        //处理列表数据
        foreach ($list as &$value) {
            $file = api('File/getFiles', array($value['cover'], array('abs_url')));
            $value['cover'] = $file[0]['abs_url'];
        }
        return $list;
    }

    /**
     * @param array $request
     * @return bool
     * 绑定互联
     */
    function interconnect($request = array())
    {
        if (empty($request['openid']) || empty($request['platform']))
            return $this->setLogicInfo('参数错误！', false);
        if (M('Interconnect')->where(array('m_id' => $request['m_id'], 'platform' => $request['platform'], 'openid' => $request['openid']))->count()) {
            return $this->setLogicInfo('绑定成功！', true);
        }
        $conn_data['m_id'] = $request['m_id'];
        $conn_data['openid'] = $request['openid'];
        $conn_data['platform'] = $request['platform'];
        if (!M('Interconnect')->data($conn_data)->add()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        return $this->setLogicInfo('绑定成功！', true);
    }

    /**
     * @param array $request
     * @return bool
     * 创建购卡和充值订单
     */
    function createRechargeOrder($request = array())
    {
        if (empty($request['order_type']) || !in_array($request['order_type'], array(2, 3)))
            return $this->setLogicInfo('参数错误！', false);
        if ($request['order_type'] == 2) {
            if (empty($request['recharge_amounts']))
                return $this->setLogicInfo('请输入充值金额！', false);
            $diff_data = array(
                'recharge_amounts' => $request['recharge_amounts'],
                'pay_amounts' => $request['recharge_amounts'],
            );
        }
        if ($request['order_type'] == 3) {
            if (empty($request['rec_card_id']))
                return $this->setLogicInfo('请选择充值卡！', false);
            $card = M('RechargeCard')->where(array('id' => $request['rec_card_id']))->field('face_value,sales_price')->find();
            if (!$card)
                return $this->setLogicInfo('充值卡不存在！', false);
            $diff_data = array(
                'rec_card_id' => $request['rec_card_id'],
                'recharge_amounts' => $card['face_value'],
                'pay_amounts' => $card['sales_price'],
            );
        }

        $data = array_merge(array(
            'user_id' => $request['m_id'],
            'user_type' => 1,
            'order_sn' => D('FrontC/OrderInfo', 'Service')->createSn(1),
            'order_type' => $request['order_type'],
            'create_time' => NOW_TIME,
            'expire_time' => NOW_TIME + 600,
        ), $diff_data);

        $result = M('RechargeOrder')->data($data)->add();

        if (!$result)
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        return array('rec_order_id' => $result, 'pay_amounts' => $data['pay_amounts']);
    }

    /**
     * @param array $request
     * @return bool
     * app同步回调操作
     */
    function appCallback($request = array())
    {
        $order = M('RechargeOrder')->where(array('id' => $request['rec_order_id']))->field('status')->find();
        if ($order['status'] == 0) {
            return $this->setLogicInfo('支付失败！', false);
        }
        return $this->setLogicInfo('支付成功！', true);
    }

    /**
     * pup
     * [center我的钱包调用]
     * @param array $request
     * @return array
     * 余额明细
     */
    public function balanceTurnover($request = array())
    {
        $param['where']['bal_t.user_id'] = $request['m_id'];
        if ($request['flag'] == 1) {  //邀请收益
            $param['where']['bal_t.trend'] = 4;
        }
        //获取用户余额
        $member_param = array('id' => $request['m_id'], 'status' => 1);
        $balance = D('FrontC/Member', 'Model')->getBalance($member_param);
        $turnover = D('FrontC/Finance', 'Service')->balanceTurnover($param);
        $result = array(
            'balance' => $balance,
            'turnover' => $turnover,
        );
        return $result;
    }

    /**
     * @param array $request
     * @return array
     * 我的充值卡
     */
    function myRecCards($request = array())
    {
        $param['where']['rec_code.user_id'] = $request['m_id'];
        $result = D('FrontC/Finance', 'Service')->memRecCards($param);
        return $result;
    }

    /**
     * @param array $request
     * @return array
     * 查看卡密
     */
    function seeCode($request = array())
    {
        if (empty($request['rec_code_id']))
            return $this->setLogicInfo('参数错误！', false);
        //验证码支付密码
        if (empty($request['pay_pass']))
            return $this->setLogicInfo('请输入支付密码！', false);
        //获取用户支付密码
        $pay_pass = M('Member')->where(array('id' => $request['m_id']))->getField('pay_pass');
        //判断支付密码是否已设置
        if (empty($pay_pass))
            return $this->setLogicInfo('未设置支付密码，为保证安全请先去个人信息中设置支付密码！', false);
        //支付密码验证
        if (MD5($request['pay_pass']) != $pay_pass)
            return $this->setLogicInfo('支付密码不正确！', false);
        //获取充值码
        $code = M('RechargeCode')->where(array('user_id' => $request['m_id'], 'id' => $request['rec_code_id'], 'status' => 0))->getField('code');
        if (!$code)
            return $this->setLogicInfo('您的充值卡不存在或已充值！', false);
        //
        return array('code' => $code);
    }

    /**
     * @param array $request
     * @return array
     * 充值码充值
     */
    function rechargeByCode($request = array())
    {
        if (empty($request['code']))
            return $this->setLogicInfo('请输入充值码！', false);
        //验证充值码 区分大小写 'user_id'=>$request['m_id'],
        $code = M('RechargeCode')->where(array('code' => array('exp', '="' . $request['code'] . '" COLLATE utf8_bin')))->field('id,face_value,status')->find();
        if (!$code)
            return $this->setLogicInfo('充值码无效！', false);
        if ($code['status'] == 1)
            return $this->setLogicInfo('该充值码已使用！', false);
        //一切无误 进行业务操作
        $user_info = M('Member')->where(array('id' => $request['m_id']))->field('balance')->find();
        //开启事务
        $model = new \Think\Model();
        $model->startTrans();
        //增加余额
        $r1 = $model->table(C('DB_PREFIX') . 'member')->where(array('id' => $request['m_id']))->setInc('balance', $code['face_value']);
        if (!$r1) {
            $model->rollback();
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        //更新充值码信息
        $data['status'] = 1;
        $data['recharge_time'] = NOW_TIME;
        $data['recharger_id'] = $request['m_id'];
        $data['recharger_type'] = 1;
        $r2 = $model->table(C('DB_PREFIX') . 'recharge_code')->where(array('id' => $code['id']))->data($data)->save();
        if (!$r2) {
            $model->rollback();
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        //添加余额记录
        D('FrontC/Finance', 'Service')->addBalanceTurnover($request['m_id'], 1, 1, 3, $code['face_value'], $user_info['balance'], 1);

        $model->commit();

        return $this->setLogicInfo('充值成功！', true);
    }

    /**
     * [center提现调用]
     * @param array $request
     * @return bool
     * 提现
     */
    public function withdraw($request = array())
    {
        //用户银行卡ID
        if (empty($request['u_card_id']))
            return $this->setLogicInfo('参数错误！', false);
        //提现金额验证
        $amounts = floatval($request['amounts']);
        if (empty($amounts))
            return $this->setLogicInfo('请输入提现金额！', false);
        //余额是否充足
        $balance = M('Member')->where(array('id' => $request['m_id']))->getField('balance');
        if ($balance < $amounts)
            return $this->setLogicInfo('余额不足！', false);
        //获取银行卡信息
        $card = M('UserBankcard')->where(array('id' => $request['u_card_id']))->field('open_name,card_number,bank_name,bank_short,bank_logo')->find();
        if (!$card)
            return $this->setLogicInfo('用户银行卡不存在！', false);
        //添加提现记录
        $data = array(
            'user_id' => $request['m_id'],
            'user_type' => 1,
            'amounts' => $amounts,
            'bank_short' => $card['bank_short'],
            'bank_name' => $card['bank_name'],
            'bank_logo' => $card['bank_logo'],
//            'mobile' => $card['mobile'],
            'mobile' => 0,
            'open_name' => $card['open_name'],
            'card_number' => $card['card_number'],
            'create_time' => NOW_TIME,
        );
        //开启事务
        $model = new \Think\Model();
        $model->startTrans();
        //添加提现记录
        $r1 = $model->table(C('DB_PREFIX') . 'withdraw')->data($data)->add();
        if (!$r1) {
            $model->rollback();
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        $user_info = M('Member')->where(array('id' => $request['m_id']))->field('balance')->find();
        //减余额
        $r2 = $model->table(C('DB_PREFIX') . 'member')->where(array('id' => $request['m_id']))->setDec('balance', $amounts);
        if (!$r2) {
            $model->rollback();
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        }
        //添加余额记录
        D('FrontC/Finance', 'Service')->addBalanceTurnover($request['m_id'], 1, 2, 5, $amounts, $user_info['balance'], 1);

        return $this->setLogicInfo('提现已申请，等待平台操作...', true);
    }

    /**
     * [center提现记录调用]
     * @param array $request
     * @return array
     * 提现记录
     */
    function wdLogs($request = array())
    {
        $param['where']['wd.user_id'] = $request['m_id'];
        $result = D('FrontC/Finance', 'Service')->wdLogs($param);
        return $result;
    }

    /**
     * @param array $request
     * @return array
     * 我邀请的用户
     */
    function myInvites($request = array())
    {
        $param['where']['invite.user_id'] = $request['m_id'];
        //排序
        $param['order'] = 'invite.id DESC';
        //每页数量
        $param['page_size'] = 15;

        $result = D('FrontC/InviteLog')->getList($param);
        //数据列表 //分页信息
        $list = $result['list'];
        $page = $result['page'];

        foreach ($list as &$value) {
            $value['head'] = D('FrontC/Member', 'Service')->getHead($value['head']);
        }

        return array('list' => $list, 'total' => $result['total'], 'new_list' => array_slice($list, 0, 2));//$list;
    }
}