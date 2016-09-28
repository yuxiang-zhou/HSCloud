<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$type = $_GPC['type'];
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    ca('patient.log.view');
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    if ($type == '') {
        $condition = ' and log.uniacid=:uniacid and log.status>0';
    } else {
        $condition = ' and log.uniacid=:uniacid and d.type=:type and log.status>0';
    }

    $params = array(
        ':uniacid' => $_W['uniacid'],
        ':type' => $type
    );
    if (!empty($_GPC['keyword'])) {
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' and ( log.logno like :keyword or log.eno like :keyword or d.title like :keyword ) ';
        $params[':keyword'] = "%{$_GPC['keyword']}%";
    }
    if ($_GPC['status'] != '') {
        $condition .= " and log.status=" . intval($_GPC['status']);
    }
    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= ' and ( m.realname like :realname or m.nickname like :realname or m.mobile like :realname or log.realname like :realname or log.phone like :realname  ) ';
        $params[':realname'] = "%{$_GPC['realname']}%";
    }
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime = time();
    }
    $searchtime = $_GPC['searchtime'];
    if (!empty($_GPC['searchtime'])) {
        $starttime = strtotime($_GPC['time']['start']);
        $endtime = strtotime($_GPC['time']['end']);
        if (!empty($searchtime)) {
            $condition .= " AND log.createtime >= :starttime AND log.createtime <= :endtime ";
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
        }
    }
    $list = pdo_fetchall("select log.*, m.nickname,m.avatar,m.mobile,d.title,d.thumb,d.thumb,d.credit,d.money,d.type as doctortype from " . tablename('ewei_shop_patient_log') . " log " . " left join " . tablename('ewei_shop_member') . " m on m.openid = log.openid and m.uniacid=:uniacid" . " left join " . tablename('ewei_shop_patient_doctor') . " d on d.id = log.doctorid" . " where 1 {$condition} ORDER BY log.createtime desc limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
    $total = pdo_fetchcolumn("select count(log.id) from" . tablename('ewei_shop_patient_log') . " log " . " left join " . tablename('ewei_shop_member') . " m on m.openid = log.openid and m.uniacid=:uniacid" . " left join " . tablename('ewei_shop_patient_doctor') . " d on d.id = log.doctorid" . " where 1 {$condition}", $params);
    foreach ($list as &$row) {
        $row['address'] = array();
        if (!empty($row['addressid'])) {
            $row['address'] = pdo_fetch('select realname,mobile,address,province,city,area from ' . tablename('ewei_shop_member_address') . ' where id=:id and uniacid=:uniacid limit 1', array(
                ':id' => $row['addressid'],
                ':uniacid' => $_W['uniacid']
            ));
        }
        $row['address']['logid'] = $row['id'];
        $canexchange = true;
        if ($row['status'] == 2) {
            if (empty($row['paystatus'])) {
                $canexchange = false;
            }
        } else {
            $canexchange = false;
        }
        $row['canexchange'] = $canexchange;
    }
    unset($row);
    $pager = pagination($total, $pindex, $psize);
} else if ($operation == 'detail') {
    $id = intval($_GPC['id']);
    $log = pdo_fetch('select * from ' . tablename('ewei_shop_patient_log') . ' where id=:id and uniacid=:uniacid limit 1', array(
        ':id' => $id,
        ':uniacid' => $_W['uniacid']
    ));
    if (empty($log)) {
        message('预约记录不存在!', referer(), 'error');
    }
    $type = $log['type'];
    ca('patient.log.view');
    $member = m('member')->getMember($log['openid'], true);
    $doctor = $this->model->getDoctor($log['doctorid'], $member);
    if (empty($doctor['id'])) {
        message('医生记录不存在!', referer(), 'error');
    }
    $canexchange = true;
    if ($log['status'] == 2) {
        if (empty($log['paystatus'])) {
            $canexchange = false;
        }
    } else {
        $canexchange = false;
    }
    $log['canexchange'] = $canexchange;
    $address = false;
    $address['logid'] = $id;
} else if ($operation == 'exchange') {
    ca('patient.log.exchange');
    $id = intval($_GPC['id']);
    $log = pdo_fetch('select * from ' . tablename('ewei_shop_patient_log') . ' where id=:id and uniacid=:uniacid limit 1', array(
        ':id' => $id,
        ':uniacid' => $_W['uniacid']
    ));
    if (empty($log)) {
        message('预约记录不存在!', referer(), 'error');
    }
    if (empty($log['status'])) {
        message('无效预约记录!', referer(), 'error');
    }
    if ($log['status'] >= 3) {
        message('此预约已确认过了!', referer(), 'error');
    }
    $member = m('member')->getMember($log['openid'], true);
    $doctor = $this->model->getDoctor($log['doctorid'], $member);
    if (empty($doctor['id'])) {
        message('医生记录不存在!', referer(), 'error');
    }
    if (!empty($doctor['type'])) {
        if ($log['status'] <= 1) {
            message('未预约，不能确认!', referer(), 'error');
        }
    }
    if ($doctor['money'] > 0 && empty($log['paystatus'])) {
        message('未支付，无法进行预约!', referer(), 'error');
    }
    pdo_update('ewei_shop_patient_log', array(
        'status' => 3,
        'usetime' => time(),
    ), array(
        'id' => $id
    ));
    $this->model->sendMessage($id);
    plog('patient.log.exchange', "医院门诊预约 预约记录ID: {$id}");
    message('确认成功!', $this->createPluginWebUrl('patient/log', array(
        'type' => $doctor['type']
    )), 'success');
}
include $this->template('log');