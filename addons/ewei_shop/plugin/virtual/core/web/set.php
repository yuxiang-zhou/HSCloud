<?php

global $_W, $_GPC;
check_shop_auth('http://120.26.212.219/api.php', $this->pluginname);
ca('virtual.set.view');
$set = $this->getSet();
if (checksubmit('submit')) {
	ca('virtual.set.save');
	$data = is_array($_GPC['setdata']) ? array_merge($set, $_GPC['setdata']) : array();
	$data['tm'] = is_array($_GPC['tm']) ? $_GPC['tm'] : array();
	$this->updateSet($data);
	plog('virtual.set.save', '修改基本设置');
	message('设置保存成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('set');