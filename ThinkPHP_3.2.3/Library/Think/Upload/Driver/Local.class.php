<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Think\Upload\Driver;
class Local{
    /**
     * 上传文件根目录
     * @var string
     */
    private $rootPath;

    /**
     * 本地上传错误信息
     * @var string
     */
    private $error = ''; //上传错误信息

    /**
     * 构造函数，用于设置上传根路径
     */
    public function __construct($config = null){

    }

    /**
     * 检测上传根目录
     * @param string $rootpath   根目录
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath($rootpath){
        if(!(is_dir($rootpath) && is_writable($rootpath))){
            $this->error = '上传根目录不存在！请尝试手动创建:'.$rootpath;
            return false;
        }
        $this->rootPath = $rootpath;
        return true;
    }

    /**
     * 检测上传目录
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath){
        /* 检测并创建目录 */
        if (!$this->mkdir($savepath)) {
            return false;
        } else {
            /* 检测目录是否可写 */
            if (!is_writable($this->rootPath . $savepath)) {
                $this->error = '上传目录 ' . $savepath . ' 不可写！';
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * 保存指定文件
     * @param  array   $file    保存的文件信息
     * @param  boolean $replace 同名文件是否覆盖
     * @param  array   $config   配置信息
     * @return boolean          保存状态，true-成功，false-失败
     */
    public function save($file, $replace=true, $config=array()) {
        $filename = $this->rootPath . $file['savepath'] . $file['savename'];

        /* 不覆盖同名文件 */ 
        if (!$replace && is_file($filename)) {
            $this->error = '存在同名文件' . $file['savename'];
            return false;
        }

        /* 移动文件 */
        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            $this->error = '文件上传保存错误！';
            return false;
        }

        //添加水印
        if($config['isWater']) {
            $this->_water($filename);
        }

        //生成缩略图
        if($config['isThumb']) {
            $this->_thumb($filename, $file, $config);
        }

        if($this->zipImags) {
            // TODO 对图片压缩包在线解压
        }
        
        return true;
    }

    /**
     * @param string $filename  文件相对路径+名称
     * @param array $file  文件信息
     * @param array $config  配置
     * 生成缩略图
     */
    private function _thumb($filename, $file, $config) {
        //判断是不是图像文件 是图像文件进行缩略
        if(false !== getimagesize($filename)) {
            //缩略图最大宽度
            $thumbWidth		=	explode(',', $config['thumbMaxWidth']);
            //缩略图最大高度
            $thumbHeight	=	explode(',', $config['thumbMaxHeight']);
            //缩略图前缀
            $thumbPrefix	=	explode(',', $config['thumbPrefix']);
            //缩略图后缀
            $thumbSuffix    =   explode(',', $config['thumbSuffix']);
            //初始化图像处理类
            $Image          =   new \Think\Image();
            // 生成图像缩略图 可多张
            for($i = 0, $length = count($thumbWidth); $i < $length; $i++) {
                $prefix      =   isset($thumbPrefix[$i]) ? $thumbPrefix[$i] : $thumbPrefix[0];//前缀
                $suffix      =   isset($thumbSuffix[$i]) ? $thumbSuffix[$i] : $thumbSuffix[0];//后缀
                $thumb_name  =   $prefix . basename($filename, '.' . $file['ext']) . $suffix; //缩略图名称
                //生成缩略图  open打开元图像文件 thumb进行缩略 save保存缩略图
                $Image->open($filename)->thumb($thumbWidth[$i],$thumbHeight[$i])->save(dirname($filename).'/' . $thumb_name . '.' . $file['ext']);
            }
        }
    }

    /**
     * @param $filename
     * 添加水印
     */
    private function _water($filename) {
        //初始化图像处理类
        $Image = new \Think\Image();
        // 给原图添加水印并保存
        $Image->open($filename)->water(C('WATER_PATH'))->save($filename);
    }

    /**
     * 创建目录
     * @param  string $savepath 要创建的穆里
     * @return boolean          创建状态，true-成功，false-失败
     */
    public function mkdir($savepath){
        $dir = $this->rootPath . $savepath;
        if(is_dir($dir)){
            return true;
        }

        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            $this->error = "目录 {$savepath} 创建失败！";
            return false;
        }
    }

    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->error;
    }

}
