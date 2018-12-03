<?php

namespace FrontC\Service;

/**
 * Class CustomService
 * @package FrontC\Service
 * 首页订制内容数据服务层
 */
class CustomService extends FrontBaseService
{
    /*
     * [首页搜索、]
     * */
    public function serviceSearch($request = array())
    {
        if(empty($request['keywords']))
            return $this->setServiceInfo('请输入关键字哦',false);
        //根据关键字模糊查询服务商品和套餐
        $param['service.service_name'] = array('LIKE', '%' . $request['keywords'] . '%');
        $param['service.status']=1;
        $services=D('FrontC/Service','Model')->getListSearch($param);
        $param_p['package.package_name'] = array('LIKE', '%' . $request['keywords'] . '%');
        $param_p['package.status'] = 1;
        $packages=D('FrontC/Package','Model')->getListSearch($param_p);
        $sers=array_merge($services,$packages);
        //分页
        $Page = new \Think\Page(count($sers), 9, $_REQUEST);
        $result = array_slice($sers,$Page->firstRow,$Page->listRows);
        if(empty($result))
            return array();
        return $result;

    }

    /*
     * [mall商城首页调用、]
     * 获取商品一级分类
     * */
    public function getCateFirst()
    {
        //获取缓存中数据
        // $list = S('CateFirst_Cache');
        //不存在缓存 查找数据库
        // if (!$list) {
            $list = M('GoodsCategory')->alias('goodscate')
                ->field('goodscate.id cate_id,goodscate.icon,file.abs_url icon')
                ->where(array('goodscate.status' => 1, 'goodscate.parent_id' => 0))
                ->join(array(
                    'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = goodscate.icon',
                ))
                ->select();
            foreach ($list as &$val){
                $val['name'] = ' ';
            }
            //存入缓存
            // S('CateFirst_Cache', $list);
        // }
        if(empty($list))
            return array();
        return $list;
    }

    /**
     * [index首页调用]
     * 栏目列表
     */
    public function getChannel()
    {
        //获取缓存中数据
        // $list = S('Channel_Cache');
        //不存在缓存 查找数据库
        // if (!$list) {
            $list = M('ServiceCategory')->alias('servicecate')
                ->field('servicecate.id cate_id,servicecate.icon,file.abs_url icon')
                ->where(array('servicecate.status' => 1, 'servicecate.parent_id' => 0))
                ->join(array(
                    'LEFT JOIN ' . C('DB_PREFIX') . 'file file ON file.id = servicecate.icon',
                ))
                ->order('servicecate.sort DESC')
                ->select();
            foreach ($list as &$val){
                $val['name'] = ' ';
            }
            //计入缓存
            // S('Channel_Cache', $list);
        // }
        if(empty($list))
            return array();
        return $list;
    }

    /**
     * [index首页调用、mall商城首页调用]
     * 版块列表
     */
    public function getSection($request = array())
    {
        $position = $request['position'];
        //获取缓存中数据
        // $list = S('Section_Cache' . $position);
        //不存在缓存 查找数据库
        // if (!$list) {
            $list = M('Section')->field('name,layout,configure')->where(array('status' => 1, 'position' => $position))->order('sort DESC')->select();
            foreach ($list as &$value) {
                //解析版块配置json
                $configure = json_decode($value['configure'], true);
                //处理每小版块的图片
                foreach ($configure as &$config) {
                    $config['cover'] = api('File/getFiles', array($config['cover']))[0]['abs_url'];
                }
                $value['configure'] = $configure;
            }
            //计入缓存
            // S('Section_Cache' . $position, $list);
        // }
        if(empty($list)){
            return array();
        }
        return $list;
    }
}