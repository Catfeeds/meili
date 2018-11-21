<?php
namespace Wap\Controller;

/**
 * Class CenterController
 * @package Wap\Controller
 * 个人中心控制器
 */
class CenterController extends WapBaseController {

    /**
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        //验证登陆
        $this->checkLogin();
    }

    /**
     * 个人中心主页
     */
    function index() {
        $result = D('FrontC/Member', 'Service')->getInfo('', I('request.m_id'));
        $this->assign('info', $result);
        //是否有未读消息
        if(!empty($_REQUEST['m_id']))
            $this->assign('not_read', M('SiteMessage')->where(array('m_id'=>I('request.m_id'),'status'=>0))->count());
        else
            $this->assign('not_read', 0);
        $this->display('index');
    }

    /**
     * 个人信息
     */
    function myInfo() {
        $result = D('FrontC/Member', 'Service')->getInfo('', I('request.m_id'));
        $this->assign('info', $result);
        $this->display('myInfo');
    }

    /**
     * 修改头像
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *head(头像文件)
     */
    function modifyHead() {
        $_REQUEST['save_path'] = 'Head';
        $result = D('FrontC/Center', 'Logic')->modifyHead(I('request.'));
        if($result === false) {
            $this->ajaxReturn(array('status'=>0,'info'=>D('FrontC/Center', 'Logic')->getLogicInfo()), JSON);
        } else {
            $this->ajaxReturn($result, JSON);
        }
    }

    /**
     * 修改文本信息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *field(字段标记 1--昵称) *value(字段值)
     */
    function modifyInfo() {
        if(!IS_POST) {
            $this->display('modifyInfo');
        } else {
            $result = D('FrontC/Center', 'Logic')->modifyInfo(I('request.'));
            if (!$result) {
                $this->error(D('FrontC/Center', 'Logic')->getLogicInfo());
            } else {
                $this->success(D('FrontC/Center', 'Logic')->getLogicInfo(), U('Center/myInfo'));
            }
        }
    }

    /**
     * 修改登陆密码
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *password(原密码) *new_password(新密码) *re_new_password(确认新密码)
     */
    function modifyPass() {
        if(!IS_POST) {
            $this->display('modifyPass');
        } else {
            $result = D('FrontC/Center', 'Logic')->modifyPass(I('request.'));
            if (!$result) {
                $this->error(D('FrontC/Center', 'Logic')->getLogicInfo());
            } else {
                $this->success(D('FrontC/Center', 'Logic')->getLogicInfo(), U('Center/myInfo'));
            }
        }
    }

    /**
     * 用户修改手机号页面控制
     */
    function modifyAccount() {
        if(I('request.step') == 1) {
            $result = D('FrontC/Member', 'Service')->getInfo('', I('request.m_id'));
            $this->assign('info', $result);
            $this->display('modifyAccount_1');
        } else {
            $this->display('modifyAccount_2');
        }
    }

    /**
     * 修改手机号码
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *account(原账号) *new_account(新账号) *verify(新手机号验证码)
     */
    function doModifyAccount() {
        $result = D('FrontC/Center', 'Logic')->modifyAccount(I('request.'));
        if(!$result) {
            $this->error(D('FrontC/Center', 'Logic')->getLogicInfo());
        } else {
            $this->success(D('FrontC/Center', 'Logic')->getLogicInfo(), U('Center/myInfo'));
        }
    }

    /**
     * 用户地址列表
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function addresses() {
        $list = D('FrontC/Address', 'Logic')->getList(I('request.'));
        $this->assign('addresses', $list);
        $this->display('addresses');
    }

    /**
     * 添加、更新用户地址信息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) adr_id(主键ID) *contacts(联系人) *mobile(手机号) *province_id(省ID) *province_name(省名称)
     *          *city_id(城市ID) *city_name(城市名称) *area_id(区域ID) *area_name(区域名称) *address(详细地址) is_default(是否默认 1是)
     */
    function updAddress() {
        if(!IS_POST) {
            $result = D('FrontC/Address', 'Logic')->getRow(I('request.'));
            $this->assign('row', $result);
            $this->display('updAddress');
        } else {
            $_REQUEST['model'] = 'FrontC/Address'; //
            $_REQUEST['id'] = I('request.adr_id'); //
            $result = D('FrontC/Address', 'Logic')->update(I('request.'));
            if (!$result) {
                $this->error(D('FrontC/Address', 'Logic')->getLogicInfo());
            } else {
                $this->success(D('FrontC/Address', 'Logic')->getLogicInfo(), empty($_REQUEST['flag']) ? U('Center/addresses') : cookie('__forward__'));
            }
        }
    }

    /**
     * 删除一条地址信息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(地址主键ID)
     */
    function delAddress() {
        $_REQUEST['model']   = 'FrontC/Address'; //
        $_REQUEST['ids']     = I('request.adr_id'); //
        $result = D('FrontC/Address', 'Logic')->remove(I('request.'));
        if(!$result)
            $this->error(D('FrontC/Address', 'Logic')->getLogicInfo());
        $this->success(D('FrontC/Address', 'Logic')->getLogicInfo());
    }

    /**
     * 设为默认
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(地址主键ID)
     */
    function setDefault() {
        $result = D('FrontC/Address', 'Logic')->setDefault(I('request.'));
        if(!$result)
            $this->error(D('FrontC/Address', 'Logic')->getLogicInfo());
        $this->success(D('FrontC/Address', 'Logic')->getLogicInfo());
    }

    /**
     * 我的积分页面
     */
    function myIntegrals() {
        $this->assign('integral', M('Member')->where(array('id'=>I('request.m_id')))->getField('integral'));
        $this->display('myIntegrals');
    }

    /**
     * 我的积分
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) p
     */
    function getMyIntegrals() {
        $result = D('FrontC/Center', 'Logic')->myIntegrals(I('request.'));
        if(empty($result['list']))
            $this->error('无结果');
        $this->success('', '', true, $result['list']);
    }

    /**
     * 我的优惠券页面控制
     */
    function myCoupons() {
        $this->display('myCoupons');
    }

    /**
     * 我的优惠券
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) p
     */
    function getMyCoupons() {
        $result = D('FrontC/Center', 'Logic')->myCoupons(I('request.'));
        if(empty($result))
            $this->error(D('FrontC/Center', 'Logic')->getLogicInfo());
        $this->success('', '', true, $result);
    }

    /**
     * 我的优惠券页面控制
     */
    function myCollGoods() {
        $this->display('myCollGoods');
    }

    /**
     * 我的优惠券页面控制
     */
    function myCollArt() {
        $this->display('myCollArt');
    }

    /**
     * 我的收藏商品
     */
    function getMyCollGoods() {
        $result = D('FrontC/Center', 'Logic')->myCollGoods(I('request.'));
        if(empty($result))
            $this->error(D('FrontC/Center', 'Logic')->getLogicInfo());
        $this->success('', '', true, $result);
    }

    /**
     * 我的收藏文章
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *p(页号)
     */
    function getMyCollArt() {
        $result = D('FrontC/Center', 'Logic')->myCollArt(I('request.'));
        if(empty($result))
            $this->error(D('FrontC/Center', 'Logic')->getLogicInfo());
        $this->success('', '', true, $result);
    }

    /**
     * 我的管家
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function myKeeper() {
        $result = D('FrontC/Center', 'Logic')->myKeeper(I('request.'));
        if($result === false)
            $this->assign('msg', D('FrontC/Center', 'Logic')->getLogicInfo());
        $this->assign('keeper', $result);

        $this->display('myKeeper');
    }

    /**
     * 签到
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *adr_id(地址主键ID)
     */
    function sign() {
        $result = D('FrontC/Center', 'Logic')->sign(I('request.'));
        if($result === false)
            $this->error(D('FrontC/Center', 'Logic')->getLogicInfo());
        $this->success(C('SIGN_INTEGRAL'));
    }

    /**
     * 绑定三方
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *open_id() *platform(平台类型 1--扣扣  4--微信)
     */
    function interconnect() {
        $result = D('FrontC/Center', 'Logic')->interconnect(I('request.'));
        if($result === false)
            api_response('error', D('FrontC/Center', 'Logic')->getLogicInfo());
        api_response('success', D('FrontC/Center', 'Logic')->getLogicInfo());
    }

    /**
     * 设置
     */
    function set() {
        $this->assign('config', D('FrontC/System','Service')->getConfig('1'));
        $this->display('set');
    }

    /**
     * 退出登录
     */
    function logout() {
        session(null);
        redirect(U('Passport/login'));
    }
}