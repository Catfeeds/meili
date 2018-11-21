<?php
namespace Common\Base;
use Think\Model;

/**
 * Class BaseModel
 * @package Common\Base
 * 整站数据模型总父类 所有Model类的顶层父类
 */
class BaseModel extends Model {

    /**
     * @param array $param
     * @return array
     * 获取列表 公用方法
     */
    function getList($param = array()) {
        //分页对象
        $Page = null;
        //是否分页
        if(!empty($param['page_size'])) {
            //数据总数
            $total  = $this->alias($param['alias'])->where($param['where'])->count();
            //获取分类对象
            $Page   = $this->getPage($total, $param['page_size'], $_REQUEST);
        }
        //数据列表
        $list  = $this->_getCURD($param, $Page)->select();
        //返回列表和分页信息
        if(!empty($param['page_size']))
            return array('list' => $list, 'page' => $Page->show());
        else
            return $list;
    }

    /**
     * @param array $param
     * @return mixed
     * 一行数据
     */
    function findRow($param = array()) {
        return $this->_getCURD($param, null)->find();
    }

    /**
     * @param $param
     * @param $Page
     * @return BaseModel
     * 获取数据库连贯操作
     */
    final private function _getCURD($param = array(), $Page = null) {
        $model = $this;
        //表别名 字符串
        !empty($param['alias'])         ? $model = $model->alias($param['alias']) : '';
        //强制索引  参数为数组
        //!empty($param['force'])        ? $model = $model->force($param['force']) : '';
        //去掉重复记录 true/false
        //!empty($param['distinct'])      ? $model = $model->distinct($param['distinct']) : '';
        //指定当前数据表 字符串/数组 可用于多表操作  'think_user user,think_role role'  array('think_user'=>'user','think_role'=>'role')
        //!empty($param['table'])         ? $model = $model->table($param['table']) : '';
        //查询字段  两个参数  字符串/数组 ,true/false  true代表过滤
        !empty($param['field'])         ? $model = $model->field($param['field'], $param['field_filter']) : '';
        //查询条件 数组
        !empty($param['where'])         ? $model = $model->where($param['where']) : '';
        //左、右、并 连接查询  统一使用数组 array('','')
        !empty($param['join'])          ? $model = $model->join($param['join']) : '';
        //归并 两个参数 合并两个或多个 SELECT 语句的结果集。 统一使用数组array('SELECT name FROM think_user_1','SELECT name FROM think_user_2')  array,true/false true代表unionall
        //!empty($param['union'])         ? $model = $model->union($param['union'],$param['union_all']) : '';
        //排序 字符串
        !empty($param['order'])         ? $model = $model->order($param['order']) : '';
        //分页
        !empty($param['page_size']) && $Page  ? $model = $model->limit($Page->firstRow, $Page->listRows) : '';
        //分组查询 字符串 支持多字段分组  'name,score'
        //!empty($param['group'])         ? $model = $model->group($param['group']) : '';
        //和group共用  查询条件  字符串
        //!empty($param['having'])        ? $model = $model->having($param['having']) : '';
        //只返回SQL语句不执行 true/false  默认false,  为true时只返回sql语句
        !empty($param['fetchSql'])      ? $model = $model->fetchSql($param['fetchSql']) : '';

        return $model;
    }

    /**
     * @param int $total 总数
     * @param int $page_size 每页记录数
     * @param array $parameter 拼接参数
     * @return \Think\Page 分页对象
     */
    protected function getPage($total = 0, $page_size = 0, $parameter = array()) {
        //初始化分页对象
        $Page = new \Think\Page($total, $page_size, $parameter);
        //分页样式配置
        if($total > $page_size) {
            $Page->setConfig('theme', $this->getPageTheme());
        }
        return $Page;
    }

    /**
     * @param $value 验证字段的值
     * @param $field 验证字段的名称 该名称在“验证时间”后设置 数组的形式
     * @return bool
     * 自动验证时 验证是否已存在记录  若表中删除为假删除 则验证名称、标题等是否已存在则 使用该方法
     */
    protected function checkUnique($value, $field) {
        //获取主键名称
        $pk = $this->getPk();
        //判断提交数据中是否存在主键值 如果存在则是修改的情况
        if(!empty($_REQUEST[$pk])) {
            //如果原值等于现值  不在进行下一步判断
            if($this->where(array($pk => $_REQUEST[$pk]))->getField($field) == $value)
                return true;
        }
        //判断现值是否存在
        if($this->where(array('' . $field . '' => $value))->count() > 0)
            return false;
        return true;
    }

    /**
     * @param string $alias
     * @param $param
     * @param null $Page 分页类对象
     * @param array $join
     * 特殊查询 先获取满足条件的ID数组 再议ID为条件查询
     * 根据条件先只查询ID数组 然后用IN方法查询数据 利用ID的主键索引优化查询
     */
    protected function specialSearch($param, $Page = null, $alias = '', $join = array()) {
        //数据对象
        $model                          = $this;
        //要查询的字段 此方法只查询ID
        $field                          = 'id';
        //判断是否需要设置表别名
        if(!empty($alias)) {
            $model  = $model->alias($alias); //设置表别名
            $field  = $alias . '.' . $field; //字段添加别名
        }
        //获取查询 排序条件下的ID列表
        $model                          = $model->field($field)->where($param['where'])->order($param['order']);
        //是否分页
        !($Page == null) ? $model       = $model->limit($Page->firstRow, $Page->listRows) : '';
        //是否关联
        !empty($join)    ? $model       = $model->join($join) : '';
        //获取ID列表
        $id_list                        = $model->select();
        //数组降维
        $ids                            = array_column(array_merge($id_list, array(array('id'=>0))), 'id');
        //查询ID 在数组中的结果集
        $param['special_where'][$field] = array('exp', ' IN (' . implode(',', $ids) . ')');
        //结果数组排序时使用
        $param['ids_for_sort']          = $ids;
        //返回参数
        //var_dump($model->_sql());
        return $param;
    }
}