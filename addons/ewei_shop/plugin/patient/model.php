<?php


defined('IN_IA') or exit('Access Denied');

if (!class_exists('PatientModel')) {
    class PatientModel extends PluginModel
    {
        public function getDoctor($id, $member)
        {
            global $_W;
            $credit = $member['credit1'];
            $money = $member['credit2'];
            if (empty($id)) {
                return array(
                    'canbuy' => false,
                    'buymsg' => '已删除'
                );
            }
            $doctor = pdo_fetch('select * from ' . tablename('ewei_shop_patient_doctor') . ' where id=:id and uniacid=:uniacid limit 1', array(
                ':id' => $id,
                ':uniacid' => $_W['uniacid']
            ));
            if (empty($doctor)) {
                return array(
                    'canbuy' => false,
                    'buymsg' => '已删除'
                );
            }
            $doctor = set_medias($doctor, 'thumb');
            if ($doctor['credit'] > 0 & $doctor['money'] > 0) {
                $doctor['acttype'] = 0;
            } else if ($doctor['credit'] > 0) {
                $doctor['acttype'] = 1;
            } else if ($doctor['money'] > 0) {
                $doctor['acttype'] = 2;
            }
            $doctor['canbuy'] = !empty($doctor['status']) && empty($doctor['deleted']);

            if (empty($doctor['canbuy'])) {
                $doctor['buymsg'] = "已删除";
            } else {
                if ($doctor['total'] > 0) {
                    $logcount = pdo_fetchcolumn('select count(*)  from ' . tablename('ewei_shop_patient_log') . "  where doctorid=:doctorid and status>=2  and uniacid=:uniacid  ", array(
                        ':doctorid' => $id,
                        ':uniacid' => $_W['uniacid']
                    ));
                    if ($logcount >= $doctor['total']) {
                        $doctor['canbuy'] = false;
                        $doctor['buymsg'] = '已预约完';
                    }
                }
                if ($doctor['canbuy']) {
                    if ($doctor['totalday'] > 0) {
                        $logcount = pdo_fetchcolumn('select count(*)  from ' . tablename('ewei_shop_patient_log') . "  where doctorid=:doctorid and status>=2 and  date_format(from_UNIXTIME(`createtime`),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and uniacid=:uniacid  ", array(
                            ':doctorid' => $id,
                            ':uniacid' => $_W['uniacid']
                        ));
                        if ($logcount >= $doctor['totalday']) {
                            $doctor['canbuy'] = false;
                            $doctor['buymsg'] = '今日已预约完';
                        }
                    }
                }
                if ($doctor['canbuy']) {
                    if ($doctor['chanceday'] > 0) {
                        $logcount = pdo_fetchcolumn('select count(*)  from ' . tablename('ewei_shop_patient_log') . "  where doctorid=:doctorid and openid=:openid and status>0 and  date_format(from_UNIXTIME(`createtime`),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') and uniacid=:uniacid  ", array(
                            ':doctorid' => $id,
                            ':uniacid' => $_W['uniacid'],
                            ':openid' => $member['openid']
                        ));
                        if ($logcount >= $doctor['chanceday']) {
                            $doctor['canbuy'] = false;
                            $doctor['buymsg'] = '今日已预约';
                        }
                    }
                }
                if ($doctor['canbuy']) {
                    if ($doctor['chance'] > 0) {
                        $logcount = pdo_fetchcolumn('select count(*)  from ' . tablename('ewei_shop_patient_log') . '  where doctorid=:doctorid and openid=:openid and status>0 and  uniacid=:uniacid  ', array(
                            ':doctorid' => $id,
                            ':uniacid' => $_W['uniacid'],
                            ':openid' => $member['openid']
                        ));
                        if ($logcount >= $doctor['chance']) {
                            $doctor['canbuy'] = false;
                            $doctor['buymsg'] = '已预约';
                        }
                    }
                }
                if ($doctor['canbuy']) {
                    if ($doctor['usermaxbuy'] > 0) {
                        $logcount = pdo_fetchcolumn('select ifnull(sum(total),0)  from ' . tablename('ewei_shop_patient_log') . '  where goodsid=:goodsid and openid=:openid  and uniacid=:uniacid ', array(
                            ':goodsid' => $id,
                            ':uniacid' => $_W['uniacid'],
                            ':openid' => $member['openid']
                        ));
                        if ($logcount >= $doctor['chance']) {
                            $doctor['canbuy'] = false;
                            $doctor['buymsg'] = '已参加';
                        }
                    }
                }
                if ($doctor['canbuy']) {
                    if ($credit < $doctor['credit'] && $doctor['credit'] > 0) {
                        $doctor['canbuy'] = false;
                        $doctor['buymsg'] = "积分不足";
                    }
                }
            }

            return $doctor;
        }

        public function createENO()
        {
            global $_W;
            $ecount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_patient_log') . ' where uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid']
            ));
            if ($ecount < 99999999) {
                $ecount = 8;
            } else {
                $ecount = strlen($ecount . "");
            }
            $eno = rand(pow(10, $ecount), pow(10, $ecount + 1) - 1);
            while (1) {
                $c = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_patient_log') . ' where uniacid=:uniacid and eno=:eno limit 1', array(
                    ':uniacid' => $_W['uniacid'],
                    ':eno' => $eno
                ));
                if ($c <= 0) {
                    break;
                }
                $eno = rand(pow(10, $ecount), pow(10, $ecount + 1) - 1);
            }
            return $eno;
        }

        public function sendMessage($id = 0)
        {
            global $_W;
            if (empty($id)) {
                return;
            }
            $log = pdo_fetch('select log.*,d.name from ' . tablename('ewei_shop_patient_log') . "log" . " left join " . tablename('ewei_shop_patient_depart') . " d on d.id=log.departid" . '  where 1 and log.id=:id and log.uniacid=:uniacid limit 1', array(
                ':id' => $id,
                ':uniacid' => $_W['uniacid']
            ));
            if (empty($log)) {
                return;
            }
            $member = m('member')->getMember($log['openid'], true);
            if (empty($member)) {
                return;
            }
            $credit = intval($member['credit1']);
            $doctor = $this->getDoctor($log['doctorid'], $member);
            if (empty($doctor['id'])) {
                return;
            }
            $type = $doctor['type'];
            $credits = "";
            if ($doctor['credit'] > 0 & $doctor['money'] > 0) {
                $credits = $doctor['credit'] . "积分+" . $doctor['money'] . "元";
            } else if ($doctor['credit'] > 0) {
                $credits = $doctor['credit'] . "积分";
            } else if ($doctor['money'] > 0) {
                $credits = $doctor['money'] . "元";
            }
            $shop = m('common')->getSysset('shop');
            $set = $this->getSet();
            $tm = $set['tm'];
            //$detailurl = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=ewei_shop&do=plugin&p=patient&method=log&op=detail&&id=' . $id;
            $detailurl = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=ewei_shop&do=plugin&p=patient';
            if (strexists($detailurl, '/addons/ewei_shop/')) {
                $detailurl = str_replace("/addons/ewei_shop/", '/', $detailurl);
            }
            if ($log['status'] == 2) {
                if (!empty($type)) {
                    $remark = "\r\n 【" . $shop['name'] . "】提示：请及时前往就诊！到院请出示以上信息！";
                    $msg = array(
                        'first' => array(
                            'value' => "您好，您已成功预约我院门诊。",
                            "color" => "#4a5077"
                        ),
                        'keyword1' => array(
                            'title' => '就诊科室',
                            'value' => $log['name'],
                            "color" => "#4a5077"
                        ),
                        'keyword2' => array(
                            'title' => '就诊医生',
                            'value' => $doctor['title'] . "/专家号",
                            "color" => "#4a5077"
                        ),
                        'keyword3' => array(
                            'title' => '就诊人',
                            'value' => $log['realname'],
                            "color" => "#4a5077"
                        ),
                        'keyword4' => array(
                            'title' => '就诊时间',
                            'value' => $log['usedate'],
                            "color" => "#4a5077"
                        ),
                        'keyword5' => array(
                            'title' => '就诊凭证',
                            'value' => $log['eno'],
                            "color" => "#4a5077"
                        ),
                        'remark' => array(
                            'value' => $remark,
                            "color" => "#4a5077"
                        )
                    );

                    if (!empty($tm['award'])) {
                        m('message')->sendTplNotice($log['openid'], $tm['award'], $msg);
                    } else {
                        m('message')->sendCustomNotice($log['openid'], $msg);
                    }
                } else {
                    $remark = "\r\n 【" . $shop['name'] . "】提示：请及时前往就诊！到院请出示以上信息！";
                    $msg = array(
                        'first' => array(
                            'value' => "您好，您已成功预约我院门诊。",
                            "color" => "#4a5077"
                        ),
                        'keyword1' => array(
                            'title' => '就诊科室',
                            'value' => $log['name'],
                            "color" => "#4a5077"
                        ),
                        'keyword2' => array(
                            'title' => '就诊医生',
                            'value' => $doctor['title'] . "/普通号",
                            "color" => "#4a5077"
                        ),
                        'keyword3' => array(
                            'title' => '就诊人',
                            'value' => $log['realname'],
                            "color" => "#4a5077"
                        ),
                        'keyword4' => array(
                            'title' => '就诊时间',
                            'value' => $log['usedate'],
                            "color" => "#4a5077"
                        ),
                        'keyword5' => array(
                            'title' => '就诊凭证',
                            'value' => $log['eno'],
                            "color" => "#4a5077"
                        ),
                        'remark' => array(
                            'value' => $remark,
                            "color" => "#4a5077"
                        )
                    );

                    if (!empty($tm['award'])) {
                        m('message')->sendTplNotice($log['openid'], $tm['award'], $msg);
                    } else {
                        m('message')->sendCustomNotice($log['openid'], $msg);
                    }

                }

                //通知院方管理人员
                $remark = "\r\n 【" . $shop['name'] . "】提醒管理人员！有患者预约挂号，请进入后台确认信息！";;

                $noticeopenids = explode(",", $doctor['noticeopenid']);
                if (empty($doctor['noticeopenid'])) {
                    $noticeopenids = explode(",", $set['tm']['openids']);
                }
                if (!empty($noticeopenids)) {
                    foreach ($noticeopenids as $noticeopenid) {
                        if (!empty($tm['new'])) {
                            $msg = array(
                                'first' => array(
                                    'value' => "预约挂号成功~",
                                    "color" => "#4a5077"
                                ),
                                'keyword1' => array(
                                    'title' => '就诊医生',
                                    'value' => $doctor['title'],
                                    "color" => "#4a5077"
                                ),
                                'keyword2' => array(
                                    'title' => '费用',
                                    'value' => $doctor['money'],
                                    "color" => "#4a5077"
                                ),
                                'keyword3' => array(
                                    'title' => '就诊时间',
                                    'value' => $log['usedate'],
                                    "color" => "#4a5077"
                                ),
                                'keyword4' => array(
                                    'title' => '就诊凭证',
                                    'value' => $log['eno'],
                                    "color" => "#4a5077"
                                ),
                                'remark' => array(
                                    'value' => $remark,
                                    "color" => "#4a5077"
                                )
                            );
                            m('message')->sendTplNotice($noticeopenid, $tm['new'], $msg);
                        } else {
                            $msg = array(
                                'first' => array(
                                    'value' => "您好，有患者预约挂号。",
                                    "color" => "#4a5077"
                                ),
                                'keyword1' => array(
                                    'title' => '就诊科室',
                                    'value' => $log['name'],
                                    "color" => "#4a5077"
                                ),
                                'keyword2' => array(
                                    'title' => '就诊医生',
                                    'value' => $doctor['title'] . "/普通号",
                                    "color" => "#4a5077"
                                ),
                                'keyword3' => array(
                                    'title' => '就诊人',
                                    'value' => $log['realname'],
                                    "color" => "#4a5077"
                                ),
                                'keyword4' => array(
                                    'title' => '就诊时间',
                                    'value' => $log['usedate'],
                                    "color" => "#4a5077"
                                ),
                                'keyword5' => array(
                                    'title' => '就诊凭证',
                                    'value' => $log['eno'],
                                    "color" => "#4a5077"
                                ),
                                'remark' => array(
                                    'value' => $remark,
                                    "color" => "#4a5077"
                                )
                            );
                            m('message')->sendCustomNotice($noticeopenid, $msg);
                        }
                    }
                }
            } else if ($log['status'] == 3) {
                $remark = "\r\n 【" . $shop['name'] . "】提示您：你预约号已成功使用！";
                $msg = array(
                    'first' => array(
                        'value' => "你的就诊凭证已使用~",
                        "color" => "#4a5077"
                    ),
                    'keyword1' => array(
                        'title' => '就诊科室',
                        'value' => $log['name'],
                        "color" => "#4a5077"
                    ),
                    'keyword2' => array(
                        'title' => '就诊医生',
                        'value' => $doctor['title'],
                        "color" => "#4a5077"
                    ),
                    'keyword3' => array(
                        'title' => '就诊人',
                        'value' => $log['realname'],
                        "color" => "#4a5077"
                    ),
                    'keyword4' => array(
                        'title' => '就诊时间',
                        'value' => $log['usedate'],
                        "color" => "#4a5077"
                    ),
                    'keyword5' => array(
                        'title' => '就诊凭证',
                        'value' => $log['eno'],
                        "color" => "#4a5077"
                    ),
                    'remark' => array(
                        'value' => $remark,
                        "color" => "#4a5077"
                    )
                );

                if (!empty($tm['award'])) {
                    m('message')->sendTplNotice($log['openid'], $tm['award'], $msg);
                } else {
                    m('message')->sendCustomNotice($log['openid'], $msg);
                }
            }
        }

        function perms()
        {
            return array(
                'patient' => array(
                    'text' => $this->getName(),
                    'isplugin' => true,
                    'child' => array(
                        'cover' => array(
                            'text' => '入口设置'
                        ),
                        'depart' => array(
                            'text' => '科室',
                            'view' => '浏览',
                            'add' => '添加-log',
                            'edit' => '修改-log',
                            'delete' => '删除-log'
                        ),
                        'doctor' => array(
                            'text' => '医生',
                            'view' => '浏览',
                            'add' => '添加-log',
                            'edit' => '修改-log',
                            'delete' => '删除-log'
                        ),
                        'adv' => array(
                            'text' => '幻灯片',
                            'view' => '浏览',
                            'add' => '添加-log',
                            'edit' => '修改-log',
                            'delete' => '删除-log'
                        ),
                        'log' => array(
                            'text' => '预约记录',
                            'view' => '浏览预约记录',
                            'exchange' => '确认预约-log'
                        ),
                        'notice' => array(
                            'text' => '通知设置',
                            'view' => '查看',
                            'save' => '修改-log'
                        ),
                        'set' => array(
                            'text' => '基础设置',
                            'view' => '查看',
                            'save' => '修改-log'
                        )
                    )
                )
            );
        }
    }
}