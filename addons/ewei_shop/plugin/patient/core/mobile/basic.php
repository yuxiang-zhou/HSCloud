<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$openid = m('user')->getOpenid();
$uniacid = $_W['uniacid'];
$set = m('common')->getSysset('shop');
if (empty($set['name'])) {
    $set['name'] = "掌上医院";
}

include $this->template('basic');