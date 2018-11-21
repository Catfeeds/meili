<?php
namespace Common\Api;

/**
 * Class CategoryApi
 * @package Common\Api
 * 后台分类各个操作的处理接口
 */
class CategoryApi {

    /**
     * @param array $list       分类数据
     * @param string $model     模型名称
     * @param array $prohibit   禁止某些操作的ID
     * @param array $only_allow  只允许的操作
     * @return array
     * 生成分类的操作html
     * $prohibit设置部分操作禁止的ID数组
     * $only_allow 和$prohibit共同使用  $prohibit中的ID记录只显示$only_allow中的操作方法
     */
    function handleOperate($list = array(), $model = '', $prohibit = array(), $only_allow = array('add_child','edit','set_status','delete')) {
        //空数组返回空
        if(empty($list)) { return array(); }
        //'.MODULE_NAME.'
        foreach($list as $key => $value) {
            //添加子分类操作的html
            $add_child  = "<a href='".U('/'.$model.'/add/id/'.$value['id'].'')."' title='添加子分类' class=''>
                            <span class='label label-warning label-warning-hover'>+子分类</span></a>&nbsp";
            //编辑分类的HTML
            $edit       =  "<a href='".U('/'.$model.'/update/parent_id/'.$value['parent_id'].'/id/'.$value['id'].'')."' title='编辑' class=''>
                        <span class='label label-success label-success-hover'>编辑</span></a>&nbsp;";
            //判断当前状态
            if($value['status'] == 0 || $value['status'] == 1) {
                //启用html
                $set_status = "<a href='".U('/'.$model.'/forbidResume/model/'.$model.'/ids/'.$value['id'].'/field/status/value/'.abs(1-$value['status']).'')."' title=".show_status_name($value['status'])." class='ajax-get'>
                                <span class='label ".show_status_label($value['status'])."'>".show_status_name($value['status'])."</span></a>&nbsp;";
            }
            //删除操作的html
            $delete      = "<a href='".U('/'.$model.'/delete/model/'.$model.'/ids/'.$value['id'].'')."' title='删除' class='confirm ajax-get'>
                            <span class='label label-important label-important-hover'>删除</span></a>";

            $html        = '';
            //判断是否存在 禁止的ID  并当前ID是否在禁止的数组中  并存在只允许的操作
            if($prohibit && in_array($value['id'], $prohibit) && $only_allow) {
                foreach($only_allow as $rep) {
                    $html .= $$rep;
                }
            } else {
                $html = $add_child . $edit . $set_status . $delete;
            }

            $list[$key]['operate'] = $html;
        }

        return $list;
    }

    /**
     * @param array $list   分类数据
     * @param string $field_name 隐藏文本框name名称
     * @param string $index_value  默认选中的值
     * @param string $index_field  默认选中的字段索引
     * @return string
     * 获取树状下拉菜单
     */
    function getSelect($list = array(), $field_name = '', $index_value = '', $index_field = 'id') {
        //判空
        if(empty($list)) return '';
        //将数据转换成树状结构
        $tree_data  = list_to_tree($list);
        //下拉模板
        $template   = "<li data-value='{id}' data-title='{top_class}{name}'><a href='javascript:void(0)'>{top_class}{name}</a></li>";
        //设置初始数据
        api('Tree/init', array($tree_data,$template,array('id','name')));
        //生成树状下拉菜单
        $html       = api('Tree/toSelectFormatTree', array($index_value, $index_field));
        //html结构
        $select     = "<div class='btn-group'>
                            <button type='button' class='btn checked' data-default='--选择分类--'></button>
                            <button class='btn dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>
                            <ul class='dropdown-menu' style='height:300px;overflow: scroll'>
                            <li data-value='0' data-title='--取消选择--'><a href='javascript:void(0)'>--取消选择--</a></li>" . $html . "</ul>
                        </div>
                        <input type='hidden' name='" . $field_name . "' value='" . $index_value . "' class='{target-form}'>";

        return $select;
    }
}