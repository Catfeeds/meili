<?php
namespace Api\Controller;

/**
 * Class SystemController
 * @package Api\Controller
 * 系统部分内容控制器
 */
class SystemController extends ApiBaseController{

    /**
     * 获取取消、退款...原因
     * 详细描述：
     * 特别注意：
     * POST参数：
     */
    function getReason() {
		api_response('success', '', D('FrontC/System','Service')->getReasons(array('type'=>1)));
	}

    /**
     * 获取银行信息列表
     * 详细描述：
     * 特别注意：
     * POST参数：
     */
    function getBanks() {
        api_response('success', '', D('FrontC/System','Service')->getBanks());
    }

    /**
     * 获取配置信息
     * 详细描述：
     * 特别注意：
     * POST参数：*identifier(配置标识符 可一个 可多个逗号隔开)
     */
    function getConfig() {
        api_response('success', '', D('FrontC/System','Service')->getConfig(I('request.')));
    }


    /**
     * 获取地区列表
     * 详细描述：获取城市、区域列表  根据parent_id
     * 特别注意：APP端根据 region_type 判断 字段映射给 province_name ...
     * POST参数：*reg_id(地区主键ID 默认传 1)
     */
    function getRegion() {
        $list = D('Region','Service')->select(array('where'=>array('parent_id'=>I('request.reg_id'))));
        api_response('success', '', $list);
    }

    /**
     * 服务商品 套餐热门搜索
     * */
    public function getHotSearch()
    {
        $result = D('FrontC/GoodsHotSearch', 'Service')->getHotSearch(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/GoodsHotSearch', 'Service')->getLogicInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 搜索
     * 参数：keyword
     * */
    public function serviceSearch()
    {
        $result = D('FrontC/Custom', 'Service')->serviceSearch(I('request.'));
        if ($result === false)
            api_response('error', D('FrontC/Custom', 'Service')->getServiceInfo());
        else
            api_response('success', '', $result);
    }

    /**
     * 获取推荐搜索
     * 详细描述：
     * 特别注意：
     * POST参数：
     */
//    public function getHotSearch(){
//        $list = M('GoodsHotSearch')->where(array('is_hot'=>1))->field('keywords')->select();
//        api_response('success', '', $list);
//    }

    /**
     * 获取默认头像
     * 详细描述：
     * 特别注意：
     * POST参数：
     */
    public function getDefaultHead() {
        api_response('success', '', array('default_head'=>C('DEFAULT_HEAD')));
    }

    /**
     * 意见反馈
     * 详细描述：
     * 特别注意：
     * POST参数：*content(内容) contact(联系方式)
     */
    public function feedback(){
        $result = D('FrontC/System', 'Service')->feedback(I('request.'));
        if($result === false)
            api_response('error', D('FrontC/System', 'Service')->getServiceInfo());
        api_response('success', D('FrontC/System', 'Service')->getServiceInfo());
    }

    /**
     * 根据卡号获取银行信息
     * 详细描述：
     * 特别注意：
     * POST参数：*number(银行卡号)
     */
    public function getBankByNumber() {
        $result = D('FrontC/System', 'Service')->getBankByNumber(I('request.number'));
        if($result === false)
            api_response('error', '获取银行信息失败，请检查银行卡号是否正确！', (object)array());
        api_response('success', '', $result);
    }

    /**
     * 获取启动广告
     * 详细描述：
     * 特别注意：
     * POST参数：
     */
    function getStartUpAd() {
        $adverts = D('FrontC/Advert', 'Service')->getAdvert(array('position'=>3));
        if(empty($adverts))
            api_response('error', '未设置启动广告！');
        api_response('success', '', $adverts[0]);
    }
}