<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/12
 * Time: 10:41
 */

namespace FrontC\Service;


class RegionService extends FrontBaseService
{
    /*
     * 获取省市区调用
     * */
    public function getRows($param=array())
    {
        if(empty($param)){
            return array();
        }
        if($param['type'] == 1){
            $where=array('region_type'=>$param['type']);
        }else{
            $where=array('region_type'=>$param['type'],'parent_id'=>$param['regionid']);
        }
        $result=M('Region')->where($where)->field('id regionid,all_name name')->select();
        if (empty($result))
            return array();
        return $result;
   }
}