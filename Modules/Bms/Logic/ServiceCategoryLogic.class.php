<?php
/**
 * Created by PhpStorm.
 * Date: 2018/9/20
 * Time: 18:41
 */

namespace Bms\Logic;


class ServiceCategoryLogic extends BmsBaseLogic
{
    /**
     * @param array $request
     * @return array
     * 获取文章分类列表
     */
    function getList($request = array()) {
        //判断是否存在缓存  缓存在分类 修改或添加后清空
        $result = false;//S('GoodsCategory_Cache');
        if(!$result) {
            //查询的字段
            $param['field'] = 'id,parent_id,name,sort,icon,description,status';
            //获取数据
            $result = D('ServiceCategory')->getList($param);
            //设置缓存
            //S('GoodsCategory_Cache', $result);
        }

        //将数据转换成树状结构  调用分类api 生成操作html
        $tree_data = list_to_tree(api('Category/handleOperate',array($result,'ServiceCategory',$this->_getProhibit(),$this->_getAllow())));

        //获取某分类下的所有子分类
        //$list[] = D('GoodsCategory')->findRow(array('where'=>array('id'=>1)));
        //var_dump(api('Tree/getAllChild',array($tree_data, $list)));
        //获取某分类的所有父分类
        //var_dump(api('Tree/getAllParent',array($result, 45)));

        //分类模板
        $template = "<tr>
                        <td>{id}</td>
                        <td>{top_class}{name}{icon}</td>
                        <td class='quick-edit' data-model='ServiceCategory' data-id='{id}' data-field='sort'>{sort}</td>
                        <td>{description}</td>
                        <td>{status}</td>
                        <td>{operate}</td>
                    </tr>";

        //设置初始数据
        api('Tree/init',array($tree_data,$template,array('id','name',array('icon', function($value) {return $this->_isPic($value);}),'sort','description',array('status', function($value){return get_status_title($value);}),'operate')));
        //生成树状页面
        $html = api('Tree/toFormatTree');

        return array('list'=>$html);
    }

    /**
     * @return array
     * 获取禁止某些操作的ID
     */
    private function _getProhibit() {
        $ids = M('ServiceCategory')->where(array('level'=>array('GT',2)))->field('id')->select();
        return array_column($ids, 'id');
    }

    /**
     * @return array
     * 获取允许的操作
     */
    private function _getAllow() {
        return array('edit','set_status','delete');
    }

    private function _isPic($pic) {
        return !empty($pic) ? ' <i class="fa fa-photo" aria-hidden="true"></i>' : '';
    }

    /**
     * @param string $field_name 隐藏文本框name名称
     * @param string $index_value 默认选中的值
     * @param string $index_field 默认选中字段
     * @return string
     * 获取分类树状下拉菜单
     */
    function getSelect($field_name = '', $index_value = '', $index_field = 'id', $param = array()) {
        //查询的字段
        $param['field'] = 'id,parent_id,name';
        //获取数据
        $result = D('ServiceCategory')->getList($param);
        //返回数据
        return api('Category/getSelect',array($result,$field_name,$index_value,$index_field));
    }
    /**
     * @param int $result
     * @param array $request
     * @return boolean
     * 新增、更新、修改状态、删除后执行
     */
    protected function afterAll($result = 0, $request = array()) {
        S('ServiceCategory_Cache', null);
        return true;
    }

    /**
     * @param array $request
     * @return mixed
     */
    function findRow($request = array()) {
        if(!empty($request['id'])) {
            $param['where']['id'] = $request['id'];
        } else {
            $this->setLogicInfo('参数错误！'); return false;
        }

        $row = D('ServiceCategory')->findRow($param);

        if(!$row) {
            $this->setLogicInfo('未查到此记录！'); return false;
        }
        //获取文件
        $row['icon'] = api('File/getFiles', array($row['icon']));

        return $row;
    }
}