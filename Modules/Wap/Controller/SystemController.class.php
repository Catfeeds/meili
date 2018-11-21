<?php
namespace Wap\Controller;

/**
 * Class SystemController
 * @package Wap\Controller
 * 系统控制器
 */
class SystemController extends WapBaseController {

    function error404() {
        $this->display('error404');
    }

    function payError() {
        $this->display('payError');
    }

    function paySuccess() {
        $this->display('paySuccess');
    }

    function download() {
        $this->display('download');
    }

    function feedback() {
        if(!IS_POST) {
            $this->display('feedback');
        } else {
            if (empty($_REQUEST['content']))
                $this->error('请输入反馈内容！');

            $data['contact']        = I('request.contact');
            $data['content']        = I('request.content');
            $data['ip']             = get_client_ip(1);
            $data['create_time']    = time();

            if(!M('Feedback')->data($data)->add())
                $this->error('系统繁忙，稍后重试！');
            else
                $this->success('反馈成功，我们会及时处理，谢谢！', '/');
        }
    }

    /**
     * 获取地区列表
     * 详细描述：获取城市、区域列表  根据parent_id
     * 特别注意：APP端根据 region_type 判断 字段映射给 province_name ...
     * POST参数：*reg_id(地区主键ID 默认传 1)
     */
    function getRegion() {
        $list = D('Region','Service')->select(array('where'=>array('parent_id'=>I('request.reg_id'))));
        $this->success('', '', true, $list);
    }

   /*
   获取师傅类型
   */
	function getMasterCat() {
	    $list = M('Master_category')->where()->order('sort desc')->select();
	    $this->success('', '', true, $list);
	}
}