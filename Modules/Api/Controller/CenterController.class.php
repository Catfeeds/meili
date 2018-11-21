<?php

namespace Api\Controller;

/**
 * Class CenterController
 * @package Api\Controller
 * 个人中心控制器
 */
class CenterController extends ApiBaseController
{

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize()
    {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //验证登陆
        $this->checkLogin();
    }

    /**
     * 我的个人信息
     * m_id  - 用户ID
     * /
//    public function myInfo()
//    {
//        $result = D('FrontC/Center', 'Logic')->myInfo(I('request.'));
//        if(!$result) {
//            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
//        } else {
//            api_response('success', '', $result);
//        }
//    }

     /*
      * 获取充值金额
      * */
    function getRechargeAmounts()
    {
        $result = D('FrontC/Center', 'Logic')->getRechargeAmounts(I('request.'));
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', '', $result);
        }
     }

    /**
     * 意见反馈
     * 特别注意：
     * POST参数：m_id-用户ID  content-意见内容 link-联系方式
     */
    function submitOpinion()
    {
        $result = D('FrontC/Center', 'Logic')->submitOpinion(I('request.'));
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
        }
    }

    /**
     * 获取帮助文档列表
     * 特别注意：
     * POST参数：m_id-用户ID
     */
    function getHelpDocs()
    {
        $result = D('FrontC/Center', 'Logic')->getHelpDocs(I('request.'));
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', '', $result);
        }
    }

    /**
     * 获取帮助问文档列表
     * 特别注意：
     * POST参数：m_id-用户ID doc_id-文档ID
     */
    function getDocDetail()
    {
        $result = D('FrontC/Center', 'Logic')->getDocDetail(I('request.'));
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', '', $result);
        }
    }

    /**
     * 关于我们
     * 特别注意：
     * POST参数：m_id-用户ID
     */
    function aboutUs()
    {
        $result = D('FrontC/Center', 'Logic')->aboutUs(I('request.'));
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', '', $result);
        }
    }

    /**
     * 修改头像
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *head(头像文件)
     */
    function modifyHead()
    {
        //api_response('success', '上传成功！', array());
        $_REQUEST['save_path'] = 'Head';
        $result = D('FrontC/Center', 'Logic')->modifyHead(I('request.'));
        if ($result === false) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', '上传成功！', $result);
        }
    }

    /**
     * 修改文本信息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *field(字段标记 1--昵称) *value(字段值)
     */
    function modifyInfo()
    {
        $result = D('FrontC/Center', 'Logic')->modifyInfo(I('request.'));
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
        }
    }

    /**
     * 修改登陆密码
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *password(原密码) *new_password(新密码) *re_new_password(确认新密码)
     */
    function modifyPass()
    {
        $result = D('FrontC/Center', 'Logic')->modifyPass(I('request.'));
        //var_dump(D('FrontC/Center', 'Logic')->getLogicInfo());
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
        }
    }

    /**
     * 修改手机号码
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *account(原账号) *new_account(新账号) *verify(新手机号验证码)
     */
    function modifyAccount()
    {
        $result = D('FrontC/Center', 'Logic')->modifyAccount(I('request.'));
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
        }
    }

    /**
     * 设置或修改支付密码
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *pay_pass(支付密码) *re_pay_pass(确认支付密码)
     */
    function modifyPayPass()
    {
        $result = D('FrontC/Center', 'Logic')->modifyPayPass(I('request.'));
        if (!$result) {
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
        }
    }

    /**
     * 用户地址列表
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function addresses()
    {
        $list = D('FrontC/Address', 'Logic')->getList(I('request.'));
        api_response('success', '', $list);
    }

    /**
     * 获取一条地址信息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(地址主键ID)
     */
    function address()
    {
        $result = D('FrontC/Address', 'Logic')->getRow(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Address', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 获取省市区
     * */
    public function getCity()
    {
        $result = D('FrontC/Region', 'Service')->getRows(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Region', 'Service')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 添加、更新用户地址信息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) adr_id(主键ID) *contacts(联系人) *mobile(手机号) *province_id(省ID) *province_name(省名称)
     *           *city_id(城市ID) *city_name(城市名称) *area_id(区域ID) *area_name(区域名称) *address(详细地址) is_default(是否默认 1是)
     */
    function updAddress()
    {
        $_REQUEST['model'] = 'FrontC/Address';
        $_REQUEST['id'] = I('request.adr_id');
        $result = D('FrontC/Address', 'Logic')->update(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Address', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Address', 'Logic')->getLogicInfo());
    }

    /**
     * 删除一条地址信息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(地址主键ID)
     */
    function delAddress()
    {
        $_REQUEST['model'] = 'FrontC/Address';
        $_REQUEST['ids'] = I('request.adr_id');
        $result = D('FrontC/Address', 'Logic')->remove(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Address', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Address', 'Logic')->getLogicInfo());
    }

    /**
     * 设为默认
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(地址主键ID)
     */
    function setDefault()
    {
        $result = D('FrontC/Address', 'Logic')->setDefault(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Address', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Address', 'Logic')->getLogicInfo());
    }

    /**
     * 用户银行卡
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function myCards()
    {
        $list = D('FrontC/UserBankcard', 'Logic')->getList(I('request.'));
        api_response('success', '', $list);
    }

    /**
     * 银行卡详情
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *u_card_id(用户银行卡ID)
     */
    function card()
    {
        $result = D('FrontC/UserBankcard', 'Logic')->getRow(I('request.'));
        if (false === $result)
            api_response('error', D('FrontC/UserBankcard', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 获取所有银行列表
     * POST参数：m_id
     * */
//    public function getBanks()
//    {
//        $result = D('FrontC/Center', 'Logic')->getBanks(I('request.'));
//        if(!$result) {
//            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
//        } else {
//            api_response('success', '', $result);
//        }
//    }

    /*
     * 用户添加银行卡
     * */
//    public function addBankcard()
//    {
//        $result = D('FrontC/Center', 'Logic')->addBankcard(I('request.'));
//        if(!$result) {
//            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
//        } else {
//            api_response('success', '添加成功', $result);
//        }
//    }

    /**
     * 添加、更新用户银行卡
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) u_card_id(主键ID) *open_name(持卡人) *card_number(卡号) *mobile(预留手机号) *bank_name(银行名称)
     *           *bank_short(银行简称) *bank_logo(银行LOGO) open_address(开户地) is_default(是否默认 1--是)
     */
    function updBankcard()
    {
        $_REQUEST['model'] = 'FrontC/UserBankcard';
        $_REQUEST['id'] = I('request.u_card_id');
        $result = D('FrontC/UserBankcard', 'Logic')->update(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/UserBankcard', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/UserBankcard', 'Logic')->getLogicInfo());
    }

    /**
     * 删除银行卡
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *u_card_id(用户银行卡ID)
     */
    function delCard()
    {
        $_REQUEST['model'] = 'FrontC/UserBankcard';
        $_REQUEST['ids'] = I('request.u_card_id');
        $result = D('FrontC/UserBankcard', 'Logic')->remove(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/UserBankcard', 'Logic')->getLogicInfo());
        api_response('success', '已解除绑定！');
    }

    /**
     * 获取用户信息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function getInfo()
    {
        $result = D('FrontC/Member', 'Service')->getInfo('', I('request.m_id'));
        if (!$result)
            api_response('error', '获取失败！');
        api_response('success', '', $result);
    }

    /**
     * 我的积分
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) p
     */
    function myIntegrals()
    {
        $result = D('FrontC/Center', 'Logic')->myIntegrals(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 我的优惠券
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) p
     */
    function myCoupons()
    {
        $result = D('FrontC/Center', 'Logic')->myCoupons(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 商城充值卡
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) p
     */
    function recCards()
    {
        $result = D('FrontC/Center', 'Logic')->recCards(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 购买充值卡/直接充值
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *order_type(订单类型 2--充值 3--购卡) *rec_card_id(充值卡ID) *recharge_amounts(充值金额)
     */
    function doBR()
    {
        $result = D('FrontC/Center', 'Logic')->createRechargeOrder(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 购买充值卡/直接充值订单在线支付
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *rec_order_id(订单ID) *payment(支付方式)
     */
    function doBRPay()
    {
        $result = D('FrontC/Pay', 'Logic')->doBRPay(I('request.'));
        //var_dump(D('FrontC/Pay', 'Logic')->getLogicInfo());
        if (!$result)
            api_response('error', D('FrontC/Pay', 'Logic')->getLogicInfo());
        if ($_REQUEST['payment'] == 1)
            api_response('success', '', $result);
        elseif ($_REQUEST['payment'] == 2)
            print stripslashes(json_encode(array('flag' => 'success', 'message' => '', 'data' => $result)));
        else
            api_response('success', '支付成功！');
    }

    /**
     * APP同步回调
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *rec_order_id(订单ID)
     */
    function appCallback()
    {
        $result = D('FrontC/Center', 'Logic')->appCallback(I('request.'));
        if (!$result)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
    }

    /**
     * 我的收藏商品
     */
    function myCollGoods()
    {
        $result = D('FrontC/Center', 'Logic')->myCollGoods(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 我的收藏文章
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *p(页号)
     */
    function myCollArt()
    {
        $result = D('FrontC/Center', 'Logic')->myCollArt(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 签到
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(地址主键ID)
     */
    function sign()
    {
        $result = D('FrontC/Center', 'Logic')->sign(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo(), $result);
    }

    /**
     * 绑定三方
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *open_id() *platform(平台类型 1--扣扣  4--微信)
     */
    function interconnect()
    {
        $result = D('FrontC/Center', 'Logic')->interconnect(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
    }

    /**
     * 余额明细
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *p(页号)
     */
    function myWallet()
    {
        $result = D('FrontC/Center', 'Logic')->balanceTurnover(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 我的充值卡
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *p(页号)
     */
    function myRecCards()
    {
        $result = D('FrontC/Center', 'Logic')->myRecCards(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 查看卡密
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *rec_code_id(用户充值卡ID) *pay_pass(支付密码)
     */
    function seeCode()
    {
        $result = D('FrontC/Center', 'Logic')->seeCode(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 充值码充值
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *code(用户充值卡ID)
     */
    function rechargeByCode()
    {
        $result = D('FrontC/Center', 'Logic')->rechargeByCode(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
    }

    /**
     * 提现
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *u_card_id(用户银行卡ID) *amounts(提现金额)
     */
    function withdraw()
    {
        $result = D('FrontC/Center', 'Logic')->withdraw(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
    }

    /**
     * 提现记录
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *p(页号)
     */
    function wdLogs()
    {
        $result = D('FrontC/Center', 'Logic')->wdLogs(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }

    /**
     * 我邀请的用户
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *p(页号)
     */
    function myInvites()
    {
        $result = D('FrontC/Center', 'Logic')->myInvites(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', '', $result);
    }
}