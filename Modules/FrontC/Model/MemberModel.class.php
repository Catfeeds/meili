<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/13
 * Time: 10:50
 */

namespace FrontC\Model;


class MemberModel extends FrontBaseModel
{
    public function getBalance($parm=array())
    {
        $balance=$this->where($parm)->getField('balance');
        if(!empty($balance)){
            return $balance;
        }else{
            return '0';
        }
    }

//    public function getOneInfo($param=array())
//    {
//        $info=$this->alias('member')
//            ->field('member.id m_id,member.nickname,file.abs_url head')
//            ->where($param)
//            ->join(array(
//                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = member.head',
//            ))
//            ->find();
//        if(!empty($info)){
//            return $info;
//        }else{
//            return array();
//        }
//    }
}