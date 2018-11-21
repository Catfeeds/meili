<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/13
 * Time: 14:17
 */

namespace FrontC\Model;


class BankModel extends FrontBaseModel
{
    /**
     * 银行列表
     * */
    public function getBanks($param=array())
    {
        $banks=$this->alias('bank')
            ->field('bank.id bank_id,bank.name,bank.short,bank.logo,file.abs_url logo')
            ->where($param)
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = bank.logo',
            ))
            ->select();
        if(!empty($banks)){
            return $banks;
        }else{
            return array();
        }
   }

    /**
     * [获取单条信息]
     * @param array $param
     * @return array
     */
    public function getRow($param=array())
    {
        $bank=$this->alias('bank')
            ->field('bank.id bank_id,bank.name,bank.short,bank.logo,file.abs_url logo')
            ->where($param)
            ->join(array(
                'LEFT JOIN '.C('DB_PREFIX').'file file ON file.id = bank.logo',
            ))
            ->find();
        if(!empty($bank)){
            return $bank;
        }else{
            return array();
        }
    }
}