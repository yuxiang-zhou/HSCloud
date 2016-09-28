<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

ca('creditshop.set.view');
$set = $this->getSet();

include $this->template('set');