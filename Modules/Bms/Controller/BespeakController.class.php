<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/13
 * Time: 15:46
 */

namespace Bms\Controller;


class BespeakController extends BmsBaseController
{
    /**
     * 确认预约完成
     * */
    public function bespeakSussessDo($id)
    {
        if($id == 0){
            //将全部符合状态的已经预约待服务改为预约服务完成
            $ids=M('Bespeak')->where(array('status'=>1))->field('id')->select();
            if(!empty($ids)){
                foreach ($ids as $k=>$v){
                    $data['id']=$v['id'];
                    $data['status']=3;
                    $result=M('Bespeak')->data($data)->save();
                }
                if($result > 0){
                    $this->success('操作成功！','/Bespeak/index');
                }else{
                    $this->error('系统繁忙！','/Bespeak/index');
                }
            }else{
                $this->redirect('/Bespeak/index');
            }
        }else{
            //将指定预约待服务改为预约服务完成
            //验证状态是否可以执行该操作
            $status=M('Bespeak')->where(array('id'=>$id))->getField('status');
            if($status == 1){
                $data['id']=$id;
                $data['status']=3;
                $result=M('Bespeak')->data($data)->save();
                if($result > 0){
                    $this->success('操作成功！','/Bespeak/index');
                }else{
                    $this->error('系统繁忙！','/Bespeak/index');
                }
            }else{
                $this->error('该状态不可以操作！','/Bespeak/index');
            }
        }
  }
}