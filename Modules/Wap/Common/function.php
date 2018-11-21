<?php
/**
 * WAP端公用方法
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
    $member = session('member');
    if (empty($member)) {
        return 0;
    } else {
        return session('member_sign') == data_auth_sign($member) ? $member['m_id'] : 0;
    }
}