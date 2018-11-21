<?php
namespace Wap\Controller;

/**
 * Class UpDownLoadController
 * @package Wap\Controller
 * 文件上传、下载控制器
 */
class UpDownLoadController extends WapBaseController {

    /**
     * 上传文件
     */
    function upload() {
        $result = api('UpDownLoad/upload',array(I('request.')));
        $this->ajaxReturn(array_dimension($result) == 1 ? $result : $result['file']);
    }

    /**
     * @param null $id
     * 下载文件
     */
    function download($id = null) {
        if(empty($id) || !is_numeric($id)) {
            $this->error('参数错误！');
        }
        if(!api('UpDownLoad/download', array($id))) {
            $this->error(api('UpDownLoad/getApiMsg'));
        }
    }
}