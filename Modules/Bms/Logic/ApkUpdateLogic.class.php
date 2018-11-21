<?php
/**
 * Created by PhpStorm.
 * User: toocms
 * Date: 2018/11/9
 * Time: 14:10
 */

namespace Bms\Logic;


class ApkUpdateLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取行为列表
     */
    function getList($request = array()) {
        //排序条件
//        $param['order']     = 'create_time DESC';
        //页码
        $param['page_size'] = C('LIST_ROWS');
        //返回数据
        $result =  D('ApkUpdate')->getList($param);
        foreach($result['list'] as &$value){
            $value['description'] = htmlspecialchars_decode($value['description']);
        }
        return $result;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        //ID条件
        if(!empty($request['id'])) {
            $param['where']['art.id'] = $request['id'];
        } else {
            $this->setLogicInfo(L('_PARA_ERR_')); return false;
        }
        //别名
        $param['alias'] = 'art';
        //查询的字段
        $param['field'] = 'art.*';
        //关联表
        //$param['join']  = array('LEFT JOIN '.C('DB_PREFIX').'article_category art_cate ON art_cate.id = art.art_cate_id',);

        $row = D('ApkUpdate')->findRow($param);

        if(!$row) {
            $this->setLogicInfo('发生错误！'); return false;
        }
        //获取封面文件
        $row['url'] = api('File/getFiles',array($row['url']));

        //返回数据
        return $row;
    }
}