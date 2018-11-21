<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/10
 * Time: 11:24
 */

namespace FrontC\Model;


class HelpDocModel extends FrontBaseModel
{
    /**
     * [个人中心获取全部文档调用、]
     * */
    public function getRows()
    {
        $docs = $this->where(array('status'=>1))->field('id doc_id,title')->select();
        if(empty($docs))
            return array();
        return $docs;
  }

  /**
   * [个人中心帮助文档详情调用]
   * */
    public function getRow($param=array())
    {
        $doc = $this->where($param)->field('title,content')->find();
        if(empty($doc))
            return array();
        return $doc;
  }
}