<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$openid = m('user')->getOpenid();
$uniacid = $_W['uniacid'];
$set = m('common')->getSysset('shop');
if (empty($set['name'])) {
    $set['name'] = "掌上医院";
}

$advs = pdo_fetchall("select id,advname,link,thumb from " . tablename('ewei_shop_patient_adv') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(
    ':uniacid' => $uniacid
));
$advs = set_medias($advs, 'thumb');
include $this->template('index');