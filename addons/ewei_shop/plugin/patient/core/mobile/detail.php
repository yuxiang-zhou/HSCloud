<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$uniacid = $_W['uniacid'];
$id = intval($_GPC['id']);
$shop = m('common')->getSysset('shop');
$member = m('member')->getMember($openid, true);
$doctor = $this->model->getDoctor($id, $member);
$credit = $member['credit1'];
$money = $member['credit2'];
if ($_W['isajax']) {
    if ($operation == 'display') {
        if (!empty($doctor)) {
            pdo_update('ewei_shop_patient_doctor', array(
                'views' => $doctor['views'] + 1
            ), array(
                'id' => $id
            ));
        }
        //处理就诊时间
        $day = array();
        if ($doctor['type'] == 1) {
            $dtime = iunserializer($doctor['doctortime']);
            foreach ($dtime as $val) {
                $day[] = array(
                    'date' => date("Y-m-d", strtotime($val['doctortime'])),
                    'time' => str_replace(array('AM', 'PM'), array('上午', '下午'), date("A", strtotime($val['doctortime']))),
                );
            }
        } else {
            for ($i = 0; $i < 10; $i++) {
                $day[] = array(
                    'date' => date('Y-m-d', strtotime("+$i day")),
                    'time' => '全天',
                );
            }
        }
        show_json(1, array(
            'followed' => m('user')->followed($openid),
            'creditstr' => number_format(intval($credit), 0),
            'credit' => intval($credit),
            'moneystr' => number_format(intval($money), 2),
            'money' => $money,
            'day' => $day,
            'doctor' => $doctor
        ));
    } else if ($operation == 'pay' && $_W['ispost']) {
        if (empty($doctor['canbuy'])) {
            show_json(0, $doctor['buymsg']);
        }
        $needpay = false;
        if ($doctor['money'] > 0) {
            pdo_delete('ewei_shop_patient_log', array(
                'doctorid' => $id,
                'openid' => $openid,
                'status' => 0,
                'paystatus' => 0
            ));
            $needpay = true;
            $lastlog = pdo_fetch('select * from ' . tablename('ewei_shop_patient_log') . ' where doctorid=:doctorid and openid=:openid  and status=0 and paystatus=1 and uniacid=:uniacid limit 1', array(
                ':doctorid' => $id,
                ':openid' => $openid,
                ':uniacid' => $uniacid
            ));
            if (!empty($lastlog)) {
                show_json(1, array(
                    'logid' => $lastlog['id']
                ));
            }
        } else {
            pdo_delete('ewei_shop_patient_log', array(
                'doctorid' => $id,
                'openid' => $openid,
                'status' => 0
            ));
        }
        $log = array(
            'uniacid' => $uniacid,
            'openid' => $openid,
            'logno' => m('common')->createNO('patient_log', 'logno', empty($doctor['type']) ? 'EE' : 'EL'),
            'eno' => $this->model->createENO(),
            'departid' => $doctor['cate'],
            'doctorid' => $id,
            'status' => 0,
            'paystatus' => $doctor['money'] > 0 ? 0 : -1,
            'usedate' => $_GPC['date'],
            'realname' => $_GPC['realname'],
            'phone' => intval($_GPC['phone']),
            'createtime' => time()
        );

        pdo_insert('ewei_shop_patient_log', $log);
        $logid = pdo_insertid();
        if ($needpay) {
            $useweixin = true;
            if (!empty($doctor['usecredit2'])) {
                if ($money > $doctor['money']) {
                    $useweixin = false;
                }
            }
            pdo_update('ewei_shop_patient_log', array(
                'paytype' => $useweixin ? 1 : 0
            ), array(
                'id' => $logid
            ));
            if ($useweixin) {
                $set = m('common')->getSysset();
                if (!is_weixin()) {
                    show_json(0, '非微信环境!');
                }
                if (empty($set['pay']['weixin'])) {
                    show_json(0, '未开启微信支付!');
                }
                $wechat = array(
                    'success' => false
                );
                $params = array();
                $params['tid'] = $log['logno'];
                $params['user'] = $openid;
                $params['fee'] = $doctor['money'];
                $params['title'] = $set['shop']['name'] . (empty($goods['type']) ? "本院医生" : '客邀医生') . '预约单号:' . $log['logno'];
                load()->model('payment');
                $setting = uni_setting($_W['uniacid'], array(
                    'payment'
                ));
                if (is_array($setting['payment'])) {
                    $options = $setting['payment']['wechat'];
                    $options['appid'] = $_W['account']['key'];
                    $options['secret'] = $_W['account']['secret'];
                    $wechat = m('common')->wechat_build($params, $options, 2);
                    $wechat['success'] = false;
                    if (!is_error($wechat)) {
                        $wechat['success'] = true;
                    } else {
                        show_json(0, $wechat['message']);
                    }
                }
                if (!$wechat['success']) {
                    show_json(0, '微信支付参数错误!');
                }
                show_json(1, array(
                    'logid' => $logid,
                    'wechat' => $wechat
                ));
            }
        }
        show_json(1, array(
            'logid' => $logid
        ));
    } else if ($operation == 'lottery' && $_W['ispost']) {
        $logid = intval($_GPC['logid']);
        $log = pdo_fetch('select * from ' . tablename('ewei_shop_patient_log') . ' where id=:id and uniacid=:uniacid limit 1', array(
            ':id' => $logid,
            ':uniacid' => $uniacid
        ));
        if (empty($log)) {
            show_json(-1, '服务器错误!');
        }
        if ($log['status'] >= 1) {
            show_json(-1, '此记录已作废!');
        }
        if (empty($doctor['canbuy'])) {
            show_json(-1, $doctor['buymsg']);
        }
        pdo_update('ewei_shop_patient_doctor', array(
            'joins' => $doctor['joins'] + 1
        ), array(
            'id' => $id
        ));
        $upgrade = array();
        if ($goods['credit'] > 0 && empty($log['creditpay'])) {
            m('member')->setCredit($openid, 'credit1', -$goods['credit'], "医院门诊预约扣除积分 {$goods['credit']}");
            $update['creditpay'] = 1;
        }
        if ($goods['money'] > 0 && empty($log['paystatus'])) {
            if ($goods['paytype'] == 0) {
                m('member')->setCredit($openid, 'credit2', -$goods['money'], "医院门诊预约扣除余额度 {$goods['credit']}");
            }
            $update['paystatus'] = 1;
        }
        $status = 2;
        $update['eno'] = $this->model->createENO();
        $update['status'] = $status;
        pdo_update('ewei_shop_patient_log', $update, array(
            'id' => $logid
        ));
        if ($status == 2) {
            $this->model->sendMessage($logid);
        }
        show_json($status);
    }
}
include $this->template('detail');