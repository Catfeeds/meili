<?php
namespace Common\Model;
use Common\Base\BaseModel;

/**
 * Class ConfigModel
 * @package Common\Model
 * 系统配置 模型
 */
class ConfigModel extends BaseModel {

    /**
     * 获取数据库中的配置列表
     * @return array 配置数组
     * 返回 批量导入C函数的数据
     */
    function parseList() {
        //获取可用的配置列表
        $list   = $this->field('name,type,value')->where(array('status'=>1))->select();
        //配置数组
        $config = array();
        //给配置名称赋值
        if($list && is_array($list)) {
            foreach ($list as $value) {
                $config[$value['name']] = self::parse($value['type'], $value['value']);
            }
        }
        return $config;
    }

    /**
     * @param $type
     * @param $value
     * @return array
     * 根据配置类型解析配置
     */
    private function parse($type, $value) {
        switch ($type) {
            //如果是数组类型 解析数组
            case 4:
                //根据换行拆分
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                //是否存在冒号
                if(strpos($value,':')) {
                    //存在的情况 冒号前边的值为键值 后边为显示值
                    $value  = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = $v;
                    }
                } else {
                    //不存在冒号
                    $value = $array;
                }
                break;
            case 6:
                $value = get_config_picture($value); break;
        }
        return $value;
    }
}