<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$uniacid = $_W['uniacid'];

$sql = 'SELECT * FROM ' . tablename('ewei_shop_patient_depart') . '  where 1 ORDER BY displayorder DESC';
$list = pdo_fetchall($sql, $params);
$list = set_medias($list, 'thumb');

include $this->template('depart');