<?php


class PatientProcessor extends PluginProcessor
{
    public function __construct()
    {
        parent::__construct('patient');
    }

    public function respond($obj = null)
    {
        global $_W;
        $message = $obj->message;
        $openid = $obj->message['from'];
        $content = $obj->message['content'];
        $msgtype = strtolower($message['msgtype']);
        $event = strtolower($message['event']);
        if ($msgtype == 'text' || $event == 'click') {
            $saler = pdo_fetch('select * from ' . tablename('ewei_shop_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':openid' => $openid
            ));
            if (empty($saler)) {
                return $this->responseEmpty();
            }
            if (!$obj->inContext) {
                $obj->beginContext();
                return $obj->respText('请输入预约码:');
            } else if ($obj->inContext && is_numeric($content)) {
                $log = pdo_fetch('select * from ' . tablename('ewei_shop_patient_log') . ' where eno=:eno and uniacid=:uniacid  limit 1', array(
                    ':eno' => $content,
                    ':uniacid' => $_W['uniacid']
                ));
                if (empty($log)) {
                    return $obj->respText('未找到要预约码,请重新输入!');
                }
                $logid = $log['id'];
                if (empty($log)) {
                    return $obj->respText('未找到要预约码,请重新输入!');
                }
                if (empty($log['status'])) {
                    return $obj->respText('无效预约记录!');
                }
                if ($log['status'] >= 3) {
                    return $obj->respText('此记录已被确认到院了!');
                }
                $member = m('member')->getMember($log['openid'], true);
                $doctor = $this->model->getDoctor($log['doctorid'], $member);
                if (empty($doctor['id'])) {
                    return $obj->respText('医生记录不存在!');
                }
                if (empty($doctor['isverify'])) {
                    $obj->endContext();
                    return $obj->respText('此医生不支持线下兑换!');
                }
                if (!empty($doctor['type'])) {
                    if ($log['status'] <= 1) {
                        return $obj->respText('未参加，不能预约!');
                    }
                }
                if ($doctor['money'] > 0 && empty($log['paystatus'])) {
                    return $obj->respText('未支付，无法进行预约!');
                }
                if ($doctor['dispatch'] > 0 && empty($log['dispatchstatus'])) {
                    return $obj->respText('未支付运费，无法进行预约!');
                }
                $stores = explode(",", $doctor['storeids']);
                if (!empty($storeids)) {
                    if (!empty($saler['storeid'])) {
                        if (!in_array($saler['storeid'], $storeids)) {
                            return $obj->respText('您无此医院的确认权限!');
                        }
                    }
                }
                $time = time();
                pdo_update('ewei_shop_patient_log', array(
                    'status' => 3,
                    'usetime' => $time,
                    'verifyopenid' => $openid
                ), array(
                    'id' => $log['id']
                ));
                $this->model->sendMessage($logid);
                $obj->endContext();
                return $obj->respText('到院成功!');
            }
        }
    }

    private function responseEmpty()
    {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }
}