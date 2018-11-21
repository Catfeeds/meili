<?php
namespace Bms\Controller;

/**
 * Class GoodsController
 * @package Bms\Controller
 * 商品控制器
 */
class GoodsController extends BmsBaseController {

    function getIndexRelation() {
        //$this->assign('spec_types', D('Goods')->getSpecGoodsType());
        //获取商品分类
        $this->assign('select', D('GoodsCategory','Logic')->getSelect('goods_cate_id', I('request.goods_cate_id')));
    }

    function getUpdateRelation() {
        //获取商品类型
        //$this->assign('type_list', D('GoodsType')->getList(array('field'=>'id type_id,type_name')));
        //获取商品分类
        $row = $this->get('row');
        $this->assign('select', D('GoodsCategory','Logic')->getSelect('goods_cate_id', $row['goods_cate_id']));
        //获取商品属性
        //$this->assign('goods_attr_form', D('Goods')->buildAttrForm($row['type_id'], $row['id']));
    }

    function getAddRelation() {
        //获取商品类型
        //$this->assign('type_list', D('GoodsType')->getList(array('field'=>'id type_id,type_name')));
        //获取商品分类
        $this->assign('select', D('GoodsCategory','Logic')->getSelect('goods_cate_id', ''));
    }

    /**
     * 切换商品类型 获取属性表单
     */
    public function ajaxAttrForm(){
        $goods_id   = empty($_POST['goods_id']) ? 0 : $_POST['goods_id'];
        $type_id    = empty($_POST['type_id']) ? 0 : $_POST['type_id'];
        //执行创建方法
        $html = D('Goods')->buildAttrForm($type_id, $goods_id);
        $this->ajaxReturn(array('form'=>$html), 'JSON');
    }

    /**
     * 货品列表
     */
    function products() {
        $goods_id = I('get.id');
        //获取商品信息
        $goods = M('Goods')->where(array('id'=>$goods_id))->field('id goods_id,goods_name,goods_sn')->find();
        $this->assign('goods', $goods);
        //获取商品属性单选属性 即可选规格的属性列表
        $goods_attr_list = D('Goods')->getSpecGoodsAttrList($goods_id);
        //转换成其他格式数组
//        attr_id =>
//            array
//                'attr_id' => string '5'
//                'attr_name' => string '宽度'
//                'attr_values' =>
//                    array
//                        0 => string '50cm'
//                        1 => string '100cm'
        foreach($goods_attr_list as $goods_attr){
            $_goods_attr_list[$goods_attr['attr_id']]['attr_id']        = $goods_attr['attr_id'];
            $_goods_attr_list[$goods_attr['attr_id']]['attr_name']      = $goods_attr['attr_name'];
            $_goods_attr_list[$goods_attr['attr_id']]['attr_values'][]  = $goods_attr['attr_value'];
        }
        $this->assign('goods_attr_list', $_goods_attr_list);
        //获取货品列表
        $products = D('Products', 'Logic')->getProducts($goods_id);
        $this->assign('products', $products);

        $this->display('products');
    }

    /**
     * 为商品列表弹出层使用
     */
    function ajaxGetGoods() {
        if(IS_POST) {
            $result = self::$logicObject->getList(I('request.'));
            if($result) {
                $this->success('success', '', true, $result['list']);
            } else {
                $this->error(self::$logicObject->getLogicInfo());
            }
        } else {
            $this->error('');
        }
    }
}