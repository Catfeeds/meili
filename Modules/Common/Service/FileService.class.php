<?php
namespace Common\Service;
use Common\Base\BaseService;

/**
 * Class FileService
 * @package Common\Service
 * 文件表 数据服务层
 */
class FileService extends BaseService {

    /**
     * @param array $custom_param
     * @return array
     * 获取文件列表
     */
    function select($custom_param = array()) {
        //表别名
        $param['alias']                 = 'file';
        //关联的表 file_extend
        $param['join']                  = array(
            'LEFT JOIN '.C('DB_PREFIX').'file_extend file_ext ON file_ext.file_id = file.id',
        );
        //查询的字段
        $param['field']                 = 'file.*,file_ext.description';   //排序

        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param)) {
            $param = $this->customParam($param, $custom_param);
        }

        //TODO  生成缩略图路径
        $result = D('File')->getList($param);

        //处理图片信息
        foreach($result['list'] as &$value) {
            //判断 是否 需要获取 缩略图地址
            if(!isset($value['thumb_prefix']) && !isset($value['thumb_suffix'])) {
                continue;
            }
            //获取缩略图地址 数组
            $value = $this->_getThumb($value);
        }

        return $result;
    }

    /**
     * @param $file
     * @return mixed
     * 获取缩略图地址
     */
    private function _getThumb($file) {
        //缩略图前缀
        $prefixes	=	explode(',', $file['thumb_prefix']);
        //缩略图后缀
        $suffixes   =   explode(',', $file['thumb_suffix']);
        //使用 前缀/后缀 数量多的 缀名
        $length     =   count($prefixes) >= count($suffixes) ? count($prefixes) : count($suffixes);
        // 生成图像缩略图地址
        for($i = 0; $i < $length; $i++) {
            $prefix =   isset($prefixes[$i]) ? $prefixes[$i] : $prefixes[0];//前缀
            $suffix =   isset($suffixes[$i]) ? $suffixes[$i] : $suffixes[0];//后缀
            //相对路径
            $file['thumbs'][]      = dirname($file['path']) . '/' . $prefix . basename($file['path'], '.' . $file['ext']) . $suffix . '.' . $file['ext'];
            //绝对路径
            $file['abs_thumbs'][]  = dirname($file['abs_url']) . '/' . $prefix . basename($file['abs_url'], '.' . $file['ext']) . $suffix . '.' . $file['ext'];;
        }
        //释放数据
        unset($file['thumb_prefix'], $file['thumb_suffix'], $file['ext']);

        return $file;
    }
}