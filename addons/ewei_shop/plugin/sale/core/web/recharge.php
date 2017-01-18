<?php

global $_W, $_GPC;
check_shop_auth('http://120.26.212.219/api.php', $this->pluginname);
ca('sale.recharge.view');
$set = $this->getSet();
$recharges = iunserializer($set['recharges']);
if (checksubmit('submit')) {
	ca('sale.recharge.save');
	$recharges = array();
	$data = is_array($_GPC['enough']) ? $_GPC['enough'] : array();
	foreach ($data as $key => $value) {
		$enough = trim($value);
		if (!empty($enough)) {
			$recharges[] = array('enough' => trim($_GPC['enough'][$key]), 'give' => trim($_GPC['give'][$key]));
		}
	}
	$set['recharges'] = iserializer($recharges);
	$this->updateSet($set);
	plog('sale.recharge.save', '修改充值优惠设置');
	message('充值优惠设置成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('recharge');