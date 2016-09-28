<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

ca('patient.notice.view');
$set = $this->getSet();
if (checksubmit('submit')) {
    ca('patient.notice.save');
    $set['tm'] = is_array($_GPC['tm']) ? $_GPC['tm'] : array();
    if (is_array($_GPC['openids'])) {
        $set['tm']['openids'] = implode(",", $_GPC['openids']);
    }
    $this->updateSet($set);
    plog('patient.notice.save', '修改医院门诊通知设置');
    message('设置保存成功!', referer(), 'success');
}
$salers = array();
if (isset($set['tm']['openids'])) {
    if (!empty($set['tm']['openids'])) {
        $openids     = array();
        $strsopenids = explode(",", $set['tm']['openids']);
        foreach ($strsopenids as $openid) {
            $openids[] = "'" . $openid . "'";
        }
        $salers = pdo_fetchall("select id,nickname,avatar,openid from " . tablename('ewei_shop_member') . ' where openid in (' . implode(",", $openids) . ") and uniacid={$_W['uniacid']}");
    }
}
include $this->template('notice');