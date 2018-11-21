<?php
namespace FrontC\Model;

/**
 * Class AdvertModel
 * @package FrontC\Model
 * 广告数据模型
 */
class AdvertModel extends FrontBaseModel {

    /**
     * [index首页调用]
     * @param array $param
     * @return array
     * 广告列表
     */
    public function getList($param = array()) {
        return $this->alias('ad')
                     ->field('ad.id ad_id,ad.position,ad.goods_cate_id,ad.target_rule,ad.param,ad.start_time,ad.end_time,file.abs_url')
                     ->where(array('ad.status'=>1))
                     ->order('ad.sort DESC,ad.id DESC')
                     ->join(array(
                         'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = ad.picture',
                     ))
                     ->select();
    }
}