<?php
namespace Bms\Controller;

/**
 * Class UpDownLoadController
 * @package Bms\Controller
 * 文件上传、下载控制器
 */
class UpDownLoadController extends BmsBaseController {

    /**
     * 上传文件
     */
    function upload() {
        $result = api('UpDownLoad/upload',array(I('request.')));
        $this->ajaxReturn(array_dimension($result) == 1 ? $result : $result['fileData']);
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