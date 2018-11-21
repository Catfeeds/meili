<?php
namespace Common\Api;

/**
 * Class RecycleApi
 * @package Common\Api
 * 回收站操作接口
 * 若 不想彻底从数据库删除记录 则先将数据放入回收站 在进行删除
 * 存储的数据格式为json
 * 关联配置的格式 model模型名称  relation_field关联字段配置数组（this:该行配置的关联字段 parent:关联上级的字段名称） _child下级关联数据配置
 * $relation_del_conf = array(
 *     array('model'=>'Aa','relation_field'=>array('this'=>'','parent'=>''),'_child'=>array(array('model'=>'Aaa','relation_field'=>array('this'=>'','parent'=>'')))),
 *     array('model'=>'Bb','relation_field'=>array('this'=>'','parent'=>''))
 * );
 */
class RecycleApi {

    /**
     * @var array
     * 关联删除回收数据记录的ID数组
     */
    public static $relation_records = array();

    /**
     * @param $request  删除时的参数
     * @param $model    数据模型名称
     * @param $show_field  列表展示字段数据
     * @param array $relation_del_conf  关联删除配置
     * @return bool
     * 回收数据 判断ID条件 数组还是字符
     */
    public static function recovery($request, $model, $show_field, $relation_del_conf = array()) {
        //判断ID是数组还是字符  如果是数组循环加入
        if(is_array($request['ids'])) {
            //数组ID  先获取回收数据列表
            $recovery_list = M($model)->where(array('id' => array('IN',$request['ids'])))->select();
            //循环插入 回收站
            foreach($recovery_list as $recovery_data) {
                //判断插入是否成功 不成功返回false
                if(!self::_doRecovery($recovery_data, $model, $show_field, $relation_del_conf)) {
                    return false;
                }
            }
            //全部成功后返回 true
            return true;
        } elseif (is_numeric($request['ids'])) {
            //一条数据的情况
            $recovery_data = M($model)->where(array('id' => $request['ids']))->find();
            return self::_doRecovery($recovery_data, $model, $show_field, $relation_del_conf);
        }
    }


    /**
     * @param array $recovery_data  回收数据
     * @param $model   数据模型名称
     * @param $show_field   列表展示字段数据
     * @param array $relation_del_conf 关联删除配置
     * @return bool
     * 执行回收
     */
    private function _doRecovery($recovery_data, $model, $show_field, $relation_del_conf) {
        //判断回收数据是否重复 如果重复直接返回true
        if(M('Recycle')->where(array('model'=>$model, 'record_id'=>$recovery_data['id']))->count()) {
            return true;
        }
        //创建数据 顶级回收数据
        $data = array(
            'model'         => $model, //模型名称
            'record_id'     => $recovery_data['id'],  //回收的数据ID
            'data'          => serialize($recovery_data),  //json或序列化存储     最好是序列化存储  但是反序列化有时出现问题  //TODO 序列化存储
            'create_time'   => time(), //回收时间
            'admin_id'      => AID, //回收者ID
            'show_data'     => $recovery_data[$show_field],//列表展示字段数据
        );

        //判断 关联删除数组是否为空  不为空进入关联删除
		if(!empty($relation_del_conf)) {
            //已经记录并删除的回收数据 其顶级记录未记录成功
            $has_records = self::_hasRecords($model, $recovery_data['id']);
            //TODO 关联删除时 循环存储问题  可以多层关联问题
            if(self::_relationDelete($relation_del_conf, $recovery_data, $model, $recovery_data['id'])) {
                //TODO 并上已记录并且已删除的回收数据的记录的ID字符串
                $data['relation_records'] = $has_records.implode(',', self::$relation_records);
            } else {
                return false;
            }
		}
        //存入回收站
        $res = M('Recycle')->data($data)->add();
        //成功
        if($res) {
            return true;
        }
        return false;
    }

    /**
     * @param array $relation_del_conf  关联删除配置
     * @param array $recovery_data  父删除数据
     * @param string $top_model 顶级记录模型名称
     * @param $top_id 顶级记录ID
     * @return bool
     * 关联删除的方法 递归
     */
    private function _relationDelete($relation_del_conf, $recovery_data, $top_model, $top_id) {
        //循环关联配置数组
        foreach($relation_del_conf as $conf) {
            //获取关联模型的回收数据列表
            $recovery_list = M($conf['model'])->where(array(''.$conf['relation_field']['this'].'' => $recovery_data[$conf['relation_field']['parent']]))->select();
            //如果数据为空 进入下一个关联
            if(empty($recovery_list)) {
                continue;
            }
            //循环删除
            foreach($recovery_list as $row) {
                //判断回收数据是否存在记录未删除的情况
                $ryl_id = M('Recycle')->where(array('model' => $conf['model'], 'record_id' => $row['id']))->getField('id');
                if($ryl_id) {
                    //删除原数据表数据
                    if(!M($conf['model'])->where(array('id' => $row['id']))->delete()) {
                        return false;
                    }
                    //记录回收数据回收站记录的ID
                    self::$relation_records[] = $ryl_id;
                } else {
                    //创建插入数据
                    $data = array(
                        'model'             => $conf['model'], //模型名称
                        'record_id'         => $row['id'],  //回收的数据ID
                        'data'              => serialize($row), //json或序列化存储     最好是序列化存储  但是反序列化有时出现问题  //TODO 序列化存储
                        'is_relation_del'   => 1,  //是否关联删除的数据
                        'top_model'         => $top_model,//顶级回收的模型名称
                        'top_id'            => $top_id,//顶级回收的数据ID
                        'create_time'       => time(), //删除时间
                        'admin_id'          => AID, //操作者ID
                    );

                    //添加到回收站
                    $record = M('Recycle')->data($data)->add();
                    //添加成功 则删除原数据表数据
                    if ($record) {
                        //删除原数据表数据
                        if (!M($conf['model'])->where(array('id' => $row['id']))->delete()) {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    //记录回收数据回收站记录的ID
                    self::$relation_records[] = $record;
                }
                //是否有子关联删除
                if (!empty($conf['_child'])) {
                    //递归
                    self::_relationDelete($conf['_child'], $row, $top_model, $top_id);
                }
            }
        }
        return true;
    }

    /**
     * @param $top_model
     * @param $top_id
     * @return string
     * 已经回收并且删除成功的数据  情况：顶级记录未成功
     */
    private function _hasRecords($top_model, $top_id) {
        //获取顶级模型、id的关联删除数据
        $result = D('Recycle')->where(array('top_model' => $top_model,'top_id' => $top_id))->getField('id,model');
        //没有数据返回空字符
        if(empty($result)) {
            return '';
        }
        //获取键值数组
        $relation_records = array_keys($result);
        //返回字符串
        return implode(',', $relation_records) . ',';
    }


    /**
     * @param int $id
     * 数据还原
     */
    public static function restore($id = 0) {

    }
}