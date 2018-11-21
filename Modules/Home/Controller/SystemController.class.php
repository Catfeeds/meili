<?php
namespace Home\Controller;

/**
 * Class SystemController
 * @package Home\Controller
 * 系统控制器
 */
class SystemController extends HomeBaseController {

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

            $data['content']        = I('request.content');
            $data['ip']             = get_client_ip(1);
            $data['create_time']    = time();

            if(!M('Feedback')->data($data)->add())
                $this->error('系统繁忙，稍后重试！');
            else
                $this->success('反馈成功，我们会及时处理，谢谢！', '/');
        }
    }
}