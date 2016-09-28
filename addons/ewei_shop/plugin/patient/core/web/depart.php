<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if ($operation == 'display') {
    ca('patient.depart.view');
    if (!empty($_GPC['displayorder'])) {
        ca('patient.depart.edit');
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update('ewei_shop_patient_depart', array(
                'displayorder' => $displayorder
            ), array(
                'id' => $id
            ));
        }
        plog('patient.depart.edit', '批量修改科室的排序');
        message('科室排序更新成功！', $this->createPluginWebUrl('patient/depart', array(
            'op' => 'display'
        )), 'success');
    }
    $list = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_patient_depart') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('patient.depart.add');
    } else {
        ca('patient.depart.edit|patient.depart.view');
    }
    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $_W['uniacid'],
            'name' => trim($_GPC['catename']),
            'enabled' => intval($_GPC['enabled']),
            'isrecommand' => intval($_GPC['isrecommand']),
            'displayorder' => intval($_GPC['displayorder']),
            'thumb' => save_media($_GPC['thumb'])
        );
        if (!empty($id)) {
            pdo_update('ewei_shop_patient_depart', $data, array(
                'id' => $id
            ));
            plog('patient.depart.edit', "修改医院门诊科室 ID: {$id}");
        } else {
            pdo_insert('ewei_shop_patient_depart', $data);
            $id = pdo_insertid();
            plog('patient.depart.add', "添加医院门诊科室 ID: {$id}");
        }
        message('更新科室成功！', $this->createPluginWebUrl('patient/depart', array(
            'op' => 'display'
        )), 'success');
    }
    $item = pdo_fetch("select * from " . tablename('ewei_shop_patient_depart') . " where id=:id and uniacid=:uniacid limit 1", array(
        ":id" => $id,
        ":uniacid" => $_W['uniacid']
    ));
} elseif ($operation == 'delete') {
    ca('patient.depart.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id,name FROM " . tablename('ewei_shop_patient_depart') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
    if (empty($item)) {
        message('抱歉，科室不存在或是已经被删除！', $this->createPluginWebUrl('patient/depart', array(
            'op' => 'display'
        )), 'error');
    }
    pdo_delete('ewei_shop_patient_depart', array(
        'id' => $id
    ));
    plog('patient.depart.delete', "删除医院门诊科室 ID: {$id} 标题: {$item['name']} ");
    message('科室删除成功！', $this->createPluginWebUrl('patient/depart', array(
        'op' => 'display'
    )), 'success');
}
include $this->template('depart');