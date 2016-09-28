<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid, true);
$shop = m('common')->getSysset('shop');
$uniacid = $_W['uniacid'];

if ($_W['isajax']) {
    if ($operation == 'display') {

        $total = pdo_fetchcolumn('select COUNT(*) from ' . tablename('ewei_shop_patient_report') . ' where realname=:realname and mobile=:mobile and uniacid=:uniacid limit 1', array(
            ':realname' => $_GPC['name'],
            ':mobile' => $_GPC['num'],
            ':uniacid' => $uniacid
        ));

        $report = pdo_fetch('select * from ' . tablename('ewei_shop_patient_report') . ' where realname=:realname and mobile=:mobile and uniacid=:uniacid limit 1', array(
            ':realname' => $_GPC['name'],
            ':mobile' => $_GPC['num'],
            ':uniacid' => $uniacid
        ));

        if (empty($total)) {
            $msg = array(
                'status' => '0',
                'content' => '无数据，请核实后再试！',
            );
        } else {
            $msg = array(
                'id' => $report['id'],
                'status' => '1',
                'content' => '获取成功请等待，正在转入结果页面！',
            );
        }
        echo json_encode($msg);
        exit;
    } else if ($operation == 'detail') {
        $id  = intval($_GPC['id']);
        $report = pdo_fetch('select * from ' . tablename('ewei_shop_patient_report') . ' where id=:id and uniacid=:uniacid limit 1', array(
            ':id' => $id,
            ':uniacid' => $uniacid
        ));
        if (empty($report)) {
            $msg = array(
                'status' => '0',
                'content' => '无报告记录！',
            );
        } else {
            if (!empty($id)) {
                pdo_update('ewei_shop_patient_report', array(
                    'status' => '1',
                    'views' => $report['views'] + 1
                ), array(
                    'id' => $id
                ));
            }
            $msg = array(
                'status' => '1',
                'content' => $report['detail'],
            );
        }
        echo json_encode($msg);
        exit;
    }
}

if ($operation == 'display') {
    include $this->template('report');
} elseif ($operation == 'detail') {
    include $this->template('report_detail');
}