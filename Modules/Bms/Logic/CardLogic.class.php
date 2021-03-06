<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/20
 * Time: 20:59
 */

namespace Bms\Logic;


class CardLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['card_name']))
            $param['where']['card.card_name'] = array('LIKE', '%' . $request['card_name'] . '%');
        //排序条件
        $param['order'] = 'card.id DESC';
        //返回数据
        return D('Card')->getList($param);
    }
    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(), $request = array()) {
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time']   = strtotime($data['end_time']);
        return $data;
    }
    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['card.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        $row = D('Card')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取封面文件
        $row['cover'] = api('File/getFiles',array($row['cover']));
        //获取背景图
        $row['bg_cover'] = api('File/getFiles',array($row['bg_cover']));
        //返回数据
        return $row;
    }

    /*
     *更新操作完成后将选择的商品存入一卡通商品表
     * */
    function afterUpdate($result = 0, $request = array())
    {
        //根据一卡通ID 传的服务商品ID添加或者删除一卡通商品
        //存在商品
        if(!empty($request['service_ids'])){
            //先删除掉该卡的所有商品 然后添加重新编辑加入的商品
            M('CardService')->where(array('card_id'=>$request['id']))->delete();
            //添加选择的商品
            $service_ids=$request['service_ids'];
            foreach ($service_ids as $k=>$v){
                $service_info=M('Service')->field('id service_id,service_name,count,service_sn,cover,price,service_short_desc')->find($v);
                $cover_path=M('File')->where(array('id'=>$service_info['cover']))->getField('abs_url');
                $service_info['cover']=$cover_path;
                $service_info['card_id']=$request['id']? $request['id']:$result;
                M('CardService')->data($service_info)->add();
            }
        }
        return parent::afterUpdate($result, $request); // TODO: Change the autogenerated stub
    }

    protected function afterRemove($result = 0, $request = array())
    {
        //删除该一卡通下面的一卡通服务商品
        if(!empty($request['ids'])){
           M('CardService')->where(array('card_id'=>$request['ids']))->delete();
        }
        return parent::afterRemove($result, $request); // TODO: Change the autogenerated stub
    }

}