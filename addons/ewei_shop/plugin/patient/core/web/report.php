<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if ($operation == 'display') {
    ca('patient.report.view');
    $list = pdo_fetchall("SELECT * FROM " . tablename('ewei_shop_patient_report') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('patient.report.add');
    } else {
        ca('patient.report.edit|patient.report.view');
    }
    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $_W['uniacid'],
            'realname' => trim($_GPC['realname']),
            'mobile' => trim($_GPC['mobile']),
            'detail' => m('common')->html_images($_GPC['detail']),
        );
        if (!empty($id)) {
            pdo_update('ewei_shop_patient_report', $data, array(
                'id' => $id
            ));
            plog('patient.report.edit', "修改医院门诊报告 ID: {$id}");
        } else {
            pdo_insert('ewei_shop_patient_report', $data);
            $id = pdo_insertid();
            plog('patient.report.add', "添加医院门诊报告 ID: {$id}");
        }
        message('更新报告成功！', $this->createPluginWebUrl('patient/report', array(
            'op' => 'display'
        )), 'success');
    }
    $item = pdo_fetch("select * from " . tablename('ewei_shop_patient_report') . " where id=:id and uniacid=:uniacid limit 1", array(
        ":id" => $id,
        ":uniacid" => $_W['uniacid']
    ));
} elseif ($operation == 'delete') {
    ca('patient.report.delete');
    $id   = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id,realname FROM " . tablename('ewei_shop_patient_report') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
    if (empty($item)) {
        message('抱歉，报告不存在或是已经被删除！', $this->createPluginWebUrl('patient/report', array(
            'op' => 'display'
        )), 'error');
    }
    pdo_delete('ewei_shop_patient_report', array(
        'id' => $id
    ));
    plog('patient.report.delete', "删除医院门诊报告 ID: {$id} 患者姓名: {$item['realname']} ");
    message('报告删除成功！', $this->createPluginWebUrl('patient/report', array(
        'op' => 'display'
    )), 'success');
}

include $this->template('report');