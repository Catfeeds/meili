<?php
namespace FrontC\Logic;

/**
 * Class SiteMessageLogic
 * @package FrontC\Logic
 * 消息逻辑层
 */
class SiteMessageLogic extends FrontBaseLogic {

    /**
     * @param array $request
     * @return array
     * 消息列表
     */
    function messages($request = array()) {
        //用户ID
        if(!empty($request['m_id'])) {
            $param['where']['site_msg.user_id']   = $request['m_id'];
            $param['where']['site_msg.user_type'] = 1;
        }
        return D('FrontC/SiteMessage', 'Service')->messages($param);
    }

    /**
     * @param array $request
     * @return array
     * 消息详情
     */
    function detail($request = array()) {
        if(empty($request['site_msg_id']))
            return $this->setLogicInfo('参数错误！', false);
        //获取数据
        $detail = M('SiteMessage')->where(array('id'=>$request['site_msg_id']))->field('id site_msg_id,subject,content,create_time,status')->find();
        //查询错误
        if(!$detail)
            return $this->setLogicInfo('数据不存在！', false);
        //时间处理
        $detail['create_time'] = fuzzy_date($detail['create_time']);
        //已读设置
        M('SiteMessage')->where(array('id'=>$request['site_msg_id']))->setField('status', 1);

        return $detail;
    }
}