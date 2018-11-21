<?php
/**
 * Created by PhpStorm.
 * Date: 2018/8/28
 * Time: 16:28
 */

namespace FrontC\Logic;


class GoodsLogic extends FrontBaseLogic
{
    /**
     * [商城获取指定分类商品调用、]
     * 获取指定一级分类及分类下的商品
     * */
    public function getCateGoods($request = array())
    {
        if (empty($request['cate_id'])) {
            //获取默认一级分类ID
            $p_id= M('GoodsCategory')->where(array('parent_id' =>0))->field('id')->find();
            $cate_id=$p_id['id'];
            if(empty($cate_id))
                return array();
        }else{
            $cate_id=$request['cate_id'];
        }
        //获取该类所有子分类和第一个分类下的商品
        $cates = M('GoodsCategory')->where(array('parent_id' => $cate_id))->field('id category_id,name')->select();
        if (isset($request['category_id'])) {
            $param = array('goods.status' => 1, 'goods_cate_id' => $request['category_id']);
        } else {
            $param = array('goods.status' => 1, 'goods_cate_id' => $cates[0]['category_id']);
        }
        $goods=D('FrontC/Goods','Model')->getGoods($param);
        $Page = new \Think\Page(count($goods), 9, $_REQUEST);
        $goodss = array_slice($goods,$Page->firstRow,$Page->listRows);
        //购物车商品数量
        $number = M('Cart')->where(array('m_id'=>$request['m_id']))->sum('number');
        $carts = empty($number) || empty($request['m_id']) ? '0' : $number;
        $result = array(
            'cates' => $cates,
            'goods' => $goodss,
            'carts' =>$carts
        );
        return $result;
    }

    /**
     * 获取热门商品
     * */
    public function getHotGoods($request = array())
    {
        if (empty($request['m_id'])) {
            $this->setLogicInfo('请先登录哦！');
            return false;
        }
        $param = array('goods.status' => 1,'goods.is_best'=>1);
        $goods =D('FrontC/Goods','Model')->getHotGoods($param);
        if(!empty($goods))
            return $goods;
        return array();
    }

    /**
     * [商城获取商品详情调用、]
     * 商品详情
     * */
    public function goodsDetail($request = array())
    {
        if (empty($request['goods_id'])) {
            $this->setLogicInfo('商品不存在！');
            return false;
        }
        $param=array('goods.id'=>$request['goods_id']);
        $goods=D('FrontC/Goods','Model')->findGoods($param);
        $result=$goods[0];
        if(empty($result))
            return $this->setLogicInfo('商品不存在！', false);
        $result['goods_desc']=path2abs($result['goods_desc']);
        //购物车商品数量
        $number = M('Cart')->where(array('m_id'=>$request['m_id']))->sum('number');
        $result['carts'] = empty($number) || empty($request['m_id']) ? '0' : $number;
        return $result;
    }

    /**
     * @param array $request
     * @return array
     * 获取商品列表
     */
    function getGoodsList($request = array()) {
        //分类查询
        if(!empty($request['goods_cate_id']))
            $param['where']['goods_cate_id'] = array('IN', $this->getChildIdArray($request['goods_cate_id']));
        //关键字查询
        if(!empty($request['keywords']))
            $param['where']['goods_name'] = array('exp', 'LIKE "%'.$request['keywords'].'%" OR keywords LIKE "%'.$request['keywords'].'%"');
        //排序
        $param['order'] = $this->_getSort($request['sort']);
        $result = D('FrontC/Goods', 'Service')->getGoodsList($param, $request);

        return $result;
    }

    /**
     * @param int $sort
     * @return string
     * 选择排序条件
     */
    private function _getSort($sort = 0) {
        switch($sort) {
            case 1: return 'goods.views DESC'; break;
            case 2: return 'goods.sales DESC'; break;
            case 3: return 'goods.id DESC'; break;
            case 4: return 'goods.price ASC'; break;
            case 5: return 'goods.price DESC'; break;
            default: return 'goods.sort DESC,goods.id DESC'; break;
        }
    }

    /**
     * @param array $request
     * @return array
     * 获取商品分类列表
     */
    function getGoodsCateList($request = array()) {
        //如果是获取全部分类先判断缓存，获取子分类直接查询
        $list = empty($request['goods_cate_id']) ? S('GoodsCategory_Cache') : false;
        if(!$list) {
            if(!empty($request['goods_cate_id']) && $request['flag'] == 1) //查下级  商品列表页头部分类
                $where['parent_id'] = $request['goods_cate_id'] == -1 ? 0 : $request['goods_cate_id'];
            if(!empty($request['goods_cate_id']) && ($request['flag'] == 2 || empty($request['flag']))) //查同级 商品列表页头部分类
                $where['parent_id'] = M('GoodsCategory')->where(array('id'=>$request['goods_cate_id']))->getField('parent_id');
            //获取数据
            $list = M('GoodsCategory')->alias('goods_cate')->field('goods_cate.id goods_cate_id,goods_cate.parent_id,goods_cate.name,goods_cate.level,file.abs_url icon')->where($where)->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = goods_cate.icon',
            ))->order('sort DESC')->select();
            //null转字符串
            $list = null2str($list);
            //获取广告
            foreach($list as &$value) {
                if($value['level'] == 1) {
                    $adverts = D('FrontC/Advert', 'Service')->getAdvert(array('position'=>2,'goods_cate_id'=>$value['goods_cate_id']));
                    $value['ad'] = empty($adverts[0]) ? (object)array() : $adverts[0];
                }
            }
            if(!empty($request['goods_cate_id']))
                return $list;
            //将数据转换成树状结构  调用分类api 生成操作html
            $list = list_to_tree($list, 0, 'goods_cate_id');
            //设置缓存
            if(empty($request['goods_cate_id']))
                S('GoodsCategory_Cache', $list);
        }
        return $list;
    }

    /**
     * @param int $root_id
     * @return mixed
     * 获取某分类下的所有子分类的ID数组(包含当前分类)
     */
    function getChildIdArray($root_id = 0) {
        //所有分类数据转化为树状格式 父节点ID为$root_id
        $tree_data = list_to_tree(M('GoodsCategory')->field('id,name,level,parent_id')->select(), $root_id);
        //转化为列表
        $result = api('Tree/getAllChild',array($tree_data, array(M('GoodsCategory')->where(array('id'=>$root_id))->field('id,name,level,parent_id')->find())));
        return $result['ids'];
    }

    /**
     * @param array $request
     * @return array
     * 商品详情
     */
    function getGoodsDetail($request = array()) {
        if(empty($request['goods_id']))
            return $this->setLogicInfo('商品不存在！', false);
        //查询商品详细
        $param['where']['goods.id'] = $request['goods_id'];
        $detail = D('FrontC/Goods', 'Service')->getDetail($param, $request);
        if(empty($detail))
            return $this->setLogicInfo('商品不存在！', false);
        //商品相册
        $pictures = empty($detail['pictures']) ? $detail['cover'] : $detail['pictures'];
        $detail['pictures'] = D('FrontC/Goods', 'Service')->getGoodsPictures(0, $pictures);
        //商品评价
        $detail['comments'] = D('FrontC/GoodsComment', 'Service')->getGoodsComments($request['goods_id'], 0, 3);
        //商品可选属性
        //$detail['goods_attr'] = D('FrontC/Goods', 'Service')->getGoodsAttr($request['goods_id']);
        //分享地址
        $detail['share_url']    = 'http://wenf-home.toocms.com';
        $detail['share_cover']  = 'http://wenf-file.toocms.com/Uploads/logo.jpg';
        $detail['share_title']  = '闻丰礼品';
        $detail['share_content'] = '闻丰礼品闻丰礼品闻丰礼品闻丰礼品闻丰礼品闻丰礼品闻丰礼品闻丰礼品闻丰礼品闻丰礼品闻丰礼品';
        //购物车商品数量 //获取购物车商品数量
        $number = M('Cart')->where(array('m_id'=>$request['m_id']))->sum('number');
        $detail['carts'] = empty($number) || empty($request['m_id']) ? 0 : $number;
        //var_dump($detail);
        //评价总数
        $detail['comm_count'] = M('GoodsComment')->where(array('goods_id'=>$request['goods_id'],'status'=>1))->count();

        return $detail;
    }

    /**
     * @param array $request
     * @return bool|string
     * 收藏商品
     */
    function goodsCollection($request = array()) {
        //参数验证
        if(empty($request['m_id']) || empty($request['goods_id']))
            return $this->setLogicInfo('参数错误！', false);
        //is_coll=1则是已收藏  要取消收藏操作
        if($request['is_coll'] == 1) {
            //删除收藏记录
            if(!M('GoodsCollection')->where(array('m_id'=>$request['m_id'], 'goods_id'=>$request['goods_id']))->delete())
                return $this->setLogicInfo('系统繁忙，稍后重试！', false);
            else
                return $this->setLogicInfo('取消收藏成功！', true);
        }
        //是否已收藏
        if(M('GoodsCollection')->where(array('m_id'=>$request['m_id'], 'goods_id'=>$request['goods_id']))->count()) {
            return $this->setLogicInfo('收藏成功！', true);
        }
        //添加收藏
        $data['m_id']           = $request['m_id'];
        $data['goods_id']       = $request['goods_id'];
        $data['create_time']    = time();
        //添加记录
        if(!m('GoodsCollection')->data($data)->add()) {
            return $this->setLogicInfo('系统繁忙，稍后重试！', false);
        } else {
            return $this->setLogicInfo('收藏成功！', true);
        }
    }
}