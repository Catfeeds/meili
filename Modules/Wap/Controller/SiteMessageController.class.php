<?php
namespace Wap\Controller;

/**
 * Class SiteMessageController
 * @package Wap\Controller
 * 消息控制器
 */
class SiteMessageController extends WapBaseController{

    /**
     *
     */
    protected function _initialize() {
        parent::_initialize();
        $this->checkLogin();
    }

    function messages() {
        $this->display('messages');
    }
    /**
     * 消息列表
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function getMessages() {
        $result = D('FrontC/SiteMessage', 'Logic')->messages(I('request.'));
        if(empty($result['list']))
            $this->error('');
        else
            $this->success('', '', true, $result['list']);
    }

    /**
     * 消息详情
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *site_msg_id(消息ID)
     */
    function detail() {
        $result = D('FrontC/SiteMessage', 'Logic')->detail(I('request.'));
        if($result === false) {
            redirect('System/error404');
        } else {
            $this->assign('msg', $result);
            $this->display('detail');
        }
    }

    /**
     * 未读消息
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function notRead() {
        //是否有未读消息
        if(!empty($_REQUEST['m_id']))
            $not_read = M('SiteMessage')->where(array('m_id'=>I('request.m_id'),'status'=>0))->count();
        else
            $not_read = 0;

        api_response('success', '', array('not_read'=>$not_read));
    }
}