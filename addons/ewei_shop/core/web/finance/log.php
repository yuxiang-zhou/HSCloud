<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

global $_W, $_GPC;
check_shop_auth('http://120.26.212.219/api.php');
$op = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$groups = m('member')->getGroups();
$levels = m('member')->getLevels();
$uniacid = $_W['uniacid'];

if ($op == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$type = intval($_GPC['type']);

	if ($type == 1) {
		ca('finance.withdraw.view');
	} elseif ($type == 0) {
		ca('finance.recharge.view');
	} elseif ($type == 2) {
    ca('finance.refund.view');
  }

  // member search query params
  $condition = '';
  if($type != 2) {
    $condition .= ' AND log.uniacid=:uniacid AND log.type=:type AND log.money<>0';
    $params = array(':uniacid' => $_W['uniacid'], ':type' => $type);
  }

	if (!empty($_GPC['realname'])) {
		$_GPC['realname'] = trim($_GPC['realname']);
		$params[':realname'] = "%{$_GPC['realname']}%";
    if($type != 2) {
      $condition .= ' AND (m.realname like :realname or m.nickname like :realname or m.mobile like :realname)';
    } else {
      $condition .= ' AND (realname like :realname or nickname like :realname or mobile like :realname)';
    }

	}

	if (!empty($_GPC['level'])) {

    if($type != 2) {
      $condition .= ' AND m.level=' . intval($_GPC['level']);
    } else {
      $condition .= ' AND level=' . intval($_GPC['level']);
    }
	}
	if (!empty($_GPC['groupid'])) {
    if($type != 2) {
      $condition .= ' AND m.groupid=' . intval($_GPC['groupid']);
    } else {
      $condition .= ' AND groupid=' . intval($_GPC['groupid']);
    }
	}

  // recharge params
	if (!empty($_GPC['rechargetype'])) {
		$_GPC['rechargetype'] = trim($_GPC['rechargetype']);
		$condition = " AND log.rechargetype=:rechargetype";
		if ($_GPC['rechargetype'] == 'system1') {
			$condition = " AND log.rechargetype='system' and log.money<0";
		}
		$params[':rechargetype'] = trim($_GPC['rechargetype']);
	}

	if ($_GPC['status'] != '') {
    if($type != 2) {
      $condition .= ' AND log.status=' . intval($_GPC['status']);
    }
	}

  // log params

  if (!empty($_GPC['logno'])) {
    $_GPC['logno'] = trim($_GPC['logno']);
    $params[':logno'] = "%{$_GPC['logno']}%";
    if($type != 2) {
      $condition .= ' AND log.logno like :logno';
    } else {
      $condition .= ' AND ordersn like :logno';
    }

  }
  if (empty($starttime) || empty($endtime)) {
    $starttime = strtotime('-1 month');
    $endtime = time();
  }
  if (!empty($_GPC['time'])) {
    $starttime = strtotime($_GPC['time']['start']);
    $endtime = strtotime($_GPC['time']['end']);
    if ($_GPC['searchtime'] == '1') {
      $params[':starttime'] = $starttime;
      $params[':endtime'] = $endtime;
      if($type != 2) {
        $condition .= " AND log.createtime >= :starttime AND log.createtime <= :endtime ";
      } else {
        $condition .= " AND createtime >= :starttime AND createtime <= :endtime ";
      }
    }
  }

  // load data with params

  if ($type != 2) {
  	$sql = "select log.id,m.id as mid, m.realname,m.avatar,m.weixin,log.logno,log.type,log.status,log.rechargetype,m.nickname,m.mobile,g.groupname,log.money,log.createtime,l.levelname from " . tablename('ewei_shop_member_log') . " log " . " left join " . tablename('ewei_shop_member') . " m on m.openid=log.openid" . " left join " . tablename('ewei_shop_member_group') . " g on m.groupid=g.id" . " left join " . tablename('ewei_shop_member_level') . " l on m.level =l.id" . " where 1 {$condition} ORDER BY log.createtime DESC ";
  	if (empty($_GPC['export'])) {
  		$sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
  	}
  	$list = pdo_fetchall($sql, $params);
  	$total = pdo_fetchcolumn("select count(*) from " . tablename('ewei_shop_member_log') . " log " . " left join " . tablename('ewei_shop_member') . " m on m.openid=log.openid and m.uniacid= log.uniacid" . " left join " . tablename('ewei_shop_member_group') . " g on m.groupid=g.id" . " left join " . tablename('ewei_shop_member_level') . " l on m.level =l.id" . " where 1 {$condition} ", $params);
  } else {
    $sql_columns = "FROM (SELECT m.id as mid, o.openid, og.total, og.orderid, m.level, m.groupid, m.realname,m.avatar,m.weixin,m.nickname,m.mobile,grp.groupname,o.ordersn, og.goodssn, og.price, gr.single_refund_price, gr.num_refund, gr.period, gr.id AS refund_id, SUM(grh.price) AS money, MAX(grh.time_refund) AS createtime, COUNT(grh.refund_id) AS num_refunded ";

    $sql_tables  = " FROM hs_ewei_shop_order AS o ";
    $sql_tables .= " INNER JOIN hs_ewei_shop_order_goods AS og ";
    $sql_tables .= " ON o.id=og.orderid ";
    $sql_tables .= " INNER JOIN hs_ewei_shop_member AS m ";
    $sql_tables .= " ON m.openid=o.openid ";
    $sql_tables .= " LEFT JOIN hs_ewei_shop_member_group AS grp ";
    $sql_tables .= " ON m.groupid=grp.id ";
    $sql_tables .= " LEFT JOIN hs_ewei_shop_member_level AS ml ";
    $sql_tables .= " ON m.level=ml.id ";
    $sql_tables .= " INNER JOIN hs_ewei_shop_goods_cashback AS gr ";
    $sql_tables .= " ON gr.goodssn=og.goodssn ";
    $sql_tables .= " LEFT JOIN hs_ewei_shop_goods_cashback_hist AS grh ";
    $sql_tables .= " ON grh.refund_id=gr.id AND grh.ordersn=o.ordersn AND grh.goodssn=og.goodssn ";
    $sql_tables .= " WHERE o.status=3 AND gr.activate > 0 AND o.createtime >= gr.start_date AND o.createtime <= gr.end_date ";
    $sql_tables .= " GROUP BY o.ordersn,og.goodssn,gr.id) AS rt WHERE 1 ";
    $sql_tables .= $condition;

    $total = pdo_fetchcolumn("SELECT count(*) " . $sql_columns . $sql_tables, $params);

    if (empty($_GPC['export'])) {
      $sql_tables .= " LIMIT " . ($pindex - 1) * $psize . ',' . $psize . ' ';
    }

    $list = pdo_fetchall('SELECT * ' . $sql_columns . $sql_tables, $params);


    // get detailed refund history
    foreach ($list as &$row) {
      $refund_params = array(':goodssn' => $row['goodssn'], ':ordersn' => $row['ordersn'], ':refund_id' => $row['refund_id']);
      $row['hist'] = pdo_fetchall('SELECT * FROM hs_ewei_shop_goods_cashback_hist WHERE goodssn=:goodssn AND ordersn=:ordersn AND refund_id=:refund_id', $refund_params);
      $row['unique_id'] = 'ID'.$row['ordersn'].$row['goodssn'].$row['refund_id'];
    }
    unset($row);

    // refund management list
    $refundlist = pdo_fetchall("SELECT * FROM hs_ewei_shop_goods_cashback");
  }
  if ($_GPC['export'] == 1) {
    if ($_GPC['type'] == 1) {
      ca('finance.withdraw.export');
      plog('finance.withdraw.export', '导出提现记录');
    } else {
      ca('finance.recharge.export');
      if ($_GPC['type'] == 2) {
        plog('finance.recharge.export', '导出返现记录');
      } else {
        plog('finance.recharge.export', '导出充值记录');
      }

    }
    foreach ($list as &$row) {
      $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
      $row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
      $row['levelname'] = empty($row['levelname']) ? '普通会员' : $row['levelname'];
      if ($row['status'] == 0) {
        if ($row['type'] == 0) {
          $row['status'] = "未充值";
        } else {
          $row['status'] = "申请中";
        }
      } else if ($row['status'] == 1) {
        if ($row['type'] == 0) {
          $row['status'] = "充值成功";
        } else {
          $row['status'] = "完成";
        }
      } else if ($row['status'] == -1) {
        if ($row['type'] == 0) {
          $row['status'] = "";
        } else {
          $row['status'] = "失败";
        }
      }
      if ($row['rechargetype'] == 'system') {
        $row['rechargetype'] = "后台";
      } else if ($row['rechargetype'] == 'wechat') {
        $row['rechargetype'] = "微信";
      } else if ($row['rechargetype'] == 'alipay') {
        $row['rechargetype'] = "支付宝";
      }
    }
    unset($row);
    $columns = array(
      array('title' => '昵称', 'field' => 'nickname', 'width' => 12),
      array('title' => '姓名', 'field' => 'realname', 'width' => 12),
      array('title' => '手机号', 'field' => 'mobile', 'width' => 12),
      array('title' => '会员等级', 'field' => 'levelname', 'width' => 12),
      array('title' => '会员分组', 'field' => 'groupname', 'width' => 12),
      array('title' => (empty($type) ? "充值金额" : ($type==1 ? "提现金额" : "已返现总金额")), 'field' => 'money', 'width' => 12),
      array('title' => (empty($type) ? "充值时间" : ($type==1 ? "提现申请时间" : "上次返现时间")), 'field' => 'createtime', 'width' => 12),
    );
    if (empty($_GPC['type'])) {
      $columns[] = array('title' => "充值方式", 'field' => 'rechargetype', 'width' => 12);
    }
    m('excel')->export($list, array("title" => (empty($type) ? "会员充值数据-" : "会员提现记录") . date('Y-m-d-H-i', time()), "columns" => $columns));
  }
	$pager = pagination($total, $pindex, $psize);
} elseif ($op == 'pay') {
	$paytype = $_GPC['paytype'];
  if ($paytype == 'cashback') {

    $ordersn = $_GPC['ordersn'];
    $goodssn = $_GPC['goodssn'];
    $lastdate = date('Y-m-d H:i',$_GPC['createtime']);

		// cashback validation
    if ($_GPC['num_refunded'] > 0) {
      if ($_GPC['num_refunded'] >= $_GPC['num_refund']) {
        message('返现已于 '.$lastdate.' 完成!', '', 'error');
      }
			$lastFYtime = new DateTime('NOW');
			$lastFYtime->setTimestamp($_GPC['createtime']);
			$lastmonthFYtime = new DateTime('-'.strval($_GPC['period']).' month');
			$interval = $lastmonthFYtime->diff($lastFYtime);
			if ($interval->y == 0 && $interval->m == 0)  {
				message('本月已于 '.$lastdate.' 返现!', '', 'error');
			}
    }

    // variable definition
    $openid = $_GPC['openid'];
    $money = floatval($_GPC['single_refund_price']) * floatval($row['total']);

    $member = m('member')->getMember($openid);
    $set = m('common')->getSysset('shop');
    $logno = m('common')->createNO('member_log', 'logno', 'RB');
  	$log = array(
			'uniacid' => $_W['uniacid'],
			'logno' => $logno,
			'title' => $set['name'] . "销售返现",
			'openid' => $openid, 'type' => 0,
			'createtime' => time(), 'status' => 0,
			'type' => 2,
			'status' => 1,
			'money' => $money
		);

    // execute
    $result = m('finance')->pay($openid, 1, $money * 100.0, $logno, $set['name'].'销售返现');

    // error handling
    if (is_error($result)) {
      message('返现失败: ' . $result['message'], '', 'error');
    }

    // notify user
  	pdo_insert('ewei_shop_member_log', $log);
  	$logid = pdo_insertid();
    m('notice')->sendMemberLogMessage($logid);
    plog('finance.cashback', "销售返现 ID: {$log['id']} 金额: {$log['money']} <br/>会员信息:  ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");

    // log cashback
    pdo_insert('ewei_shop_goods_cashback_hist', array(
			'ordersn' => $ordersn,
			'goodssn' => $goodssn,
			'refund_id' => $_GPC["refund_id"],
			'price' => $money,
			'time_refund' => time(),
			'logno' => $logno
		));

    message('返现成功!', referer(), 'success');

  } else {
    $id = intval($_GPC['id']);
    //增加
  	$set = m('common')->getSysset(array('trade'));
  	$rate = empty($set['trade']['withdrawrate'])?100:$set['trade']['withdrawrate'];

  	$set = m('common')->getSysset('shop');
  	$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $uniacid));
  	if (empty($log)) {
  		message('未找到记录!', '', 'error');
  	}
  	$member = m('member')->getMember($log['openid']);
  	if ($paytype == 'manual') {
  		ca('finance.withdraw.withdraw');
  		pdo_update('ewei_shop_member_log', array('status' => 1), array('id' => $id, 'uniacid' => $uniacid));
  		m('notice')->sendMemberLogMessage($logid);
  		plog('finance.withdraw.withdraw', "余额提现 方式: 手动 ID: {$log['id']} <br/>会员信息: ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
  		message('手动提现完成!', referer(), 'success');
  	} else if ($paytype == 'wechat') {
  		ca('finance.withdraw.withdraw');
  		//$result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $log['logno'], $set['name'] . '余额提现');
  		//修改
  		$result = m('finance')->pay($log['openid'], 1, $log['money'] * $rate, $log['logno'], $set['name'] . '余额提现');

  		if (is_error($result)) {
  			message('微信钱包提现失败: ' . $result['message'], '', 'error');
  		}
  		pdo_update('ewei_shop_member_log', array('status' => 1), array('id' => $id, 'uniacid' => $uniacid));
  		m('notice')->sendMemberLogMessage($log['id']);
  		plog('finance.withdraw.withdraw', "余额提现 ID: {$log['id']} 方式: 微信 金额: {$log['money']} <br/>会员信息:  ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
  		message('微信钱包提现成功!', referer(), 'success');
  	} else if ($paytype == 'refuse') {
  		ca('finance.withdraw.withdraw');
  		pdo_update('ewei_shop_member_log', array('status' => -1), array('id' => $id, 'uniacid' => $uniacid));
  		m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(0, $set['name'] . '余额提现退回'));
  		m('notice')->sendMemberLogMessage($log['id']);
  		plog('finance.withdraw.withdraw', "拒绝余额度提现 ID: {$log['id']} 金额: {$log['money']} <br/>会员信息:  ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
  		message('操作成功!', referer(), 'success');
  	} else if ($paytype == 'refund') {
  		ca('finance.recharge.refund');
  		if (!empty($log['type'])) {
  			message('充值退款失败: 非充值记录!', '', 'error');
  		}
  		if ($log['rechargetype'] == 'system') {
  			message('充值退款失败: 后台充值无法退款!', '', 'error');
  		}
  		$current_credit = m('member')->getCredit($log['openid'], 'credit2');
  		if ($log['money'] > $current_credit) {
  			message('充值退款失败: 会员账户余额不足，无法进行退款!', '', 'error');
  		}
  		$out_refund_no = 'RR' . substr($log['logno'], 2);
  		if ($log['rechargetype'] == 'wechat') {
  			$result = m('finance')->refund($log['openid'], $log['logno'], $out_refund_no, $log['money'] * 100, $log['money'] * 100);
  		} else {
  			$result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $out_refund_no, $set['name'] . '充值退款');
  		}
  		if (is_error($result)) {
  			message('充值退款失败: ' . $result['message'], '', 'error');
  		}
  		pdo_update('ewei_shop_member_log', array('status' => 3), array('id' => $id, 'uniacid' => $uniacid));
  		m('member')->setCredit($log['openid'], 'credit2', -$log['money'], array(0, $set['name'] . '充值退款'));
  		m('notice')->sendMemberLogMessage($log['id']);
  		plog('finance.withdraw.withdraw', "充值退款 ID: {$log['id']} 金额: {$log['money']} <br/>会员信息:  ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
  		message('充值退款成功!', referer(), 'success');
  	} else {
  		message('未找到提现方式!', '', 'error');
  	}
  }

} elseif ($op=='refund') {
	$action = $_GPC['action'];
	if ($action == "delete") {
		pdo_delete('ewei_shop_goods_cashback', array('id' => $_GPC['id']));
		message('成功删除!', referer(), 'success');
	} elseif ($action == "upsert") {

    $data = array(
      "goodssn" => $_GPC['update_goodssn'],
      "period" => intval($_GPC['update_period']),
      "single_refund_price" => floatval($_GPC['update_single_refund_price']),
      "num_refund" => intval($_GPC['update_num_refund']),
      "created" => time(),
      "activate" => intval($_GPC['update_activate']),
      "start_date" => strtotime($_GPC['time']['start']),
      "end_date" => strtotime($_GPC['time']['end'])
    );

    if(intval($_GPC['refund_id']) != 0) {
      pdo_update('ewei_shop_goods_cashback', $data, array("id" => intval($_GPC['refund_id'])));
      message('成功更新!', referer(), 'success');
    } else {
      pdo_insert('ewei_shop_goods_cashback', $data, $replace = FALSE);
      message('成功添加!', referer(), 'success');
    }
	} else {
		message('Undefined Action', '', 'error');
	}
}
load()->func('tpl');
include $this->template('web/finance/log');
