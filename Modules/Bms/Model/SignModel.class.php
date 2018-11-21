<?php
namespace Bms\Model;

/**
 * Class SignModel
 * @package Bms\Model
 * 签到 数据层
 */
class SignModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array (
        array('unique_code', 'require', '行为标识必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('unique_code', '/^[a-zA-Z]\w{0,39}$/', '行为标识不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('unique_code', '1,20', '行为标识长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('unique_code', 'checkUnique', '行为标识已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('unique_code')),
        array('name', 'require', '行为名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,20', '标题长度不能超过20个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('remark', 'require', '行为描述不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('remark', '1,120', '行为描述不能超过120个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    /**
     * @param array $param
     * @return array
     * 基本列表
     */
    public function getList($param = array()) {
        //数据总数
        $total  = $this->alias($param['alias'])->where($param['where'])->count();
        //创建分页对象
        $Page   = $this->getPage($total, C('LIST_ROWS'), $_REQUEST);
        //生成ID查询条件
        $param  = $this->specialSearch($param, $Page, $param['alias']);
        //获取数据
        $list   = $this->alias($param['alias'])
            ->field('sign.id,sign.m_id,sign.sign_in_time,sign.sign_in_ip,m.account,m.nickname')
            ->where($param['special_where'])
            ->join(array(
                'LEFT JOIN ' . C('DB_PREFIX') . 'member m ON m.id = sign.m_id',
            ))
            ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}