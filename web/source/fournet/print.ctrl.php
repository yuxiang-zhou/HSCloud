<?php
/**
 * [WEIZAN System] Copyright (c) 2014 012WZ.COM
 * WEIZAN is NOT a free software, it under the license terms, visited http://www.012wz.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
uni_user_permission_check('fournet_print');
$row = pdo_fetchcolumn("SELECT `print` FROM ".tablename('uni_settings') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));
$print = iunserializer($row);
if(checksubmit('submit')) {
	$print = array(
			'name' => $_GPC['name'],
			'type' => $_GPC['type'],
			'use' => $_GPC['use'],
			'appkey' => $_GPC['appkey'],
			'dis' => $_GPC['dis'],
			'code' => $_GPC['code'],
			'num' => $_GPC['num'],
		);
		$row = array();
		$row['print'] = iserializer($print);
		pdo_update('uni_settings', $row, array('uniacid' => $_W['uniacid']));
		message('更新设置成功！', url('fournet/print'));
	}
template('fournet/print');
