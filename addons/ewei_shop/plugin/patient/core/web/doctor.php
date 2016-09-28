<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if ($operation == 'addtype') {
    $kw = $_GPC['kw'];
    include $this->template('doctor/act_type', array('op' => 'addtype'));
    die;
} else if ($operation == 'display') {
    ca('patient.doctor.view');
    if (!empty($_GPC['displayorder'])) {
        ca('patient.doctor.edit');
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update('ewei_shop_patient_doctor', array(
                'displayorder' => $displayorder
            ), array(
                'id' => $id
            ));
        }
        plog('patient.doctor.edit', '批量修改医院门诊医生排序');
        message('医生排序更新成功！', $this->createPluginWebUrl('patient/doctor', array(
            'op' => 'display'
        )), 'success');
    }
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = ' uniacid = :uniacid AND deleted = :deleted';
    $params = array(
        ':uniacid' => $_W['uniacid'],
        ':deleted' => '0'
    );
    if (!empty($_GPC['keyword'])) {
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' AND title LIKE :title';
        $params[':title'] = '%' . trim($_GPC['keyword']) . '%';
    }
    if ($_GPC['type'] != '') {
        $condition .= ' AND type = :type';
        $params[':type'] = intval($_GPC['type']);
    }
    if ($_GPC['status'] != '') {
        $condition .= ' AND status = :status';
        $params[':status'] = intval($_GPC['status']);
    }
    if ($_GPC['cate'] != '') {
        $condition .= ' AND cate = :cate';
        $params[':cate'] = intval($_GPC['cate']);
    }
    $sql = 'SELECT * FROM ' . tablename('ewei_shop_patient_doctor') . " where  1 and {$condition} ORDER BY displayorder DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_patient_doctor') . " where 1 and {$condition}", $params);
    $pager = pagination($total, $pindex, $psize);
    $category = pdo_fetchall("select id,name,thumb from " . tablename('ewei_shop_patient_depart') . ' where uniacid=:uniacid order by displayorder desc', array(
        ':uniacid' => $_W['uniacid']
    ), 'id');
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('patient.doctor.add');
    } else {
        ca('patient.doctor.view|patient.doctor.edit');
    }
    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $_W['uniacid'],
            'displayorder' => intval($_GPC['displayorder']),
            'title' => trim($_GPC['title']),
            'technicaltitle' => trim($_GPC['technicaltitle']),
            'skill' => trim($_GPC['skill']),
            'cate' => intval($_GPC['cate']),
            'thumb' => save_media($_GPC['thumb']),
            'price' => $_GPC['price'],
            'type' => intval($_GPC['type']),
            'chance' => intval($_GPC['chance']),
            'chanceday' => intval($_GPC['chanceday']),
            'total' => intval($_GPC['total']),
            'totalday' => intval($_GPC['totalday']),
            'credit' => intval($_GPC['credit']),
            'money' => $_GPC['money'],
            'doctortime' => '',
            'status' => intval($_GPC['status']),
            'usecredit2' => intval($_GPC['usecredit2'])
        );
        $doctortime = $_GPC['date'];
        if (!empty($doctortime)) {
            $dtime = array();
            foreach ($doctortime as $key => $val) {
                $dtime[] = array('doctortime' => $val);
            }
        }
        $data['doctortime'] = iserializer($dtime);
        if (!empty($id)) {
            $data['updatetime'] = time();
            pdo_update('ewei_shop_patient_doctor', $data, array(
                'id' => $id,
                'uniacid' => $_W['uniacid']
            ));
            plog('patient.doctor.edit', "编辑医院门诊医生 ID: {$id} <br/>医生名称: {$data['title']}");
        } else {
            $data['createtime'] = time();
            pdo_insert('ewei_shop_patient_doctor', $data);
            $id = pdo_insertid();
            plog('patient.doctor.add', "添加医院门诊医生 ID: {$id}  <br/>医生名称: {$data['title']}");
        }
        message('更新医生成功！', $this->createPluginWebUrl('patient/doctor', array(
            'op' => 'post',
            'id' => $id
        )), 'success');
    }
    $item = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_patient_doctor') . " WHERE id =:id and uniacid=:uniacid limit 1", array(
        ':uniacid' => $_W['uniacid'],
        ':id' => $id
    ));
    $category = pdo_fetchall("select id,name,thumb from " . tablename('ewei_shop_patient_depart') . ' where uniacid=:uniacid order by displayorder desc', array(
        ':uniacid' => $_W['uniacid']
    ));
    $data = iunserializer($item['doctortime']);
} elseif ($operation == 'delete') {
    ca('patient.doctor.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id,title FROM " . tablename('ewei_shop_patient_doctor') . " WHERE id =:id and uniacid=:uniacid limit 1", array(
        ':id' => $id,
        ':uniacid' => $_W['uniacid']
    ));
    if (empty($item)) {
        message('抱歉，医生不存在或是已经被删除！', $this->createPluginWebUrl('patient/doctor', array(
            'op' => 'display'
        )), 'error');
    }
    pdo_update('ewei_shop_patient_doctor', array(
        'deleted' => 1
    ), array(
        'id' => $id,
        'uniacid' => $_W['uniacid']
    ));
    plog('patient.doctor.delete', "删除医院门诊医生 ID: {$id}  <br/>医生名称: {$item['title']} ");
    message('医生删除成功！', $this->createPluginWebUrl('verify/saler', array(
        'op' => 'display'
    )), 'success');
} elseif ($operation == 'setdoctorproperty') {
    ca('patient.doctor.edit');
    $id = intval($_GPC['id']);
    $type = $_GPC['type'];
    $data = intval($_GPC['data']);
    if (in_array($type, array(
        'status'
    ))) {
        $data = ($data == 1 ? '0' : '1');
        pdo_update('ewei_shop_patient_doctor', array(
            $type => $data
        ), array(
            "id" => $id,
            "uniacid" => $_W['uniacid']
        ));
        if ($type == 'status') {
            $typestr = "显示隐藏";
        }
        plog('patient.doctor.edit', "修改医院门诊医生{$typestr}状态   ID: {$id}");
        die(json_encode(array(
            'result' => 1,
            'data' => $data
        )));
    }
    die(json_encode(array(
        'result' => 0
    )));
}

include $this->template('doctor');