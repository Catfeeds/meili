<?php
namespace Common\Model;
use Common\Base\BaseModel;

/**
 * Class FileModel
 * @package Common\Model
 * 文件数据模型
 */
class FileModel extends BaseModel {

    /**
     * @param array $param
     * @return array
     * 列表
     */
    public function getList($param = array()) {
        $Page = null;
        //存在page_size 是否分页
        if(!empty($param['page_size'])) {
            $total = $this->alias($param['alias'])->where($param['where'])->count();
            $Page  = $this->getPage($total, $param['page_size'], $_REQUEST);
        }
        //查询对象
        $model = $this->alias($param['alias'])->field($param['field'])->where($param['where'])->join($param['join']);
        //是否分页
        !($Page == null) ? $model = $model->limit($Page->firstRow,$Page->listRows) : '';

        $list  = $model->select();

        return array('list'=>$list,'page'=>!($Page == null) ? $Page->show() : '');
    }

}