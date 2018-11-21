<?php
namespace ShopBms\Model;

/**
 * Class MemberModel
 * @package Bms\Model
 * 会员模型
 */
class MemberModel extends BmsBaseModel {

    /**
     * @var array
     * 自动验证规则
     */
    protected $_validate = array ();

    /**
     * 初始化处理
     */
    public function _initialize() {
        //自动验证规则
        $this->_validate = array(
            array('account', 'require', '账号不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
            array('account', '/^0?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[037])[0-9]{8}$/', '账号格式不正确！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
            array('account', 'checkUnique', '账号已经存在！', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH, array('account')),
            array('password', '6,18', '密码长度在6-18个字符！', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
            array('pay_pass', '/^\d{6}$/', '支付密码为6位数字！', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
            array('nickname', 'require', '请输入昵称！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
            array('nickname', '1,15', '昵称长度不能超过15个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        );
    }

    /**
     * @var array
     * 自动完成规则
     */
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_INSERT, 'function'),
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
                        ->field('m.id,m.account,m.nickname,m.freeze_balance,m.integral,m.update_time,m.balance,m.profit,m.login,m.status,m.last_login_time')
                        ->where($param['special_where'])
                        ->join(array(
                            //'LEFT JOIN ' . C('DB_PREFIX') . 'keeper kp ON kp.id = m.kp_id',
                        ))
                        ->select();
        //返回记录 根据ID顺序排序
        return array('list'=>sort_by_array($param['ids_for_sort'], $list), 'page'=>$Page->show());
    }
}