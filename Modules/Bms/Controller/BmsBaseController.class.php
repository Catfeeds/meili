<?php
namespace Bms\Controller;
use MsC\Controller\MscBaseController;
use Bms\Model\AuthRuleModel;

/**
 * Class BaseController
 * @package Bms\Controller
 * 控制器父类
 * 其中包含 验证登录
 *          菜单处理
 *          和一些基本的增、删、改、查的方法
 *          好多方法可以集中处理 这里分开 是为了方便权限验证
 */
class BmsBaseController extends MscBaseController {

    /**
     * @var string
     * 权限验证规则
     * 模块/控制器/方法
     */
    protected static $rule          =  '';

    /**
     * @var null
     * 逻辑类对象
     */
    protected static $logicObject   = NULL;


    /**
     * 初始化执行
     * 每个控制器方法执行前 先执行该方法
     */
    protected function _initialize() {
        //执行 父类_initialize()的方法体
        parent::_initialize();
        // 获取当前登录的管理员ID
        define('AID', is_login());
        // 还没登录 跳转到登录页面
        if(!AID)
            $this->redirect('Login/login');
        // 是否是超级管理员
        define('IS_ROOT', is_administrator());
        //检查IP地址是否禁止访问
        if(!IS_ROOT && C('ADMIN_ALLOW_IP')) {
            // 检查IP地址访问
            if(!in_array(get_client_ip(), explode(',', C('ADMIN_ALLOW_IP'))))
                $this->error('403:IP禁止访问');
        }
        //验证规则
        self::$rule         =  MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        //当前控制器 对应的逻辑类对象 存在类 即初始化
        self::$logicObject  = class_exists('' . MODULE_NAME . '\\Logic\\' . CONTROLLER_NAME . 'Logic') ? D(CONTROLLER_NAME, 'Logic') : NULL;
        //左侧菜单列表
        $this->assign('menus', get_menus());
        //面包屑导航
        $this->assign('content_header', L('_CONTENT_HEADER_'));
    }

    /**
     * 频道列表页公共方法
     */
    function index() {
        //验证权限
        $this->checkRule(self::$rule);
        // 记录当前列表页的cookie
        cookie('__forward__', U('' . CONTROLLER_NAME . '/index', $_REQUEST));
        //查询结果
        $result = self::$logicObject->getList(I('request.'));
        if($result) {
            $this->assign('page', $result['page']); //分页信息
            $this->assign('list', $result['list']); //数据信息
            $this->assign('result', $result); //所有数据信息
            $this->assign('breadcrumb_nav', L('_LIST_BREADCRUMB_NAV_')); //面包屑导航
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
        //关联数据
        $this->getIndexRelation();
        $this->display('index');
    }

    /**
     * 添加公共方法
     */
    function add() {
        //权限验证
        $this->checkRule(self::$rule);
        //关联数据
        $this->getAddRelation();
        $this->assign('breadcrumb_nav', L('_ADD_BREADCRUMB_NAV_'));
        $this->display('update');
    }

    /**
     * 修改公共方法
     */
    function update() {
        //判断是添加还是修改 验证规则
        $this->checkRule(empty($_REQUEST['id']) ? MODULE_NAME . '/' . CONTROLLER_NAME . '/add' : MODULE_NAME . '/' . CONTROLLER_NAME . '/update');
        //判断是执行 修改/添加 还是 进入页面
        if(!IS_POST) {
            if ($_GET['id']) {
                $row = self::$logicObject->findRow(I('get.'));
                if ($row) {
                    $this->assign('row', $row);
                    $this->assign('breadcrumb_nav', L('_UPD_BREADCRUMB_NAV_'));
                    $this->getUpdateRelation();
                } else {
                    $this->error(self::$logicObject->getLogicInfo());
                }
            }
            $this->display('update');
        } else {
            $result = self::$logicObject->update(I('post.'));
            if($result) {
                $this->success(self::$logicObject->getLogicInfo(), cookie('__forward__'));
            } else {
                $this->error(self::$logicObject->getLogicInfo());
            }
        }
    }

    /**
     * 添加时 获取相关系数据
     * 例：添加文章时 要获取文章分类列表，添加管理员获取组列表等
     */
    protected function getAddRelation() {}
    /**
     * 修改时 获取相关系数据
     * 例：添加文章时 要获取文章分类列表，添加管理员获取组列表等
     */
    protected function getUpdateRelation() {}
    /**
     * 频道列表页相关系数据
     */
    protected function getIndexRelation() {}

    /**
     * 禁用、启用操作公共方法
     */
    function forbidResume() {
        //权限验证
        $this->checkRule(self::$rule);
        $this->setField(I('request.'));
    }

    /**
     * 针对某个字段的快速修改
     */
    function quickEdit() {
        //权限验证
        $this->checkRule(self::$rule);
        $this->setField(I('request.'));
    }

    /**
     * @param array $request
     * 修改字段
     */
    protected function setField($request = array()) {
        $result = self::$logicObject->setField($request);
        if($result) {
            $this->success(self::$logicObject->getLogicInfo());
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }

    /**
     * 删除公共方法
     */
    function delete() {
        //权限验证
        $this->checkRule(self::$rule);
        $result = self::$logicObject->remove(I('request.'));
        if($result) {
            $this->success(self::$logicObject->getLogicInfo());
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }

    /**
     * 详情
     */
    public function detail() {
        //权限验证
        $this->checkRule(self::$rule);
        $row = self::$logicObject->findRow(I('get.'));
        if($row) {
            $this->assign('row', $row);
            $this->assign('breadcrumb_nav', L('_DETAIL_BREADCRUMB_NAV_'));
            $this->display('detail');
        } else {
            $this->error(self::$logicObject->getLogicInfo());
        }
    }


    /**
     * @param string $rule 检测的规则
     * @param int $type 检验类型
     * @param string $mode 验证模式
     * @return bool
     * 权限检测
     */
    final protected function checkRule($rule, $type = AuthRuleModel::RULE_URL, $mode = 'url') {
        return true;
        if(!$this->accessControl($rule)) {
            //超级管理员允许访问任何页面
            if (!IS_ROOT) {
                static $Auth = null;
                if (!$Auth) {
                    //初始化权限检测类
                    $Auth = new \Think\Auth();
                }
                //检测权限
                if (!$Auth->check($rule, AID, $type, $mode)) {
                    $this->error('权限不足！'); exit;
                }
            }
        }
    }

    /**
     * @param string $rule 检测的规则
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问
     */
    final protected function accessControl($rule = '') {
        return true;
        //管理员允许访问任何页面
        if(IS_ROOT) {
            return true;
        }
        //允许所有人访问的方法列表
        $allow = C('ALLOW_VISIT');
        //不允许访问的方法列表（除超管外)
        $deny  = C('DENY_VISIT');
        //判断当前访问 是否在禁止访问的列表中
        if ( !empty($deny)  && in_array_case($rule,$deny) ) {
            //非超管禁止访问deny中的方法
            $this->error('权限不足！'); exit;
        }
        //判断当前访问 是否在允许访问的列表中
        if ( !empty($allow) && in_array_case($rule,$allow) ) {
            return true;
        }
        //需要继续检测
        return false;
    }
}