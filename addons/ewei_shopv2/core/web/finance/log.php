<?php
//加密方式：Ioncube 7.X/8.X/9.X加密，代码还原率98+%以上(变量数值会有问题需手动修复)。
//VIP会员:zh6691 您好,破解:ioncube加密,本次扣金币:15个,金币余额:115个,感谢您的支持.
//此程序由【找源码】http://Www.ZhaoYuanMa.Com (VIP会员功能）在线逆向还原，QQ：7530782
?>
<?php
if (!(defined('IN_IA')))
{
	exit('Access Denied');
}
class Log_EweiShopV2Page extends WebPage
{
	protected function main($type = 0)
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and log.uniacid=:uniacid and m.uniacid=:uniacid and log.type=:type and log.money<>0';
		$params = array(':uniacid' => $_W['uniacid'], ':type' => $type);
		if (!(empty($_GPC['keyword'])))
		{
			$_GPC['keyword'] = trim($_GPC['keyword']);
			if ($_GPC['searchfield'] == 'logno')
			{
				$condition .= ' and log.logno like :keyword';
			}
			else if ($_GPC['searchfield'] == 'member')
			{
				$condition .= ' and (m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword)';
			}
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		if (empty($starttime) || empty($endtime))
		{
			$starttime = strtotime('-1 month');
			$endtime = time();
		}
		if (!(empty($_GPC['time']['start'])) && !(empty($_GPC['time']['end'])))
		{
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND log.createtime >= :starttime AND log.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		if (!(empty($_GPC['level'])))
		{
			$condition .= ' and m.level=' . intval($_GPC['level']);
		}
		if (!(empty($_GPC['groupid'])))
		{
			$condition .= ' and m.groupid=' . intval($_GPC['groupid']);
		}
		if (!(empty($_GPC['rechargetype'])))
		{
			$_GPC['rechargetype'] = trim($_GPC['rechargetype']);
			if ($_GPC['rechargetype'] == 'system1')
			{
				$condition .= ' AND log.rechargetype=\'system\' and log.money<0';
			}
			else
			{
				$condition .= ' AND log.rechargetype=:rechargetype';
				$params[':rechargetype'] = $_GPC['rechargetype'];
			}
		}
		if ($_GPC['status'] != '')
		{
			$condition .= ' and log.status=' . intval($_GPC['status']);
		}
		$sql = 'select log.id,m.id as mid, m.realname,m.avatar,m.weixin,log.logno,log.type,log.status,log.rechargetype,m.nickname,m.mobile,g.groupname,log.money,log.createtime,l.levelname,log.realmoney,log.deductionmoney,log.charge,log.remark,log.alipay,log.bankname,log.bankcard,log.realname as applyrealname,log.applytype from ' . tablename('ewei_shop_member_log') . ' log ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=log.openid' . ' left join ' . tablename('ewei_shop_member_group') . ' g on m.groupid=g.id' . ' left join ' . tablename('ewei_shop_member_level') . ' l on m.level =l.id' . ' where 1 ' . $condition . ' ORDER BY log.createtime DESC ';
		if (empty($_GPC['export']))
		{
			$sql .= 'LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		$list = pdo_fetchall($sql, $params);
		$apply_type = array(0 => '微信钱包', 2 => '支付宝', 3 => '银行卡');
		if (!(empty($list)))
		{
			foreach ($list as $key => $value )
			{
				$list[$key]['typestr'] = $apply_type[$value['applytype']];
				if ($value['deductionmoney'] == 0)
				{
					$list[$key]['realmoney'] = $value['money'];
				}
			}
		}
		if ($_GPC['export'] == 1)
		{
			if ($_GPC['type'] == 1)
			{
				plog('finance.log.withdraw.export', '导出提现记录');
			}
			else
			{
				plog('finance.log.recharge.export', '导出充值记录');
			}
			foreach ($list as &$row )
			{
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['groupname'] = ((empty($row['groupname']) ? '无分组' : $row['groupname']));
				$row['levelname'] = ((empty($row['levelname']) ? '普通会员' : $row['levelname']));
				$row['typestr'] = $apply_type[$row['applytype']];
				if ($row['status'] == 0)
				{
					if ($row['type'] == 0)
					{
						$row['status'] = '未充值';
					}
					else
					{
						$row['status'] = '申请中';
					}
				}
				else if ($row['status'] == 1)
				{
					if ($row['type'] == 0)
					{
						$row['status'] = '充值成功';
					}
					else
					{
						$row['status'] = '完成';
					}
				}
				else if ($row['status'] == -1)
				{
					if ($row['type'] == 0)
					{
						$row['status'] = '';
					}
					else
					{
						$row['status'] = '失败';
					}
				}
				if ($row['rechargetype'] == 'system')
				{
					$row['rechargetype'] = '后台';
				}
				else if ($row['rechargetype'] == 'wechat')
				{
					$row['rechargetype'] = '微信';
				}
				else if ($row['rechargetype'] == 'alipay')
				{
					$row['rechargetype'] = '支付宝';
				}
			}
			unset($row);
			$columns = array();
			$columns[] = array('title' => '昵称', 'field' => 'nickname', 'width' => 12);
			$columns[] = array('title' => '姓名', 'field' => 'realname', 'width' => 12);
			$columns[] = array('title' => '手机号', 'field' => 'mobile', 'width' => 12);
			$columns[] = array('title' => '会员等级', 'field' => 'levelname', 'width' => 12);
			$columns[] = array('title' => '会员分组', 'field' => 'groupname', 'width' => 12);
			$columns[] = array('title' => (empty($type) ? '充值金额' : '提现金额'), 'field' => 'money', 'width' => 12);
			if (!(empty($type)))
			{
				$columns[] = array('title' => '到账金额', 'field' => 'realmoney', 'width' => 12);
				$columns[] = array('title' => '手续费金额', 'field' => 'deductionmoney', 'width' => 12);
				$columns[] = array('title' => '提现方式', 'field' => 'typestr', 'width' => 12);
				$columns[] = array('title' => '提现姓名', 'field' => 'applyrealname', 'width' => 24);
				$columns[] = array('title' => '支付宝', 'field' => 'alipay', 'width' => 24);
				$columns[] = array('title' => '银行', 'field' => 'bankname', 'width' => 24);
				$columns[] = array('title' => '银行卡号', 'field' => 'bankcard', 'width' => 24);
				$columns[] = array('title' => '申请时间', 'field' => 'applytime', 'width' => 24);
			}
			$columns[] = array('title' => (empty($type) ? '充值时间' : '提现申请时间'), 'field' => 'createtime', 'width' => 12);
			if (empty($type))
			{
				$columns[] = array('title' => '充值方式', 'field' => 'rechargetype', 'width' => 12);
			}
			$columns[] = array('title' => '备注', 'field' => 'remark', 'width' => 24);
			m('excel')->export($list, array('title' => ((empty($type) ? '会员充值数据-' : '会员提现记录')) . date('Y-m-d-H-i', time()), 'columns' => $columns));
		}
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_log') . ' log ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=log.openid and m.uniacid= log.uniacid' . ' left join ' . tablename('ewei_shop_member_group') . ' g on m.groupid=g.id' . ' left join ' . tablename('ewei_shop_member_level') . ' l on m.level =l.id' . ' where 1 ' . $condition . ' ', $params);
		$pager = pagination($total, $pindex, $psize);
		$groups = m('member')->getGroups();
		$levels = m('member')->getLevels();
		include $this->template();
	}
	public function refund()
	{
		global $_W;
		global $_GPC;
		$set = $_W['shopset']['shop'];
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log))
		{
			show_json(0, '未找到记录!');
		}
		if (!(empty($log['type'])))
		{
			show_json(0, '非充值记录!');
		}
		if ($log['rechargetype'] == 'system')
		{
			show_json(0, '后台充值无法退款!');
		}
		$current_credit = m('member')->getCredit($log['openid'], 'credit2');
		if ($current_credit < $log['money'])
		{
			show_json(0, '会员账户余额不足，无法进行退款!');
		}
		$out_refund_no = 'RR' . substr($log['logno'], 2);
		if ($log['rechargetype'] == 'wechat')
		{
			if (empty($log['isborrow']))
			{
				$result = m('finance')->refund($log['openid'], $log['logno'], $out_refund_no, $log['money'] * 100, $log['money'] * 100, (!(empty($log['apppay'])) ? true : false));
			}
			else
			{
				$result = m('finance')->refundBorrow($log['openid'], $log['logno'], $out_refund_no, $log['money'] * 100, $log['money'] * 100);
			}
		}
		else
		{
			$result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $out_refund_no, $set['name'] . '充值退款');
		}
		if (is_error($result))
		{
			show_json(0, $result['message']);
		}
		pdo_update('ewei_shop_member_log', array('status' => 3), array('id' => $id, 'uniacid' => $_W['uniacid']));
		$refundmoney = $log['money'] + $log['gives'];
		m('member')->setCredit($log['openid'], 'credit2', -$refundmoney, array(0, $set['name'] . '充值退款'));
		m('notice')->sendMemberLogMessage($log['id']);
		$member = m('member')->getMember($log['openid']);
		plog('finance.log.refund', '充值退款 ID: ' . $log['id'] . ' 金额: ' . $log['money'] . ' <br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1, array('url' => referer()));
	}
	public function wechat()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log))
		{
			show_json(0, '未找到记录!');
		}
		if ($log['deductionmoney'] == 0)
		{
			$realmoeny = $log['money'];
		}
		else
		{
			$realmoeny = $log['realmoney'];
		}
		$set = $_W['shopset']['shop'];
		$result = m('finance')->pay($log['openid'], 1, $realmoeny * 100, $log['logno'], $set['name'] . '余额提现');
		if (is_error($result))
		{
			show_json(0, array('message' => $result['message']));
		}
		pdo_update('ewei_shop_member_log', array('status' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('notice')->sendMemberLogMessage($log['id']);
		$member = m('member')->getMember($log['openid']);
		plog('finance.log.wechat', '余额提现 ID: ' . $log['id'] . ' 方式: 微信 提现金额: ' . $log['money'] . ' ,到账金额: ' . $realmoeny . ' ,手续费金额 : ' . $log['deductionmoney'] . '<br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}
	public function manual()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log))
		{
			show_json(0, '未找到记录!');
		}
		$member = m('member')->getMember($log['openid']);
		pdo_update('ewei_shop_member_log', array('status' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('notice')->sendMemberLogMessage($log['id']);
		plog('finance.log.manual', '余额提现 方式: 手动 ID: ' . $log['id'] . ' <br/>会员信息: ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}
	public function refuse()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log))
		{
			show_json(0, '未找到记录!');
		}
		pdo_update('ewei_shop_member_log', array('status' => -1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		if (0 < $log['money'])
		{
			m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(0, $set['name'] . '余额提现退回'));
		}
		m('notice')->sendMemberLogMessage($log['id']);
		plog('finance.log.refuse', '拒绝余额度提现 ID: ' . $log['id'] . ' 金额: ' . $log['money'] . ' <br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}
	public function recharge()
	{
		$this->main(0);
	}
	public function withdraw()
	{
		$this->main(1);
	}
  public function cashback()
	{
    global $_W;
		global $_GPC;
    $type = 0;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;


    // search definition
    $condition = '';
		$params = array();

    // keyword search
		if (!(empty($_GPC['keyword'])))
		{
			$_GPC['keyword'] = trim($_GPC['keyword']);
			if ($_GPC['searchfield'] == 'logno')
			{
        $condition .= ' AND ordersn like :keyword';
			}
			else if ($_GPC['searchfield'] == 'member')
			{
        $condition .= ' AND (realname like :keyword or nickname like :keyword or mobile like :keyword)';
			}
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

    // time search
		if (empty($starttime) || empty($endtime))
		{
			$starttime = strtotime('-1 month');
			$endtime = time();
		}
		if (!(empty($_GPC['time']['start'])) && !(empty($_GPC['time']['end'])))
		{
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
      $condition .= " AND createtime >= :starttime AND createtime <= :endtime ";
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

    // level search
		if (!(empty($_GPC['level'])))
		{
			$condition .= ' AND level=' . intval($_GPC['level']);
		}

    // group id search
		if (!(empty($_GPC['groupid'])))
		{
			$condition .= ' AND groupid=' . intval($_GPC['groupid']);
		}

    // status search
		if ($_GPC['status'] != '')
		{
			$condition .= ' AND num_refunded>0';
		}

    // load data
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


    // export
    if ($_GPC['export'] == 1) {
      plog('finance.recharge.export', '导出返现记录');
      foreach ($list as &$row) {
        $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
        $row['groupname'] = empty($row['groupname']) ? '无分组' : $row['groupname'];
        $row['levelname'] = empty($row['levelname']) ? '普通会员' : $row['levelname'];
      }
      unset($row);
      $columns = array(
        array('title' => '昵称', 'field' => 'nickname', 'width' => 12),
        array('title' => '姓名', 'field' => 'realname', 'width' => 12),
        array('title' => '手机号', 'field' => 'mobile', 'width' => 12),
        array('title' => '会员等级', 'field' => 'levelname', 'width' => 12),
        array('title' => '会员分组', 'field' => 'groupname', 'width' => 12),
        array('title' => "已返现总金额", 'field' => 'money', 'width' => 12),
        array('title' => "上次返现时间", 'field' => 'createtime', 'width' => 12),
      );
      m('excel')->export($list, array("title" => "会员返现记录-" . date('Y-m-d-H-i', time()), "columns" => $columns));
    }

		$pager = pagination($total, $pindex, $psize);
		$groups = m('member')->getGroups();
		$levels = m('member')->getLevels();
		include $this->template();
	}

  public function cashback_fn()
  {

    $ordersn = $_GPC['ordersn'];
    $goodssn = $_GPC['goodssn'];
    $lastdate = date('Y-m-d H:i',$_GPC['createtime']);

		// cashback validation
    if ($_GPC['num_refunded'] > 0) {
      if ($_GPC['num_refunded'] >= $_GPC['num_refund']) {
        show_json(0, '返现已于 '.$lastdate.' 完成!');
      }
			$lastFYtime = new DateTime('NOW');
			$lastFYtime->setTimestamp($_GPC['createtime']);
			$lastmonthFYtime = new DateTime('-'.strval($_GPC['period']).' month');
			$interval = $lastmonthFYtime->diff($lastFYtime);
			if ($interval->y == 0 && $interval->m == 0)  {
        show_json(0, '本月已于 '.$lastdate.' 返现!');
			}
    }

    // variable definition
    $openid = $_GPC['openid'];
    $money = floatval($_GPC['single_refund_price']) * floatval($_GPC['total']);

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
      show_json(0, '返现失败: ' . $result['message']);
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

    show_json(0, '返现成功!');

  }
}
?>
