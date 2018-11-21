<?php
namespace FrontC\Model;
use Common\Base\BaseModel;

/**
 * Class FrontBaseModel
 * @package FrontC\Model
 * 前端集体数据层父类
 *  Api
 *  Home
 *  Wap
 */
class FrontBaseModel extends BaseModel {

    /**
     * @return string
     * 分页样式
     */
    protected function getPageTheme() {
        return '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% %HEADER%';
    }
}