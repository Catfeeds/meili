<?php
namespace Bms\Logic;

/**
 * Class GoodsLogic
 * @package Bms\Logic
 * 商品逻辑层
 */
class GoodsLogic extends BmsBaseLogic {

    /**
     * @param array $request
     * @return array
     * 获取列表
     */
    function getList($request = array()) {
        //筛选
        if(!empty($request['goods_name']))
            $param['where']['goods.goods_name'] = array('LIKE', '%' . $request['goods_name'] . '%');
        if(!empty($request['goods_cate_id']))
            $param['where']['goods.goods_cate_id'] = $request['goods_cate_id'];
        if(!empty($request['spe_id'])) {
            $ids = M('SpecialGoods')->where(array('spe_id'=>$request['spe_id']))->field('goods_id')->select();
            if(!empty($ids))
                $param['where']['goods.id'] = array('NOT IN', array_column($ids, 'goods_id'));
        }
        if(!empty($request['is_best']))
            $param['where']['goods.is_best'] = $request['is_best'];
        //排序条件
        $param['order'] = 'goods.id DESC';
        //返回数据
        return D('Goods')->getList($param);
    }

    //TODO 彻底删除--关联删除  商品收藏记录、购物车、商品属性、商品评价
    /**
     * @param array $request
     * @return boolean
     * 彻底删除前执行 将数据存入回收站
     */
    /*protected function beforeRemove($request = array()) {
        if(api('Recycle/recovery',array($request,'Action','name'))) {
            return true;
        }
        $this->setLogicInfo('删除失败！');  return false;
    }*/

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['goods.id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }
        $row = D('Goods')->findRow($param);
        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取封面文件
        $row['cover'] = api('File/getFiles',array($row['cover']));
        //获取轮播
        $row['pictures'] = api('File/getFiles',array($row['pictures']));
        //返回数据
        return $row;
    }

    /**
     * @param array $data
     * @param array $request
     * @return array
     * 处理提交数据 进行加工或者添加其他默认数据
     */
    protected function processData($data = array(),$request = array()) {
        $data['goods_desc'] = $_POST['goods_desc'];
        /*preg_match_all('/src=\"\/?(.*?)\"/', $_POST['goods_desc'], $match);
        foreach($match[1] as $key => $src) {
            if(!strpos($src, '://')) {
                $data['goods_desc'] = str_replace('/' . $src, C('FILE_HOST') . '/' . $src . "\" width=100%", $_POST['goods_desc']);
            } else {
                $data['goods_desc'] = str_replace($src . '"', $src . '" width=100%' , $_POST['goods_desc']);
            }
        }*/
        return $data;
    }

    /**
     * @param $result
     * @param array $request
     * @return boolean
     * 更新后执行
     */
    protected function afterUpdate($result = 0, $request = array()) {
        //判断是新增商品还是修改
        //$goods_id = empty($request['id']) ? $result : $request['id'];
        //更新商品属性
        //$this->updateGoodsAttr($goods_id);
        return true;
    }

    /**
     * @param int $goods_id
     * 更新商品属性
     */
    function updateGoodsAttr($goods_id = 0) {
        if(empty($goods_id))
            return false;
        //最后要处理的商品属性列表
        $goods_attr_list = array();
        //获取商品原来的属性列表
        $original_goods_attr_list = D('Goods')->getGoodsAttrList($goods_id);
        //处理成带有增删改标记的数组
        foreach($original_goods_attr_list as $key => $goods_attr) {
            $goods_attr_list[$goods_attr['attr_id']][$goods_attr['attr_value']] = array('sign'=>'delete', 'goods_attr_id'=>$goods_attr['goods_attr_id']);
        }
        //循环新的数据  根据原来的属性数据 判断是需要增加还是修改  做相应处理
        if(isset($_POST['attr_id_list'])) {
            foreach($_POST['attr_id_list'] as $key => $attr_id) {
                $attr_value = $_POST['attr_value_list'][$key];
                $attr_price = $_POST['attr_price_list'][$key];
                if(!empty($attr_value)) {
                    //如果原来有，标记为更新  TODO 价格也没有变的情况 不需要任何操作
                    if(isset($goods_attr_list[$attr_id][$attr_value])) {
                        $goods_attr_list[$attr_id][$attr_value]['sign']         = 'update';
                        $goods_attr_list[$attr_id][$attr_value]['attr_price']   = $attr_price;
                    } else {
                        //如果原来没有，标记为新增
                        $goods_attr_list[$attr_id][$attr_value]['sign']         = 'insert';
                        $goods_attr_list[$attr_id][$attr_value]['attr_price']   = $attr_price;
                    }
                }
            }
        }
        // 处理总的属性数据  插入、更新、删除数据
        foreach($goods_attr_list as $attr_id => $attr_value_list) {
            foreach ($attr_value_list as $attr_value => $info) {
                if($info['sign'] == 'insert') {
                    M('GoodsAttribute')->data(array('attr_id'=>$attr_id,'goods_id'=>$goods_id,'attr_value'=>$attr_value,'attr_price'=>$info['attr_price']))->add();
                } elseif($info['sign'] == 'update') {
                    M('GoodsAttribute')->where(array('id'=>$info['goods_attr_id']))->data(array('attr_price'=>$info['attr_price']))->save();
                } else {
                    M('GoodsAttribute')->where(array('id'=>$info['goods_attr_id']))->delete();
                }
            }
        }
    }

    /**
     * @param $goods_id 商品ID
     * @param $attr_id_list 属性ID数组
     * @param $is_spec_list 属性是否单选属性
     * @param $value_price_list 属性值和价格数组
     * @return array
     * 调用时机：1-添加货品时选择了某个属性，但是更新商品属性时去掉了这个属性，则在这里添加上这个属性
     * 更新商品属性、返回商品属性ID数组
     */
    function handleGoodsAttr($goods_id, $attr_id_list, $is_spec_list, $value_price_list) {
        $goods_attr_id_array = array();
        //循环处理每个属性
        foreach($attr_id_list as $key => $attr_id) {
            //判断该属性是否为单选属性
            if ($is_spec_list[$attr_id] == 'false') { //不是单选属性
                $value = $value_price_list[$attr_id];
                $price = '';
            } else {
                $value_list = array(); $price_list = array();
                if($value_price_list[$attr_id]) {
                    //多行属性值+价格字符串转化为数组 chr(13)-换行
                    $vp_list = explode(chr(13), $value_price_list[$attr_id]);
                    foreach($vp_list as $v_p) {
                        $v_p_arr = explode(chr(9), $v_p); //属性值和价格的数组
                        $value_list[] = $v_p_arr[0];
                        $price_list[] = $v_p_arr[1];
                    }
                }
                $value = join(chr(13), $value_list);
                $price = join(chr(13), $price_list);
            }

            //插入或更新记录
            $goods_attr_id = M('GoodsAttribute')->where(array('goods_id'=>$goods_id,'attr_id'=>$attr_id,'attr_value'=>$value))->getField('id');
            //判断是否查到了商品属性
            if(!empty($goods_attr_id)) {
                M('GoodsAttribute')->where(array('goods_id'=>$goods_id,'attr_id'=>$attr_id,'id'=>$goods_attr_id))->data(array('attr_value'=>$value))->save();
                $goods_attr_id_array[$attr_id] = $goods_attr_id;
            } else {
                $data = array('goods_id'=>$goods_id,'attr_id'=>$attr_id,'attr_value'=>$value,'attr_price'=>$price);
                $new_goods_attr_id = M('GoodsAttribute')->data($data)->add();
            }
            if ($goods_attr_id_array[$attr_id] == '') {
                $goods_attr_id_array[$attr_id] = $new_goods_attr_id;
            }
        }
        return $goods_attr_id_array;
    }
}