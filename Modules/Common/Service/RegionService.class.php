<?php
namespace Common\Service;
use Common\Base\BaseService;

/**
 * Class RegionService
 * @package Common\Service
 * 地区公共服务层 获取地区列表
 */
class RegionService extends BaseService {

    /**
     * @param array $custom_param
     * @return array
     * 获取列表
     */
    function select($custom_param = array()) {
        //表别名
        $param['alias']                 = 'reg';
        //查询条件
        $param['where']['status']       = 1;
        //查询的字段
        $param['field']                 = 'reg.id reg_id,reg.region_name,reg.region_type,reg.parent_id';

        //是否有外部其他自定义条件  如果有替换条件
        if(!empty($custom_param)) {
            $param = $this->customParam($param, $custom_param);
        }

        //调用 公共数据模型层方法获取数据
        $result = D('Common/Region')->getList($param);

        //
        //foreach($result as &$value) {}

        //缓存设置

        return $result;
    }
}