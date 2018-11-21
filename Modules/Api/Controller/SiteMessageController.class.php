<?php
namespace Api\Controller;

/**
 * Class SiteMessageController
 * @package Api\Controller
 * 消息控制器
 */
class SiteMessageController extends ApiBaseController{

    /**
     *
     */
    protected function _initialize() {
        parent::_initialize();
        $this->checkLogin();
    }

    /**
     * 消息列表
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID)
     */
    function messages() {
        $result = D('FrontC/SiteMessage', 'Logic')->messages(I('request.'));
        //var_dump($result);
        if($result === false)
            api_response('error', D('FrontC/SiteMessage', 'Logic')->getLogicInfo());
        else
            api_response('success', '', empty($result['list']) ? array() : $result['list']);
    }

    /**
     * 消息详情
     * 详细描述：
     * 特别注意：
     * POST参数：*m_id(用户ID) *site_msg_id(消息ID)
     */
    function detail() {
        $result = D('FrontC/SiteMessage', 'Logic')->detail(I('request.'));
        //var_dump(D('FrontC/Friendship', 'Logic')->getLogicInfo());
        if($result === false)
            api_response('error', D('FrontC/SiteMessage', 'Logic')->getLogicInfo());
        else
            api_response('success', '', $result);
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
            $not_read = M('SiteMessage')->where(array('user_id'=>I('request.m_id'),'status'=>0))->count();
        else
            $not_read = 0;

        api_response('success', '', array('not_read'=>$not_read));
    }
}