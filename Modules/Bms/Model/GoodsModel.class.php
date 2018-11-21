<?php
namespace Bms\Model;

/**
 * Class GoodsModel
 * @package Bms\Model
 * 商品数据层
 */
class GoodsModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('goods_name', 'require', '请输入商品名称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('goods_sn', 'require', '请输入商品货号！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('is_best', 'require', '请设置是否热门！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('goods_sn', '/^[a-zA-Z0-9]\w{0,39}$/', '商品货号必须是英文数字！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('goods_sn', 'checkUnique', '该货号已经存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('goods_sn')),
        array('goods_cate_id', 'require', '请选择商品分类！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('price', 'require', '请输入商品售价！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('market_price', 'require', '请输入商品原价！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('stock', 'require', '请输入商品库存！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cover', 'require', '请上传商品封面图！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('goods_desc', 'require', '请输入商品图文描述！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    /**
     * @param array $param  综合条件参数
     * @return array
     */
    function getList($param = array()) {
        //数据总数
        $total  = $this->alias('goods')->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param = $this->specialSearch($param, $Page, 'goods');
        //获取数据
        $list  = $this->alias('goods')
                    ->field('goods.id,goods.goods_name,goods.is_best,goods.goods_sn,goods.goods_cate_id,goods.price,goods.market_price,goods.stock,goods.is_on_sale,goods.is_integral,
                    goods.is_best,goods.sort,goods.type_id,goods.update_time,goods.status,goods_cate.name goods_cate_name')
                    ->where($param['special_where'])
                    ->join(array(
                        'LEFT JOIN '.C('DB_PREFIX').'goods_category goods_cate ON goods_cate.id = goods.goods_cate_id',
                    ))
                    ->select();
        //返回数据
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }

    /**
     * @param $relation_goods
     * @return mixed
     * 获取文章关联商品
     */
    function getRelationGoods($relation_goods = '') {
        //获取数据
        return $this->alias('goods')
                    ->field('goods.id,goods.goods_name,goods.price,goods.status,file.abs_url')
                    ->where(array('goods.id'=>array('IN', $relation_goods)))
                    ->join(array(
                        'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = goods.cover',
                    ))
                    ->select();
    }

    /**
     * @param array $param
     * @return mixed|void
     */
    function findRow($param = array()) {
        return $this->alias('goods')
                    ->field('goods.id,goods.goods_name,goods.is_best,goods.goods_sn,goods.goods_cate_id,goods.price,goods.market_price,goods.stock,goods.unit,goods.keywords,goods.goods_desc,
                    goods.cover,goods.pictures,goods.type_id,goods.is_on_sale,goods.is_integral,goods.is_best,goods.sort,goods.update_time,goods.status')
                    ->where($param['where'])
                    ->join(array())
                    ->find();
    }

    /**
     * @param int $type_id
     * @param int $goods_id
     * @return string
     * 根据属性数组创建属性的表单
     */
    function buildAttrForm($type_id = 0, $goods_id = 0) {
        //获取属性、商品属性 信息
        $attributes = $this->getAttributes($type_id,$goods_id);
        if(empty($attributes))
            return '';
        //属性表单HTML
        $html = '';
        //循环每条属性信息  生成属性表单
        foreach ($attributes as $key => $value) {
            $html .= '<div class="control-group">
                        <label class="control-label">' . $value['attr_name'] . '</label>
                        <div class="controls">
                        <input type="hidden" name="attr_id_list[]" value="' . $value['attr_id'] . '"/>';
            //根据输入类型 判断表单类型
            if($value['attr_input_type'] == 1) { //小文本框输入类型
                $html .= '<input type="text" class="text-width-20" name="attr_value_list[]" value="' . htmlspecialchars($value['attr_value']) . '"/>';
            } elseif($value['attr_input_type'] == 2) { //大文本框输入类型
                $html .= '<textarea name="attr_value_list[]">' . htmlspecialchars($value['attr_value']) . '</textarea>';
            } else { //下拉选择输入类型
                $html .= '<select name="attr_value_list[]" class="select-height-35">
                            <option value="0">--请选择--</option>';
                //根据换行符 将属性值字符串转化成数组
                $attr_values = explode("\n", $value['attr_values']);
                //循环值数组 生成下拉选择项
                foreach ($attr_values as $av) {
                    $av    = trim(htmlspecialchars($av));
                    //如果属性值=选择项 则选中
                    $html .= $value['attr_value'] != $av ? '<option value="' . $av . '">' . $av . '</option>' : '<option value="' . $av . '" selected="selected">' . $av . '</option>';
                }
                $html .= '</select>';
            }
            //根据属性类型 判断是否有属性价格和多项输入
            if($value['attr_type'] == 2 || $value['attr_type'] == 3) {
                $html .= '　　<span class="">
                            属性价格　<input type="text" class="text-width-10" name="attr_price_list[]" value="' . $value['attr_price'] . '"/>
                            </span>　';
                //根据 attr_id标记 判断是添加行 还是 删除行 操作
                $html .= $attributes[$key-1]['attr_id'] != $value['attr_id'] ? '<a class="spec" href="javascript:void(0);" onclick="addSpec(this);"><span>[+]</span></a>' : '<a class="spec" href="javascript:void(0);"  onclick="removeSpec(this)"><span>[-]</span></a>';
            } else {
                $html .= '<input type="hidden" name="attr_price_list[]" value="0"/>';
            }
            $html .= '</div></div><div class="add-area"></div>';
        }
        return $html;
    }

    /**
     * @param int $type_id
     * @param int $goods_id
     * @return mixed
     * 如果没有商品ID则获取的的是某类型下的所有属性信息
     * 如果有商品ID则获取的是商品属性信息及对应的属性信息
     */
    function getAttributes($type_id = 0, $goods_id = 0) {
        //商品类型为空 返回空数据
        if(empty($type_id))
            return array();
        //获取属性信息 商品属性信息
        $attributes = M('Attribute')->alias('attr')
                    ->field('attr.id attr_id,attr.attr_name,attr.attr_input_type,attr.attr_type,attr.attr_values,goods_attr.attr_value,goods_attr.attr_price')
                    ->order('attr.attr_type,attr.id,goods_attr.attr_price,goods_attr.id')
                    ->where(array('attr.type_id'=>array('exp', '='.$type_id.' OR attr.type_id=0')))
                    ->join(array(
                        'LEFT JOIN ' . C('DB_PREFIX') . 'goods_attribute goods_attr ON goods_attr.attr_id=attr.id AND goods_attr.goods_id='.$goods_id.'',
                    ))
                    ->select();
        return $attributes;
    }

    /**
     * @param int $goods_id
     * @return mixed
     * 添加、编辑商品时 取得原有的商品属性值
     */
    function getGoodsAttrList($goods_id = 0) {
        $goods_attr = M('GoodsAttribute')->alias('goods_attr')
                ->field('goods_attr.id goods_attr_id,goods_attr.goods_id,goods_attr.attr_id,goods_attr.attr_value,
                goods_attr.attr_price,attr.attr_type')
                ->where(array('goods_attr.goods_id'=>$goods_id))
                ->join(array(
                    'LEFT JOIN ' . C('DB_PREFIX') . 'attribute attr ON attr.id = goods_attr.attr_id'
                ))
                ->select();
        return $goods_attr;
    }

    /**
     * @param int $goods_id
     * @return array|mixed
     * 获得商品已添加属性中 属性类型为单选属性（即可选规格）的商品属性列表
     */
    function getSpecGoodsAttrList($goods_id = 0) {
        if(empty($goods_id))
            return array();
        $goods_attr = M('GoodsAttribute')->alias('goods_attr')
                ->field('goods_attr.id goods_attr_id,goods_attr.attr_id,goods_attr.attr_value,goods_attr.attr_price,attr.attr_name')
                ->where(array('goods_attr.goods_id'=>$goods_id,'attr.attr_type'=>2))
                ->join(array(
                    'LEFT JOIN ' . C('DB_PREFIX') . 'attribute attr ON attr.id = goods_attr.attr_id'
                ))
                ->order('goods_attr.attr_id ASC')
                ->select();

        return $goods_attr;
    }

    /**
     * @param $goods_attr_id_array
     * @param string $sort
     * @return array
     * 将$goods_attr_id_array 的序列按照 attr_id 重新排序
     * 注意：非规格属性的id会被排除
     */
    function sortGoodsAttrIdArray($goods_attr_id_array, $sort = 'ASC') {
        //ID数组判空
        if(empty($goods_attr_id_array)) {
            return $goods_attr_id_array;
        }
        //获取只有单选属性的商品属性信息
        $list = M('Attribute')->alias('attr')
                            ->field('goods_attr.id goods_attr_id,goods_attr.attr_value,attr.attr_type')
                            ->where(array('goods_attr.id'=>array('IN', $goods_attr_id_array)))
                            ->join(array(
                                'LEFT JOIN ' . C('DB_PREFIX') . 'goods_attribute goods_attr ON goods_attr.attr_id = attr.id AND attr.attr_type = 2'
                            ))
                            ->order('attr.id ' . $sort . '')
                            ->select();
        $goods_attr_id_array = array();
        foreach($list as $value) {
            $goods_attr_id_array['sort'][] = $value['goods_attr_id'];
            //$return_arr['row'][$value['goods_attr_id']] = $value;
        }
        return $goods_attr_id_array;
    }

    /**
     * 获取商品类型中包含单选属性（即存在可选规格的）的类型列表
     */
    function getSpecGoodsType(){
        $types = M('Attribute')->where(array('attr_type'=>2))->field('type_id')->distinct('type_id')->select();
        return array_column($types, 'type_id');
    }
}