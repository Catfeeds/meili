<?php
/**
 * Created by PhpStorm.
 * User: toocms
 * Date: 2018/11/9
 * Time: 15:40
 */

namespace Api\Controller;


class AppController extends ApiBaseController
{
    /**
     * 检查更新
     * 详细描述：
     * 特别注意：
     * POST参数：*package(包名)
     */
    public function checkVersion(){
        $version = M('ApkUpdate')->where(array('package'=>I('request.package')))->field('version,url,description')->find();
        $version['url'] ='http://meili-api.uuudoo.com/System/download/flag/1/package/'.I('request.package');
        if(empty($version))
            return array();
        api_response('success', '', $version);
    }

    /**
     * 下载更新包apk
     * @param int $flag
     * @param $package
     */
    function download($flag=1,$package) {
        header("Content-Type:textml; charset=utf-8");
        //最新的apk
        $url = M('ApkUpdate')->where(array('package'=>$package))->order('update_time desc')->limit(1)->select();

        // $down_url = D('FrontC/Member', 'Service')->getHead($url['0']['path']);
        $down_url = M("File")->where(array('id'=>$url['0']['url']))->getField('path');
        switch($flag) {
            case 1 : $filename = '.'.$down_url; break;
            default : exit; break;
        }
        //执行下载
        header('Content-Description: File Transfer');
        header('Content-type: application/octet-stream');
        Header('Accept-Ranges:  bytes ');
        header('Content-Length: ' . filesize($filename));
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: attachment; filename="' . rawurlencode(basename($filename)) . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        }
        readfile($filename);
    }
}