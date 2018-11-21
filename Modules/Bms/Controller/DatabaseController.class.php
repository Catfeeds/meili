<?php
namespace Bms\Controller;
use Think\Db;

/**
 * Class DatabaseController
 * @package Bms\Controller
 * 数据库操作控制器
 */
class DatabaseController extends BmsBaseController {

    /**
     * 备份页 获取所有表信息
     */
    function export(){
        //权限验证
        $this->checkRule(self::$rule);
        //获取数据库操作对象
        $Db    = Db::getInstance();
        //获取所有表的信息
        $list  = $Db->query('SHOW TABLE STATUS');
        //转化数组键值为小写
        $list  = array_map('array_change_key_case', $list);

        $this->assign('list',$list);
        $this->assign('breadcrumb_nav', L('_EXP_BREADCRUMB_NAV_'));
        $this->display('export');
    }

    /**
     * @param array $tables 数据表名
     * 数据备份
     * 在linux下注意备份文件写入到的文件夹的权限  是否有写入权限
     */
    function doExport($tables = array()) {
        //权限验证
        $this->checkRule(self::$rule);
        //表名参数 是否存在
        if(empty($tables)) {
            $this->error('请指定要备份的表！');
        }
        //备份数据库 可执行文件路径 绝对路径
        $exe_path    = C('BACKUP_DB_EXEC_PATH');
        //锁文件路径
        $exe_lock    = realpath('./Data') . DIRECTORY_SEPARATOR . basename($exe_path) . '.lock'; //执行文件锁
        //备份文件名称 以时间命名
        $name        = date('Ymd-His', time());
        $backup_lock = realpath('./Data/data') . DIRECTORY_SEPARATOR . $name . '.lock'; //备份文件锁
        //检查是否有正在执行的任务
        if(is_file($exe_lock) || is_file($backup_lock)) {
            //存在锁文件  提示
            $this->error('检测到有备份任务正在执行，请稍后再试！');
        } else {
            //创建锁文件
            file_put_contents($exe_lock, NOW_TIME); file_put_contents($backup_lock, NOW_TIME);
        }
        //转化成字符串
        $tables   = implode(' ', $tables);
        //读取可执行文件内容
        $exe_cont = \Think\Storage::read($exe_path);
        //判断是否存在{{tables}}字符
        if(false !== strpos($exe_cont, '{{tables}}')) {
            //替换{{tables}}为要备份的表名称 {{name}}问备份文件名称
            $rep_cont = str_replace(array('{{tables}}','{{name}}'), array($tables, $name), $exe_cont);
            //修改可执行文件内容
            \Think\Storage::put($exe_path, $rep_cont);
            //执行备份数据库的可执行.bat文件  （在linux下注意相关文件权限，是否读写）
            exec($exe_path, $out, $status);
            //恢复可执行文件内容
            \Think\Storage::put($exe_path, $exe_cont);
            //删除锁文件
            array_map("unlink", array($exe_lock, $backup_lock));
            //status为0时 执行成功
            if ($status == 0) {
                $this->success('数据备份成功！');
            } else {
                $this->error('数据备份失败！');
            }
        } else {
            //删除锁文件
            array_map("unlink", array($exe_lock, $backup_lock));
            $this->error('数据备份失败！执行文件有误！');
        }
    }

    /**
     * 数据库还原列表
     */
    function import() {
        //权限验证
        $this->checkRule(self::$rule);
        //列出备份文件列表
        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator('./Data/data',  $flag);
        //循环所有文件
        foreach ($glob as $name => $file) {
            //文件名格式匹配
            if(preg_match('/^\d{8,8}-\d{6,6}+\.sql(?:\.gz)?$/', $name)) {
                //读取与指定格式相符的数据 到数组
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s');
                //日期
                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                //具体时间
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                //获取文件大小
                $info['size']     = $file->getSize();
                //文件扩展名
                $extension        = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                //是否压缩
                $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
                //文件名对应的时间戳
                $info['time']     = strtotime("{$date} {$time}");

                $list["{$date} {$time}"] = $info;
            }
        }
        $this->assign('list', $list);
        $this->assign('breadcrumb_nav', L('_IMP_BREADCRUMB_NAV_'));
        $this->display('import');
    }

    /**
     * 删除备份文件
     * @param  Integer $time 备份时间 文件名时间戳
     */
    function del($time = 0) {
        //权限验证
        $this->checkRule(self::$rule);
        //文件时间参数是否存在
        if(empty($time)) {
            $this->error('参数错误！');
        }
        //检查该文件是否有正在执行的任务文件
        if(is_file(realpath('./Data/data') . DIRECTORY_SEPARATOR . date('Ymd-His', $time) . '.lock')) {
            //存在锁文件  提示
            $this->error('检测到该文件任务正在执行，请稍后再试！');
        }
        //文件名称
        $name  = date('Ymd-His', $time) . '.sql*';
        //文件路径
        $path  = realpath('./Data/data') . DIRECTORY_SEPARATOR . $name;
        //执行删除 array_map执行 unlink
        array_map("unlink", glob($path));
        //判断成功 失败
        if(count(glob($path))) {
            $this->error('备份文件删除失败，请检查权限！');
        } else {
            $this->success('备份文件删除成功！');
        }
    }

    /**
     * @param int $time
     * 还原数据库
     */
    function doImport($time = 0) {
        //权限验证
        $this->checkRule(self::$rule);
        //时间参数是否存在
        if(empty($time)) {
            $this->error('参数错误！');
        }
        //还原数据库 可执行文件路径
        $exe_path    = C('RESTORE_DB_EXEC_PATH');
        //锁文件路径
        $exe_lock    = realpath('./Data') . DIRECTORY_SEPARATOR . basename($exe_path) . '.lock'; //执行文件锁
        //备份文件名称 文件名称转换
        $name        = date('Ymd-His', $time);
        $backup_lock = realpath('./Data/data') . DIRECTORY_SEPARATOR . $name . '.lock'; //还原备份文件锁
        //检查是否有正在执行的任务
        if(is_file($exe_lock) || is_file($backup_lock)) {
            //存在锁 文件  提示
            $this->error('检测到该文件有任务正在执行，请稍后再试！');
        } else {
            //创建锁文件
            file_put_contents($exe_lock, NOW_TIME); file_put_contents($backup_lock, NOW_TIME);
        }
        //读取可执行文件内容
        $exe_cont = \Think\Storage::read($exe_path);
        //判断是否存在{{file}}字符
        if(false !== strpos($exe_cont, '{{file}}')) {
            //替换{{file}}为当前还原文件名称
            $rep_cont = str_replace('{{file}}', $name, $exe_cont);
            //修改可执行文件内容
            \Think\Storage::put($exe_path, $rep_cont);
            //执行还原数据库的可执行.bat文件  （在linux下注意相关文件权限，是否读写）
            exec($exe_path, $out, $status);
            //恢复可执行文件内容
            \Think\Storage::put($exe_path, $exe_cont);
            //删除锁文件
            array_map("unlink", array($exe_lock, $backup_lock));
            //status为0时 执行成功
            if ($status == 0) {
                $this->success('数据还原成功！');
            } else {
                $this->error('数据还原失败！');
            }
        } else {
            //删除锁文件
            array_map("unlink", array($exe_lock, $backup_lock));
            $this->error('数据还原失败！执行文件有误！');
        }
    }

    /**
     * @param null $tables 表名  数组/字符
     * 优化表
     */
    function optimize($tables = null) {
        //权限验证
        $this->checkRule(self::$rule);
        //scandir 返回指定路径中的文件和路径  否则返回false  //TODO 判断有没有锁文件

        $this->_tableOR($tables, 'optimize');
    }

    /**
     * @param null $tables
     * 修复表
     */
    public function repair($tables = null) {
        //权限验证
        $this->checkRule(self::$rule);
        //scandir 返回指定路径中的文件和路径  否则返回false //TODO 判断有没有锁文件

        $this->_tableOR($tables, 'repair');
    }

    /**
     * @param $tables 表名称
     * @param string $exe 执行 优化还是修复
     * 表 优化、修复方法
     */
    private function _tableOR($tables, $exe) {
        //要执行的sql语句
        $sql  = '';
        //显示的文字  “优化 修复”
        $text = '';
        switch($exe){
            case 'optimize' : $sql = "OPTIMIZE TABLE `{$tables}`";  $text = '优化'; break;
            case 'repair'   : $sql = "REPAIR TABLE `{$tables}`";    $text = '修复'; break;
        }
        if($tables) {
            //获取数据库操作对象
            $Db = Db::getInstance();
            //批量优化
            if(is_array($tables)) {
                //转化成字符串
                $tables = implode('`,`', $tables);
                //执行修复语句
                $list   = $Db->query($sql);
                //判断成功 失败
                if($list) {
                    $this->success("数据表" . $text . "完成！");
                } else {
                    $this->error("数据表" . $text . "出错请重试！");
                }
            } else {
                //单个表修复
                $list = $Db->query($sql);
                //判断成功 失败
                if($list) {
                    $this->success("数据表'{$tables}'" . $text . "完成！");
                } else {
                    $this->error("数据表'{$tables}'" . $text . "出错请重试！");
                }
            }
        } else {
            //未指定的表
            $this->error("请指定要" . $text . "的表！");
        }
    }
}
