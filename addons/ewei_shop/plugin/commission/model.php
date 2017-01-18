<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}
define('TM_COMMISSION_AGENT_NEW', 'commission_agent_new');
define('TM_COMMISSION_ORDER_PAY', 'commission_order_pay');
define('TM_COMMISSION_ORDER_FINISH', 'commission_order_finish');
define('TM_COMMISSION_APPLY', 'commission_apply');
define('TM_COMMISSION_CHECK', 'commission_check');
define('TM_COMMISSION_PAY', 'commission_pay');
define('TM_COMMISSION_UPGRADE', 'commission_upgrade');
define('TM_COMMISSION_BECOME', 'commission_become');
if (!class_exists('CommissionModel')) {
	class CommissionModel extends PluginModel
	{
		public function getSet($_var_0 = 0)
		{
			$set = parent::getSet($_var_0);
			$set['texts'] = array('agent' => empty($set['texts']['agent']) ? '分销商' : $set['texts']['agent'], 'shop' => empty($set['texts']['shop']) ? '小店' : $set['texts']['shop'], 'myshop' => empty($set['texts']['myshop']) ? '我的小店' : $set['texts']['myshop'], 'center' => empty($set['texts']['center']) ? '分销中心' : $set['texts']['center'], 'become' => empty($set['texts']['become']) ? '成为分销商' : $set['texts']['become'], 'withdraw' => empty($set['texts']['withdraw']) ? '提现' : $set['texts']['withdraw'], 'commission' => empty($set['texts']['commission']) ? '佣金' : $set['texts']['commission'], 'commission1' => empty($set['texts']['commission1']) ? '分销佣金' : $set['texts']['commission1'], 'commission_total' => empty($set['texts']['commission_total']) ? '累计佣金' : $set['texts']['commission_total'], 'commission_ok' => empty($set['texts']['commission_ok']) ? '可提现佣金' : $set['texts']['commission_ok'], 'commission_apply' => empty($set['texts']['commission_apply']) ? '已申请佣金' : $set['texts']['commission_apply'], 'commission_check' => empty($set['texts']['commission_check']) ? '待打款佣金' : $set['texts']['commission_check'], 'commission_lock' => empty($set['texts']['commission_lock']) ? '未结算佣金' : $set['texts']['commission_lock'], 'commission_detail' => empty($set['texts']['commission_detail']) ? '佣金明细' : $set['texts']['commission_detail'], 'commission_pay' => empty($set['texts']['commission_pay']) ? '成功提现佣金' : $set['texts']['commission_pay'], 'order' => empty($set['texts']['order']) ? '分销订单' : $set['texts']['order'], 'myteam' => empty($set['texts']['myteam']) ? '我的团队' : $set['texts']['myteam'], 'c1' => empty($set['texts']['c1']) ? '一级' : $set['texts']['c1'], 'c2' => empty($set['texts']['c2']) ? '二级' : $set['texts']['c2'], 'c3' => empty($set['texts']['c3']) ? '三级' : $set['texts']['c3'], 'mycustomer' => empty($set['texts']['mycustomer']) ? '我的客户' : $set['texts']['mycustomer'],);
			return $set;
		}
		
		public function calculate($orderid = 0, $update = true)
		{
			global $_W;
			//获取基本分销设置
			$set = $this->getSet();
			//获取分销等级
			$levels = $this->getLevels();
			//获得分销商的ID
			$agentid = pdo_fetchcolumn('select agentid from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));
			$oid = pdo_fetchcolumn('select openid from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));;
			//获得代理商（会员）的级别
			$mlevel = intval(pdo_fetchcolumn('select level from ' . tablename('ewei_shop_member') . ' where openid=:id limit 1', array(':id' => $oid)));
			$agentlevels = $this->getMemberLevels();
			//获取商品分销独立佣金设置及售出商品信息
			$goods = pdo_fetchall('select og.id,og.realprice,og.total,g.hascommission,g.nocommission, g.commission1_rate,g.commission1_pay,g.commission2_rate,g.commission2_pay,g.commission3_rate,g.commission3_pay,og.commissions from ' . tablename('ewei_shop_order_goods') . '  og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id = og.goodsid' . ' where og.orderid=:orderid and og.uniacid=:uniacid', array(':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
			//判断是几级分销,==0为无分销
			if ($set['level'] > 0) {
				foreach ($goods as &$cinfo) {
					$price = $cinfo['realprice'];
					if (empty($cinfo['nocommission'])) {
						if ($cinfo['hascommission'] == 1) {//商品具有独立佣金设置
							$cinfo['commission1'] = array('default' => $set['level'] >= 1 ? ($cinfo['commission1_rate'] > 0 ? round($cinfo['commission1_rate'] * $price / 100, 2) . "" : round($cinfo['commission1_pay'] * $cinfo['total'], 2)) : 0);
							$cinfo['commission2'] = array('default' => $set['level'] >= 2 ? ($cinfo['commission2_rate'] > 0 ? round($cinfo['commission2_rate'] * $price / 100, 2) . "" : round($cinfo['commission2_pay'] * $cinfo['total'], 2)) : 0);
							$cinfo['commission3'] = array('default' => $set['level'] >= 3 ? ($cinfo['commission3_rate'] > 0 ? round($cinfo['commission3_rate'] * $price / 100, 2) . "" : round($cinfo['commission3_pay'] * $cinfo['total'], 2)) : 0);
							foreach ($levels as $level) {
								$cinfo['commission1']['level' . $level['id']] = $cinfo['commission1_rate'] > 0 ? round($cinfo['commission1_rate'] * $price / 100, 2) . "" : round($cinfo['commission1_pay'] * $cinfo['total'], 2);
								$cinfo['commission2']['level' . $level['id']] = $cinfo['commission2_rate'] > 0 ? round($cinfo['commission2_rate'] * $price / 100, 2) . "" : round($cinfo['commission2_pay'] * $cinfo['total'], 2);
								$cinfo['commission3']['level' . $level['id']] = $cinfo['commission3_rate'] > 0 ? round($cinfo['commission3_rate'] * $price / 100, 2) . "" : round($cinfo['commission3_pay'] * $cinfo['total'], 2);
							}
						} else {
							$cinfo['commission1'] = array('default' => $set['level'] >= 1 ? round($set['commission1'] * $price / 100, 2) . "" : 0);
							$cinfo['commission2'] = array('default' => $set['level'] >= 2 ? round($set['commission2'] * $price / 100, 2) . "" : 0);
							$cinfo['commission3'] = array('default' => $set['level'] >= 3 ? round($set['commission3'] * $price / 100, 2) . "" : 0);
							foreach ($levels as $level) {
								$cinfo['commission1']['level' . $level['id']] = $set['level'] >= 1 ? round($level['commission1'] * $price / 100, 2) . "" : 0;
								$cinfo['commission2']['level' . $level['id']] = $set['level'] >= 2 ? round($level['commission2'] * $price / 100, 2) . "" : 0;
								$cinfo['commission3']['level' . $level['id']] = $set['level'] >= 3 ? round($level['commission3'] * $price / 100, 2) . "" : 0;
							}
						}
					} else {
						$cinfo['commission1'] = array('default' => 0);
						$cinfo['commission2'] = array('default' => 0);
						$cinfo['commission3'] = array('default' => 0);
						foreach ($levels as $level) {
							$cinfo['commission1']['level' . $level['id']] = 0;
							$cinfo['commission2']['level' . $level['id']] = 0;
							$cinfo['commission3']['level' . $level['id']] = 0;
						}
					}
					//计算代理商的进货返点
					foreach ($agentlevels as $agentlevel) {
						$cinfo['commission1']['agent' . $agentlevel['id']] = round($agentlevel['rate1'] * $price / 100, 2). "";
						$cinfo['commission2']['agent' . $agentlevel['id']] = round($agentlevel['rate2'] * $price / 100, 2). "";;
						$cinfo['commission3']['agent' . $agentlevel['id']] = round($agentlevel['rate3'] * $price / 100, 2). "";;
					}
					if ($update) {
						$commissions = array('level1' => 0, 'level2' => 0, 'level3' => 0);
						//针对当前商品计算每一层分销商的具体佣金
						$isdealer = ($mlevel >=1 && $mlevel <= 4 && ($cinfo['hascommission'] <> 1) ); //我是代理商且商品没有独立的佣金！！！
						if (!empty($agentid)) {//如果有上层分销商
							//获得第一层分销商的信息
							$m1 = m('member')->getMember($agentid);
							//如果分销商存在且正常
							if ($m1['isagent'] == 1 && $m1['status'] == 1) {
								//获得分销商的级别
								$level1 = $this->getLevel($m1['openid']);
								//计算第一层该级别分销商的佣金
								if ($isdealer) {//如果我是代理商
									$dealer = intval($m1['level']);
									if ($dealer >= 1 && $dealer <= 4) {//如果分销商也是代理商
										$commissions['level1'] = round($cinfo['commission1']['agent' .$m1['level']], 2);//按代理商的等级计算
									}
									else {//代理商的上一级不是代理商则不享受佣金
										$commissions['level1'] = 0;
									}
								}
								else {
									$commissions['level1'] = empty($level1) ? round($cinfo['commission1']['default'], 2) : round($cinfo['commission1']['level' . $level1['id']], 2);
								}
								//是否有第二层分销商
								if (!empty($m1['agentid'])) {
									$m2 = m('member')->getMember($m1['agentid']);
									$level2 = $this->getLevel($m2['openid']);
									//计算第二层该级别分销商的佣金
									if ($isdealer) {//如果我是代理商
										$dealer = intval($m2['level']);
										if ($dealer >= 1 && $dealer <= 4) {//如果第二层分销商也是代理商
											$commissions['level2'] = round($cinfo['commission2']['agent' .$m2['level']], 2);//按代理商的等级计算
										}
										else {//代理商的上一级不是代理商则不享受佣金
											$commissions['level2'] = 0;
										}
									}
									else {
										$commissions['level2'] = empty($level2) ? round($cinfo['commission2']['default'], 2) : round($cinfo['commission2']['level' . $level2['id']], 2);
									}
									//是否有第三层分销商
									if (!empty($m2['agentid'])) {
										$m3 = m('member')->getMember($m2['agentid']);
										$level3 = $this->getLevel($m3['openid']);
										if ($isdealer) {//如果我是代理商
											$dealer = intval($m3['level']);
											if ($dealer >= 1 && $dealer <= 4) {//如果第三层分销商也是代理商
												$commissions['level3'] = round($cinfo['commission3']['agent' .$m3['level']], 2);//按代理商的等级计算
											}
											else {//代理商的上一级不是代理商则不享受佣金
												$commissions['level3'] = 0;
											}
										}
										else {
											$commissions['level3'] = empty($level3) ? round($cinfo['commission3']['default'], 2) : round($cinfo['commission3']['level' . $level3['id']], 2);
										}
									}
								}
							}
						}
						pdo_update('ewei_shop_order_goods', array('commission1' => iserializer($cinfo['commission1']), 'commission2' => iserializer($cinfo['commission2']), 'commission3' => iserializer($cinfo['commission3']), 'commissions' => iserializer($commissions), 'nocommission' => $cinfo['nocommission']), array('id' => $cinfo['id']));
					}
				}
				unset($cinfo);
			}
			return $goods;
		}

		function getMemberLevels()
		{
			return pdo_fetchall('select * from ' . tablename('ewei_shop_member_level'), null);
		}

		public function getOrderCommissions($orderid = 0, $_var_17 = 0)
		{
			global $_W;
			$set = $this->getSet();
			$agentid = pdo_fetchcolumn('select agentid from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));
			$goods = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('ewei_shop_order_goods') . ' where id=:id and orderid=:orderid and uniacid=:uniacid and nocommission=0 limit 1', array(':id' => $_var_17, ':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
			$commissions = array('level1' => 0, 'level2' => 0, 'level3' => 0);
			if ($set['level'] > 0) {
				$_var_18 = iunserializer($goods['commission1']);
				$_var_19 = iunserializer($goods['commission2']);
				$_var_20 = iunserializer($goods['commission3']);
				if (!empty($agentid)) {
					$m1 = m('member')->getMember($agentid);
					if ($m1['isagent'] == 1 && $m1['status'] == 1) {
						$level1 = $this->getLevel($m1['openid']);
						$commissions['level1'] = empty($level1) ? round($_var_18['default'], 2) : round($_var_18['level' . $level1['id']], 2);
						if (!empty($m1['agentid'])) {
							$m2 = m('member')->getMember($m1['agentid']);
							$level2 = $this->getLevel($m2['openid']);
							$commissions['level2'] = empty($level2) ? round($_var_19['default'], 2) : round($_var_19['level' . $level2['id']], 2);
							if (!empty($m2['agentid'])) {
								$m3 = m('member')->getMember($m2['agentid']);
								$level3 = $this->getLevel($m3['openid']);
								$commissions['level3'] = empty($level3) ? round($_var_20['default'], 2) : round($_var_20['level' . $level3['id']], 2);
							}
						}
					}
				}
			}
			return $commissions;
		}

		public function getInfo($_var_21, $_var_22 = null)
		{
			if (empty($_var_22) || !is_array($_var_22)) {
				$_var_22 = array();
			}
			global $_W;
			$set = $this->getSet();
			$level = intval($set['level']);
			$_var_23 = m('member')->getMember($_var_21);
			$_var_24 = $this->getLevel($_var_21);
			$_var_25 = time();
			$_var_26 = intval($set['settledays']) * 3600 * 24;
			$_var_27 = 0;
			$dealercount = 0;
			$_var_28 = 0;
			$_var_29 = 0;
			$_var_30 = 0;
			$_var_31 = 0;
			$_var_32 = 0;
			$_var_33 = 0;
			$_var_34 = 0;
			$_var_35 = 0;
			$_var_36 = 0;
			$_var_37 = 0;
			$_var_38 = 0;
			$_var_39 = 0;
			$_var_40 = 0;
			$_var_41 = 0;
			$_var_42 = 0;
			$level1_dealer = 0;
			$level2_dealer = 0;
			$level3_dealer = 0;
			$_var_43 = 0;
			$_var_44 = 0;
			$_var_45 = 0;
			$_var_46 = 0;
			$_var_47 = 0;
			$_var_48 = 0;
			$_var_49 = 0;
			$_var_50 = 0;
			$_var_51 = 0;
			$_var_52 = 0;
			$_var_53 = 0;
			$_var_54 = 0;
			if ($level >= 1) {
				if (in_array('ordercount0', $_var_22)) {
					$_var_55 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=0 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					$_var_43 += $_var_55['ordercount'];
					$_var_28 += $_var_55['ordercount'];
					$_var_29 += $_var_55['ordermoney'];
				}
				if (in_array('ordercount', $_var_22)) {
					$_var_55 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=1 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					$_var_46 += $_var_55['ordercount'];
					$_var_30 += $_var_55['ordercount'];
					$_var_31 += $_var_55['ordermoney'];
				}
				if (in_array('ordercount3', $_var_22)) {
					$_var_56 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=3 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					$_var_49 += $_var_56['ordercount'];
					$_var_32 += $_var_56['ordercount'];
					$_var_33 += $_var_56['ordermoney'];
					$_var_52 += $_var_56['ordermoney'];
				}
				if (in_array('total', $_var_22)) {
					$_var_57 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					foreach ($_var_57 as $_var_58) {
						$commissions = iunserializer($_var_58['commissions']);
						$_var_59 = iunserializer($_var_58['commission1']);
						if (empty($commissions)) {
							$_var_34 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
						} else {
							$_var_34 += isset($commissions['level1']) ? floatval($commissions['level1']) : 0;
						}
					}
				}
				if (in_array('ok', $_var_22)) {
					$_var_57 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$_var_25} - o.finishtime > {$_var_26}) and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					foreach ($_var_57 as $_var_58) {
						$commissions = iunserializer($_var_58['commissions']);
						$_var_59 = iunserializer($_var_58['commission1']);
						if (empty($commissions)) {
							$_var_35 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
						} else {
							$_var_35 += isset($commissions['level1']) ? $commissions['level1'] : 0;
						}
					}
				}
				if (in_array('lock', $_var_22)) {
					$_var_60 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$_var_25} - o.finishtime <= {$_var_26})  and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					foreach ($_var_60 as $_var_58) {
						$commissions = iunserializer($_var_58['commissions']);
						$_var_59 = iunserializer($_var_58['commission1']);
						if (empty($commissions)) {
							$_var_38 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
						} else {
							$_var_38 += isset($commissions['level1']) ? $commissions['level1'] : 0;
						}
					}
				}
				if (in_array('apply', $_var_22)) {
					$_var_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					foreach ($_var_61 as $_var_58) {
						$commissions = iunserializer($_var_58['commissions']);
						$_var_59 = iunserializer($_var_58['commission1']);
						if (empty($commissions)) {
							$_var_36 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
						} else {
							$_var_36 += isset($commissions['level1']) ? $commissions['level1'] : 0;
						}
					}
				}
				if (in_array('check', $_var_22)) {
					$_var_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=2 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					foreach ($_var_61 as $_var_58) {
						$commissions = iunserializer($_var_58['commissions']);
						$_var_59 = iunserializer($_var_58['commission1']);
						if (empty($commissions)) {
							$_var_37 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
						} else {
							$_var_37 += isset($commissions['level1']) ? $commissions['level1'] : 0;
						}
					}
				}
				if (in_array('pay', $_var_22)) {
					$_var_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=3 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']));
					foreach ($_var_61 as $_var_58) {
						$commissions = iunserializer($_var_58['commissions']);
						$_var_59 = iunserializer($_var_58['commission1']);
						if (empty($commissions)) {
							$_var_39 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
						} else {
							$_var_39 += isset($commissions['level1']) ? $commissions['level1'] : 0;
						}
					}
				}
				$_var_62 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']), 'id');
				$_var_40 = count($_var_62);
				$_var_27 += $_var_40;

				$level1_dealers = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and level>0 and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $_var_23['id']), 'id');
				$level1_dealer = count($level1_dealers);
				$dealercount += $level1_dealer;
			}
			if ($level >= 2) {
				if ($_var_40 > 0) {
					if (in_array('ordercount0', $_var_22)) {
						$_var_63 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ')  and o.status>=0 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_44 += $_var_63['ordercount'];
						$_var_28 += $_var_63['ordercount'];
						$_var_29 += $_var_63['ordermoney'];
					}
					if (in_array('ordercount', $_var_22)) {
						$_var_63 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ')  and o.status>=1 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_47 += $_var_63['ordercount'];
						$_var_30 += $_var_63['ordercount'];
						$_var_31 += $_var_63['ordermoney'];
					}
					if (in_array('ordercount3', $_var_22)) {
						$_var_64 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ')  and o.status>=3 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_50 += $_var_64['ordercount'];
						$_var_32 += $_var_64['ordercount'];
						$_var_33 += $_var_64['ordermoney'];
						$_var_53 += $_var_64['ordermoney'];
					}
					if (in_array('total', $_var_22)) {
						$_var_65 = pdo_fetchall('select og.commission2,og.commissions from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_65 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission2']);
							if (empty($commissions)) {
								$_var_34 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_34 += isset($commissions['level2']) ? $commissions['level2'] : 0;
							}
						}
					}
					if (in_array('ok', $_var_22)) {
						$_var_65 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ")  and ({$_var_25} - o.createtime > {$_var_26}) and o.status>=3 and og.status2=0 and og.nocommission=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($_var_65 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission2']);
							if (empty($commissions)) {
								$_var_35 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_35 += isset($commissions['level2']) ? $commissions['level2'] : 0;
							}
						}
					}
					if (in_array('lock', $_var_22)) {
						$_var_66 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ")  and ({$_var_25} - o.createtime <= {$_var_26}) and og.status2=0 and o.status>=3 and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($_var_66 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission2']);
							if (empty($commissions)) {
								$_var_38 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_38 += isset($commissions['level2']) ? $commissions['level2'] : 0;
							}
						}
					}
					if (in_array('apply', $_var_22)) {
						$_var_67 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ')  and o.status>=3 and og.status2=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_67 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission2']);
							if (empty($commissions)) {
								$_var_36 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_36 += isset($commissions['level2']) ? $commissions['level2'] : 0;
							}
						}
					}
					if (in_array('check', $_var_22)) {
						$_var_68 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ')  and o.status>=3 and og.status2=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_68 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission2']);
							if (empty($commissions)) {
								$_var_37 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_37 += isset($commissions['level2']) ? $commissions['level2'] : 0;
							}
						}
					}
					if (in_array('pay', $_var_22)) {
						$_var_68 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($_var_62)) . ')  and o.status>=3 and og.status2=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_68 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission2']);
							if (empty($commissions)) {
								$_var_39 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_39 += isset($commissions['level2']) ? $commissions['level2'] : 0;
							}
						}
					}
					$_var_69 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($_var_62)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
					$_var_41 = count($_var_69);
					$_var_27 += $_var_41;

					$level2_dealers = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($_var_62)) . ') and level>0 and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
					$level2_dealer = count($level2_dealers);
					$dealercount += $level2_dealer;
				}
			}
			if ($level >= 3) {
				if ($_var_41 > 0) {
					if (in_array('ordercount0', $_var_22)) {
						$_var_70 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ')  and o.status>=0 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_45 += $_var_70['ordercount'];
						$_var_28 += $_var_70['ordercount'];
						$_var_29 += $_var_70['ordermoney'];
					}
					if (in_array('ordercount', $_var_22)) {
						$_var_70 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ')  and o.status>=1 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_48 += $_var_70['ordercount'];
						$_var_30 += $_var_70['ordercount'];
						$_var_31 += $_var_70['ordermoney'];
					}
					if (in_array('ordercount3', $_var_22)) {
						$_var_71 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ')  and o.status>=3 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
						$_var_51 += $_var_71['ordercount'];
						$_var_32 += $_var_71['ordercount'];
						$_var_33 += $_var_71['ordermoney'];
						$_var_54 += $_var_70['ordermoney'];
					}
					if (in_array('total', $_var_22)) {
						$_var_72 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_72 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission3']);
							if (empty($commissions)) {
								$_var_34 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_34 += isset($commissions['level3']) ? $commissions['level3'] : 0;
							}
						}
					}
					if (in_array('ok', $_var_22)) {
						$_var_72 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ")  and ({$_var_25} - o.createtime > {$_var_26}) and o.status>=3 and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($_var_72 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission3']);
							if (empty($commissions)) {
								$_var_35 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_35 += isset($commissions['level3']) ? $commissions['level3'] : 0;
							}
						}
					}
					if (in_array('lock', $_var_22)) {
						$_var_73 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ")  and o.status>=3 and ({$_var_25} - o.createtime > {$_var_26}) and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
						foreach ($_var_73 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission3']);
							if (empty($commissions)) {
								$_var_38 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_38 += isset($commissions['level3']) ? $commissions['level3'] : 0;
							}
						}
					}
					if (in_array('apply', $_var_22)) {
						$_var_74 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ')  and o.status>=3 and og.status3=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_74 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission3']);
							if (empty($commissions)) {
								$_var_36 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_36 += isset($commissions['level3']) ? $commissions['level3'] : 0;
							}
						}
					}
					if (in_array('check', $_var_22)) {
						$_var_75 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ')  and o.status>=3 and og.status3=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_75 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission3']);
							if (empty($commissions)) {
								$_var_37 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_37 += isset($commissions['level3']) ? $commissions['level3'] : 0;
							}
						}
					}
					if (in_array('pay', $_var_22)) {
						$_var_75 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($_var_69)) . ')  and o.status>=3 and og.status3=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
						foreach ($_var_75 as $_var_58) {
							$commissions = iunserializer($_var_58['commissions']);
							$_var_59 = iunserializer($_var_58['commission3']);
							if (empty($commissions)) {
								$_var_39 += isset($_var_59['level' . $_var_24['id']]) ? $_var_59['level' . $_var_24['id']] : $_var_59['default'];
							} else {
								$_var_39 += isset($commissions['level3']) ? $commissions['level3'] : 0;
							}
						}
					}
					$_var_76 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and agentid in( ' . implode(',', array_keys($_var_69)) . ') and isagent=1 and status=1', array(':uniacid' => $_W['uniacid']), 'id');
					$_var_42 = count($_var_76);
					$_var_27 += $_var_42;

					$level3_dealers = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and agentid in( ' . implode(',', array_keys($_var_69)) . ') and isagent=1 and status=1', array(':uniacid' => $_W['uniacid']), 'id');
					$level3_dealer = count($level3_dealers);
					$dealercount += $level3_dealer;
				}
			}
			$_var_23['agentcount'] = $_var_27;
			$_var_23['ordercount'] = $_var_30;
			$_var_23['ordermoney'] = $_var_31;
			$_var_23['order1'] = $_var_46;
			$_var_23['order2'] = $_var_47;
			$_var_23['order3'] = $_var_48;
			$_var_23['ordercount3'] = $_var_32;
			$_var_23['ordermoney3'] = $_var_33;
			$_var_23['order13'] = $_var_49;
			$_var_23['order23'] = $_var_50;
			$_var_23['order33'] = $_var_51;
			$_var_23['order13money'] = $_var_52;
			$_var_23['order23money'] = $_var_53;
			$_var_23['order33money'] = $_var_54;
			$_var_23['ordercount0'] = $_var_28;
			$_var_23['ordermoney0'] = $_var_29;
			$_var_23['order10'] = $_var_43;
			$_var_23['order20'] = $_var_44;
			$_var_23['order30'] = $_var_45;
			$_var_23['commission_total'] = round($_var_34, 2);
			$_var_23['commission_ok'] = round($_var_35, 2);
			$_var_23['commission_lock'] = round($_var_38, 2);
			$_var_23['commission_apply'] = round($_var_36, 2);
			$_var_23['commission_check'] = round($_var_37, 2);
			$_var_23['commission_pay'] = round($_var_39, 2);
			$_var_23['level1'] = $_var_40;
			$_var_23['level1_agentids'] = $_var_62;
			$_var_23['level2'] = $_var_41;
			$_var_23['level2_agentids'] = $_var_69;
			$_var_23['level3'] = $_var_42;
			$_var_23['level3_agentids'] = $_var_76;

			$_var_23['dealercount'] = $dealercount;
			$_var_23['dealerlevel1'] = $level1_dealer;
			$_var_23['level1_dealers'] = $level1_dealers;
			$_var_23['dealerlevel2'] = $level2_dealer;
			$_var_23['level2_dealers'] = $level2_dealers;
			$_var_23['dealerlevel3'] = $level3_dealer;
			$_var_23['level3_dealers'] = $level3_dealers;

			$_var_23['agenttime'] = date('Y-m-d H:i', $_var_23['agenttime']);
			return $_var_23;
		}

		public function getAgents($orderid = 0)
		{
			global $_W, $_GPC;
			$_var_77 = array();
			$_var_78 = pdo_fetch('select id,agentid,openid from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($_var_78)) {
				return $_var_77;
			}
			$m1 = m('member')->getMember($_var_78['agentid']);
			if (!empty($m1) && $m1['isagent'] == 1 && $m1['status'] == 1) {
				$_var_77[] = $m1;
				if (!empty($m1['agentid'])) {
					$m2 = m('member')->getMember($m1['agentid']);
					if (!empty($m2) && $m2['isagent'] == 1 && $m2['status'] == 1) {
						$_var_77[] = $m2;
						if (!empty($m2['agentid'])) {
							$m3 = m('member')->getMember($m2['agentid']);
							if (!empty($m3) && $m3['isagent'] == 1 && $m3['status'] == 1) {
								$_var_77[] = $m3;
							}
						}
					}
				}
			}
			return $_var_77;
		}

		public function isAgent($_var_21)
		{
			if (empty($_var_21)) {
				return false;
			}
			if (is_array($_var_21)) {
				return $_var_21['isagent'] == 1 && $_var_21['status'] == 1;
			}
			$_var_23 = m('member')->getMember($_var_21);
			return $_var_23['isagent'] == 1 && $_var_23['status'] == 1;
		}

		public function getCommission($goods)
		{
			global $_W;
			$set = $this->getSet();
			$_var_59 = 0;
			if ($goods['hascommission'] == 1) {
				$_var_59 = $set['level'] >= 1 ? ($goods['commission1_rate'] > 0 ? ($goods['commission1_rate'] * $goods['marketprice'] / 100) : $goods['commission1_pay']) : 0;
			} else {
				$_var_21 = m('user')->getOpenid();
				$level = $this->getLevel($_var_21);
				if (!empty($level)) {
					$_var_59 = $set['level'] >= 1 ? round($level['commission1'] * $goods['marketprice'] / 100, 2) : 0;
				} else {
					$_var_59 = $set['level'] >= 1 ? round($set['commission1'] * $goods['marketprice'] / 100, 2) : 0;
				}
			}
			return $_var_59;
		}

		public function createMyShopQrcode($_var_79 = 0, $_var_80 = 0)
		{
			global $_W;
			$_var_81 = IA_ROOT . '/addons/ewei_shop/data/qrcode/' . $_W['uniacid'];
			if (!is_dir($_var_81)) {
				load()->func('file');
				mkdirs($_var_81);
			}
			$_var_82 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=' . $_var_79;
			if (!empty($_var_80)) {
				$_var_82 .= '&posterid=' . $_var_80;
			}
			$_var_83 = 'myshop_' . $_var_80 . '_' . $_var_79 . '.png';
			$_var_84 = $_var_81 . '/' . $_var_83;
			if (!is_file($_var_84)) {
				require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
				QRcode::png($_var_82, $_var_84, QR_ECLEVEL_H, 4);
			}
			return $_W['siteroot'] . 'addons/ewei_shop/data/qrcode/' . $_W['uniacid'] . '/' . $_var_83;
		}

		private function createImage($_var_82)
		{
			load()->func('communication');
			$_var_85 = ihttp_request($_var_82);
			return imagecreatefromstring($_var_85['content']);
		}

		public function createGoodsImage($goods, $_var_86)
		{
			global $_W, $_GPC;
			$goods = set_medias($goods, 'thumb');
			$_var_21 = m('user')->getOpenid();
			$_var_87 = m('member')->getMember($_var_21);
			if ($_var_87['isagent'] == 1 && $_var_87['status'] == 1) {
				$_var_88 = $_var_87;
			} else {
				$_var_79 = intval($_GPC['mid']);
				if (!empty($_var_79)) {
					$_var_88 = m('member')->getMember($_var_79);
				}
			}
			$_var_81 = IA_ROOT . '/addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/';
			if (!is_dir($_var_81)) {
				load()->func('file');
				mkdirs($_var_81);
			}
			$_var_89 = empty($goods['commission_thumb']) ? $goods['thumb'] : tomedia($goods['commission_thumb']);
			$_var_90 = md5(json_encode(array('id' => $goods['id'], 'marketprice' => $goods['marketprice'], 'productprice' => $goods['productprice'], 'img' => $_var_89, 'openid' => $_var_21, 'version' => 4)));
			$_var_83 = $_var_90 . '.jpg';
			if (!is_file($_var_81 . $_var_83)) {
				set_time_limit(0);
				$_var_91 = IA_ROOT . '/addons/ewei_shop/static/fonts/msyh.ttf';
				$_var_92 = imagecreatetruecolor(640, 1225);
				$_var_93 = imagecreatefromjpeg(IA_ROOT . '/addons/ewei_shop/plugin/commission/images/poster.jpg');
				imagecopy($_var_92, $_var_93, 0, 0, 0, 0, 640, 1225);
				imagedestroy($_var_93);
				$_var_94 = preg_replace('/\\/0$/i', '/96', $_var_88['avatar']);
				$_var_95 = $this->createImage($_var_94);
				$_var_96 = imagesx($_var_95);
				$_var_97 = imagesy($_var_95);
				imagecopyresized($_var_92, $_var_95, 24, 32, 0, 0, 88, 88, $_var_96, $_var_97);
				imagedestroy($_var_95);
				$_var_98 = $this->createImage($_var_89);
				$_var_96 = imagesx($_var_98);
				$_var_97 = imagesy($_var_98);
				imagecopyresized($_var_92, $_var_98, 0, 160, 0, 0, 640, 640, $_var_96, $_var_97);
				imagedestroy($_var_98);
				$_var_99 = imagecreatetruecolor(640, 127);
				imagealphablending($_var_99, false);
				imagesavealpha($_var_99, true);
				$_var_100 = imagecolorallocatealpha($_var_99, 0, 0, 0, 25);
				imagefill($_var_99, 0, 0, $_var_100);
				imagecopy($_var_92, $_var_99, 0, 678, 0, 0, 640, 127);
				imagedestroy($_var_99);
				$_var_101 = tomedia(m('qrcode')->createGoodsQrcode($_var_88['id'], $goods['id']));
				$_var_102 = $this->createImage($_var_101);
				$_var_96 = imagesx($_var_102);
				$_var_97 = imagesy($_var_102);
				imagecopyresized($_var_92, $_var_102, 50, 835, 0, 0, 250, 250, $_var_96, $_var_97);
				imagedestroy($_var_102);
				$_var_103 = imagecolorallocate($_var_92, 0, 3, 51);
				$_var_104 = imagecolorallocate($_var_92, 240, 102, 0);
				$_var_105 = imagecolorallocate($_var_92, 255, 255, 255);
				$_var_106 = imagecolorallocate($_var_92, 255, 255, 0);
				$_var_107 = '我是';
				imagettftext($_var_92, 20, 0, 150, 70, $_var_103, $_var_91, $_var_107);
				imagettftext($_var_92, 20, 0, 210, 70, $_var_104, $_var_91, $_var_88['nickname']);
				$_var_108 = '我要为';
				imagettftext($_var_92, 20, 0, 150, 105, $_var_103, $_var_91, $_var_108);
				$_var_109 = $_var_86['name'];
				imagettftext($_var_92, 20, 0, 240, 105, $_var_104, $_var_91, $_var_109);
				$_var_110 = imagettfbbox(20, 0, $_var_91, $_var_109);
				$_var_111 = $_var_110[4] - $_var_110[6];
				$_var_112 = '代言';
				imagettftext($_var_92, 20, 0, 240 + $_var_111 + 10, 105, $_var_103, $_var_91, $_var_112);
				$_var_113 = mb_substr($goods['title'], 0, 50, 'utf-8');
				imagettftext($_var_92, 20, 0, 30, 730, $_var_105, $_var_91, $_var_113);
				$_var_114 = '￥' . number_format($goods['marketprice'], 2);
				imagettftext($_var_92, 25, 0, 25, 780, $_var_106, $_var_91, $_var_114);
				$_var_110 = imagettfbbox(26, 0, $_var_91, $_var_114);
				$_var_111 = $_var_110[4] - $_var_110[6];
				if ($goods['productprice'] > 0) {
					$_var_115 = '￥' . number_format($goods['productprice'], 2);
					imagettftext($_var_92, 22, 0, 25 + $_var_111 + 10, 780, $_var_105, $_var_91, $_var_115);
					$_var_116 = 25 + $_var_111 + 10;
					$_var_110 = imagettfbbox(22, 0, $_var_91, $_var_115);
					$_var_111 = $_var_110[4] - $_var_110[6];
					imageline($_var_92, $_var_116, 770, $_var_116 + $_var_111 + 20, 770, $_var_105);
					imageline($_var_92, $_var_116, 771.5, $_var_116 + $_var_111 + 20, 771, $_var_105);
				}
				imagejpeg($_var_92, $_var_81 . $_var_83);
				imagedestroy($_var_92);
			}
			return $_W['siteroot'] . 'addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/' . $_var_83;
		}

		public function createShopImage($_var_86)
		{
			global $_W, $_GPC;
			$_var_86 = set_medias($_var_86, 'signimg');
			$_var_81 = IA_ROOT . '/addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/';
			if (!is_dir($_var_81)) {
				load()->func('file');
				mkdirs($_var_81);
			}
			$_var_79 = intval($_GPC['mid']);
			$_var_21 = m('user')->getOpenid();
			$_var_87 = m('member')->getMember($_var_21);
			if ($_var_87['isagent'] == 1 && $_var_87['status'] == 1) {
				$_var_88 = $_var_87;
			} else {
				$_var_79 = intval($_GPC['mid']);
				if (!empty($_var_79)) {
					$_var_88 = m('member')->getMember($_var_79);
				}
			}
			$_var_90 = md5(json_encode(array('openid' => $_var_21, 'signimg' => $_var_86['signimg'], 'version' => 4)));
			$_var_83 = $_var_90 . '.jpg';
			if (!is_file($_var_81 . $_var_83)) {
				set_time_limit(0);
				@ini_set('memory_limit', '256M');
				$_var_91 = IA_ROOT . '/addons/ewei_shop/static/fonts/msyh.ttf';
				$_var_92 = imagecreatetruecolor(640, 1225);
				$_var_103 = imagecolorallocate($_var_92, 0, 3, 51);
				$_var_104 = imagecolorallocate($_var_92, 240, 102, 0);
				$_var_105 = imagecolorallocate($_var_92, 255, 255, 255);
				$_var_106 = imagecolorallocate($_var_92, 255, 255, 0);
				$_var_93 = imagecreatefromjpeg(IA_ROOT . '/addons/ewei_shop/plugin/commission/images/poster.jpg');
				imagecopy($_var_92, $_var_93, 0, 0, 0, 0, 640, 1225);
				imagedestroy($_var_93);
				$_var_94 = preg_replace('/\\/0$/i', '/96', $_var_88['avatar']);
				$_var_95 = $this->createImage($_var_94);
				$_var_96 = imagesx($_var_95);
				$_var_97 = imagesy($_var_95);
				imagecopyresized($_var_92, $_var_95, 24, 32, 0, 0, 88, 88, $_var_96, $_var_97);
				imagedestroy($_var_95);
				$_var_98 = $this->createImage($_var_86['signimg']);
				$_var_96 = imagesx($_var_98);
				$_var_97 = imagesy($_var_98);
				imagecopyresized($_var_92, $_var_98, 0, 160, 0, 0, 640, 640, $_var_96, $_var_97);
				imagedestroy($_var_98);
				$_var_117 = tomedia($this->createMyShopQrcode($_var_88['id']));
				$_var_102 = $this->createImage($_var_117);
				$_var_96 = imagesx($_var_102);
				$_var_97 = imagesy($_var_102);
				imagecopyresized($_var_92, $_var_102, 50, 835, 0, 0, 250, 250, $_var_96, $_var_97);
				imagedestroy($_var_102);
				$_var_107 = '我是';
				imagettftext($_var_92, 20, 0, 150, 70, $_var_103, $_var_91, $_var_107);
				imagettftext($_var_92, 20, 0, 210, 70, $_var_104, $_var_91, $_var_88['nickname']);
				$_var_108 = '我要为';
				imagettftext($_var_92, 20, 0, 150, 105, $_var_103, $_var_91, $_var_108);
				$_var_109 = $_var_86['name'];
				imagettftext($_var_92, 20, 0, 240, 105, $_var_104, $_var_91, $_var_109);
				$_var_110 = imagettfbbox(20, 0, $_var_91, $_var_109);
				$_var_111 = $_var_110[4] - $_var_110[6];
				$_var_112 = '代言';
				imagettftext($_var_92, 20, 0, 240 + $_var_111 + 10, 105, $_var_103, $_var_91, $_var_112);
				imagejpeg($_var_92, $_var_81 . $_var_83);
				imagedestroy($_var_92);
			}
			return $_W['siteroot'] . 'addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/' . $_var_83;
		}

		public function checkAgent()
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$_var_21 = m('user')->getOpenid();
			if (empty($_var_21)) {
				return;
			}
			$_var_23 = m('member')->getMember($_var_21);
			if (empty($_var_23)) {
				return;
			}
			$_var_118 = false;
			$_var_79 = intval($_GPC['mid']);
			if (!empty($_var_79)) {
				$_var_118 = m('member')->getMember($_var_79);
			}
			$_var_119 = !empty($_var_118) && $_var_118['isagent'] == 1 && $_var_118['status'] == 1;
			if ($_var_119) {
				if ($_var_118['openid'] != $_var_21) {
					$_var_120 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_clickcount') . ' where uniacid=:uniacid and openid=:openid and from_openid=:from_openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_21, ':from_openid' => $_var_118['openid']));
					if ($_var_120 <= 0) {
						$_var_121 = array('uniacid' => $_W['uniacid'], 'openid' => $_var_21, 'from_openid' => $_var_118['openid'], 'clicktime' => time());
						pdo_insert('ewei_shop_commission_clickcount', $_var_121);
						pdo_update('ewei_shop_member', array('clickcount' => $_var_118['clickcount'] + 1), array('uniacid' => $_W['uniacid'], 'id' => $_var_118['id']));
					}
				}
			}
			if ($_var_23['isagent'] == 1) {
				return;
			}
			if ($_var_122 == 0) {
				$_var_123 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where id<>:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $_var_23['id']));
				if ($_var_123 <= 0) {
					pdo_update('ewei_shop_member', array('isagent' => 1, 'status' => 1, 'agenttime' => time(), 'agentblack' => 0), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
					return;
				}
			}
			$_var_25 = time();
			$_var_124 = intval($set['become_child']);
			if ($_var_119 && empty($_var_23['agentid'])) {
				if ($_var_23['id'] != $_var_118['id']) {
					if (empty($_var_124)) {
						if (empty($_var_23['fixagentid'])) {
							pdo_update('ewei_shop_member', array('agentid' => $_var_118['id'], 'childtime' => $_var_25), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
							$this->sendMessage($_var_118['openid'], array('nickname' => $_var_23['nickname'], 'childtime' => $_var_25), TM_COMMISSION_AGENT_NEW);
							$this->upgradeLevelByAgent($_var_118['id']);
						}
					} else {
						pdo_update('ewei_shop_member', array('inviter' => $_var_118['id']), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
					}
				}
			}
			$_var_125 = intval($set['become_check']);
			if (empty($set['become'])) {
				if (empty($_var_23['agentblack'])) {
					pdo_update('ewei_shop_member', array('isagent' => 1, 'status' => $_var_125, 'agenttime' => $_var_125 == 1 ? $_var_25 : 0), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
					if ($_var_125 == 1) {
						$this->sendMessage($_var_21, array('nickname' => $_var_23['nickname'], 'agenttime' => $_var_25), TM_COMMISSION_BECOME);
						if ($_var_119) {
							$this->upgradeLevelByAgent($_var_118['id']);
						}
					}
				}
			}
		}

		public function checkOrderConfirm($orderid = '0')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$_var_78 = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($_var_78)) {
				return;
			}
			$_var_21 = $_var_78['openid'];
			$_var_23 = m('member')->getMember($_var_21);
			if (empty($_var_23)) {
				return;
			}
			$_var_124 = intval($set['become_child']);
			$_var_118 = false;
			if (empty($_var_124)) {
				$_var_118 = m('member')->getMember($_var_23['agentid']);
			} else {
				$_var_118 = m('member')->getMember($_var_23['inviter']);
			}
			$_var_119 = !empty($_var_118) && $_var_118['isagent'] == 1 && $_var_118['status'] == 1;
			$_var_25 = time();
			$_var_124 = intval($set['become_child']);
			if ($_var_119) {
				if ($_var_124 == 1) {
					if (empty($_var_23['agentid']) && $_var_23['id'] != $_var_118['id']) {
						if (empty($_var_23['fixagentid'])) {
							$_var_23['agentid'] = $_var_118['id'];
							pdo_update('ewei_shop_member', array('agentid' => $_var_118['id'], 'childtime' => $_var_25), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
							$this->sendMessage($_var_118['openid'], array('nickname' => $_var_23['nickname'], 'childtime' => $_var_25), TM_COMMISSION_AGENT_NEW);
							$this->upgradeLevelByAgent($_var_118['id']);
						}
					}
				}
			}
			$agentid = $_var_23['agentid'];
			if ($_var_23['isagent'] == 1 && $_var_23['status'] == 1) {
				if (!empty($set['selfbuy'])) {
					$agentid = $_var_23['id'];
				}
			}
			if (!empty($agentid)) {
				pdo_update('ewei_shop_order', array('agentid' => $agentid), array('id' => $orderid));
			}
			$this->calculate($orderid);
		}

		public function checkOrderPay($orderid = '0')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$_var_78 = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=1 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($_var_78)) {
				return;
			}
			$_var_21 = $_var_78['openid'];
			$_var_23 = m('member')->getMember($_var_21);
			if (empty($_var_23)) {
				return;
			}
			$_var_124 = intval($set['become_child']);
			$_var_118 = false;
			if (empty($_var_124)) {
				$_var_118 = m('member')->getMember($_var_23['agentid']);
			} else {
				$_var_118 = m('member')->getMember($_var_23['inviter']);
			}
			$_var_119 = !empty($_var_118) && $_var_118['isagent'] == 1 && $_var_118['status'] == 1;
			$_var_25 = time();
			$_var_124 = intval($set['become_child']);
			if ($_var_119) {
				if ($_var_124 == 2) {
					if (empty($_var_23['agentid']) && $_var_23['id'] != $_var_118['id']) {
						if (empty($_var_23['fixagentid'])) {
							$_var_23['agentid'] = $_var_118['id'];
							pdo_update('ewei_shop_member', array('agentid' => $_var_118['id'], 'childtime' => $_var_25), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
							$this->sendMessage($_var_118['openid'], array('nickname' => $_var_23['nickname'], 'childtime' => $_var_25), TM_COMMISSION_AGENT_NEW);
							$this->upgradeLevelByAgent($_var_118['id']);
							if (empty($_var_78['agentid'])) {
								$_var_78['agentid'] = $_var_118['id'];
								pdo_update('ewei_shop_order', array('agentid' => $_var_118['id']), array('id' => $orderid));
								$this->calculate($orderid);
							}
						}
					}
				}
			}
			$_var_126 = $_var_23['isagent'] == 1 && $_var_23['status'] == 1;
			if (!$_var_126) {
				if (intval($set['become']) == 4 && !empty($set['become_goodsid'])) {
					$_var_127 = pdo_fetchall('select goodsid from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid  ', array(':uniacid' => $_W['uniacid'], ':orderid' => $_var_78['id']), 'goodsid');
					if (in_array($set['become_goodsid'], array_keys($_var_127))) {
						if (empty($_var_23['agentblack'])) {
							pdo_update('ewei_shop_member', array('status' => 1, 'isagent' => 1, 'agenttime' => $_var_25), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
							$this->sendMessage($_var_21, array('nickname' => $_var_23['nickname'], 'agenttime' => $_var_25), TM_COMMISSION_BECOME);
							if (!empty($_var_118)) {
								$this->upgradeLevelByAgent($_var_118['id']);
							}
						}
					}
				} else if ($set['become'] == 2 || $set['become'] == 3) {
					if (empty($set['become_order'])) {
						$_var_25 = time();
						if ($set['become'] == 2 || $set['become'] == 3) {
							$_var_128 = true;
							if (!empty($_var_23['agentid'])) {
								$_var_118 = m('member')->getMember($_var_23['agentid']);
								if (empty($_var_118) || $_var_118['isagent'] != 1 || $_var_118['status'] != 1) {
									$_var_128 = false;
								}
							}
							if ($_var_128) {
								$_var_129 = false;
								if ($set['become'] == '2') {
									$_var_30 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status>=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_21));
									$_var_129 = $_var_30 >= intval($set['become_ordercount']);
								} else if ($set['become'] == '3') {
									$_var_130 = pdo_fetchcolumn('select sum(og.realprice) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id  where o.openid=:openid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_21));
									$_var_129 = $_var_130 >= floatval($set['become_moneycount']);
								}
								if ($_var_129) {
									if (empty($_var_23['agentblack'])) {
										$_var_125 = intval($set['become_check']);
										pdo_update('ewei_shop_member', array('status' => $_var_125, 'isagent' => 1, 'agenttime' => $_var_25), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
										if ($_var_125 == 1) {
											$this->sendMessage($_var_21, array('nickname' => $_var_23['nickname'], 'agenttime' => $_var_25), TM_COMMISSION_BECOME);
											if ($_var_128) {
												$this->upgradeLevelByAgent($_var_118['id']);
											}
										}
									}
								}
							}
						}
					}
				}
			}
			if (!empty($_var_23['agentid'])) {
				$_var_118 = m('member')->getMember($_var_23['agentid']);
				if (!empty($_var_118) && $_var_118['isagent'] == 1 && $_var_118['status'] == 1) {
					if ($_var_78['agentid'] == $_var_118['id']) {
						$_var_127 = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $_var_78['id']));
						$goods = '';
						$level = $_var_118['agentlevel'];
						$_var_34 = 0;
						$_var_131 = 0;
						foreach ($_var_127 as $_var_132) {
							$goods .= "" . $_var_132['title'] . '( ';
							if (!empty($_var_132['optiontitle'])) {
								$goods .= ' 规格: ' . $_var_132['optiontitle'];
							}
							$goods .= ' 单价: ' . ($_var_132['realprice'] / $_var_132['total']) . ' 数量: ' . $_var_132['total'] . ' 总价: ' . $_var_132['realprice'] . '); ';
							$_var_59 = iunserializer($_var_132['commission1']);
							$_var_34 += isset($_var_59['level' . $level]) ? $_var_59['level' . $level] : $_var_59['default'];
							$_var_131 += $_var_132['realprice'];
						}
						$this->sendMessage($_var_118['openid'], array('nickname' => $_var_23['nickname'], 'ordersn' => $_var_78['ordersn'], 'price' => $_var_131, 'goods' => $goods, 'commission' => $_var_34, 'paytime' => $_var_78['paytime'],), TM_COMMISSION_ORDER_PAY);
					}
				}
			}
		}

		public function checkOrderFinish($orderid = '')
		{
			global $_W, $_GPC;
			if (empty($orderid)) {
				return;
			}
			$_var_78 = pdo_fetch('select id,openid, ordersn,goodsprice,agentid,finishtime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=3 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($_var_78)) {
				return;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return;
			}
			$_var_21 = $_var_78['openid'];
			$_var_23 = m('member')->getMember($_var_21);
			if (empty($_var_23)) {
				return;
			}
			$_var_25 = time();
			$_var_126 = $_var_23['isagent'] == 1 && $_var_23['status'] == 1;
			if (!$_var_126 && $set['become_order'] == 1) {
				if ($set['become'] == 2 || $set['become'] == 3) {
					$_var_128 = true;
					if (!empty($_var_23['agentid'])) {
						$_var_118 = m('member')->getMember($_var_23['agentid']);
						if (empty($_var_118) || $_var_118['isagent'] != 1 || $_var_118['status'] != 1) {
							$_var_128 = false;
						}
					}
					if ($_var_128) {
						$_var_129 = false;
						if ($set['become'] == '2') {
							$_var_30 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status>=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_21));
							$_var_129 = $_var_30 >= intval($set['become_ordercount']);
						} else if ($set['become'] == '3') {
							$_var_130 = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status>=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_21));
							$_var_129 = $_var_130 >= floatval($set['become_moneycount']);
						}
						if ($_var_129) {
							if (empty($_var_23['agentblack'])) {
								$_var_125 = intval($set['become_check']);
								pdo_update('ewei_shop_member', array('status' => $_var_125, 'isagent' => 1, 'agenttime' => $_var_25), array('uniacid' => $_W['uniacid'], 'id' => $_var_23['id']));
								if ($_var_125 == 1) {
									$this->sendMessage($_var_23['openid'], array('nickname' => $_var_23['nickname'], 'agenttime' => $_var_25), TM_COMMISSION_BECOME);
									if ($_var_128) {
										$this->upgradeLevelByAgent($_var_118['id']);
									}
								}
							}
						}
					}
				}
			}
			if (!empty($_var_23['agentid'])) {
				$_var_118 = m('member')->getMember($_var_23['agentid']);
				if (!empty($_var_118) && $_var_118['isagent'] == 1 && $_var_118['status'] == 1) {
					if ($_var_78['agentid'] == $_var_118['id']) {
						$_var_127 = pdo_fetchall('select g.id,g.title,og.total,og.realprice,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $_var_78['id']));
						$goods = '';
						$level = $_var_118['agentlevel'];
						$_var_34 = 0;
						$_var_131 = 0;
						foreach ($_var_127 as $_var_132) {
							$goods .= "" . $_var_132['title'] . '( ';
							if (!empty($_var_132['optiontitle'])) {
								$goods .= ' 规格: ' . $_var_132['optiontitle'];
							}
							$goods .= ' 单价: ' . ($_var_132['realprice'] / $_var_132['total']) . ' 数量: ' . $_var_132['total'] . ' 总价: ' . $_var_132['realprice'] . '); ';
							$_var_59 = iunserializer($_var_132['commission1']);
							$_var_34 += isset($_var_59['level' . $level]) ? $_var_59['level' . $level] : $_var_59['default'];
							$_var_131 += $_var_132['realprice'];
						}
						$this->sendMessage($_var_118['openid'], array('nickname' => $_var_23['nickname'], 'ordersn' => $_var_78['ordersn'], 'price' => $_var_131, 'goods' => $goods, 'commission' => $_var_34, 'finishtime' => $_var_78['finishtime'],), TM_COMMISSION_ORDER_FINISH);
					}
				}
			}
			$this->upgradeLevelByOrder($_var_21);
		}

		function getShop($_var_133)
		{
			global $_W;
			$_var_23 = m('member')->getMember($_var_133);
			$_var_134 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_shop') . ' where uniacid=:uniacid and mid=:mid limit 1', array(':uniacid' => $_W['uniacid'], ':mid' => $_var_23['id']));
			$_var_135 = m('common')->getSysset(array('shop', 'share'));
			$set = $_var_135['shop'];
			$_var_136 = $_var_135['share'];
			$_var_137 = $_var_136['desc'];
			if (empty($_var_137)) {
				$_var_137 = $set['description'];
			}
			if (empty($_var_137)) {
				$_var_137 = $set['name'];
			}
			$_var_138 = $this->getSet();
			if (empty($_var_134)) {
				$_var_134 = array('name' => $_var_23['nickname'] . '的' . $_var_138['texts']['shop'], 'logo' => $_var_23['avatar'], 'desc' => $_var_137, 'img' => tomedia($set['img']),);
			} else {
				if (empty($_var_134['name'])) {
					$_var_134['name'] = $_var_23['nickname'] . '的' . $_var_138['texts']['shop'];
				}
				if (empty($_var_134['logo'])) {
					$_var_134['logo'] = tomedia($_var_23['avatar']);
				}
				if (empty($_var_134['img'])) {
					$_var_134['img'] = tomedia($set['img']);
				}
				if (empty($_var_134['desc'])) {
					$_var_134['desc'] = $_var_137;
				}
			}
			return $_var_134;
		}

		function getLevels($_var_139 = true)
		{
			global $_W;
			if ($_var_139) {
				return pdo_fetchall('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid order by commission1 asc', array(':uniacid' => $_W['uniacid']));
			} else {
				return pdo_fetchall('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and (ordermoney>0 or commissionmoney>0) order by commission1 asc', array(':uniacid' => $_W['uniacid']));
			}
		}

		function getLevel($_var_21)
		{
			global $_W;
			if (empty($_var_21)) {
				return false;
			}
			$_var_23 = m('member')->getMember($_var_21);
			if (empty($_var_23['agentlevel'])) {
				return false;
			}
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $_var_23['agentlevel']));
			return $level;
		}

		function upgradeLevelByOrder($_var_21)
		{
			global $_W;
			if (empty($_var_21)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$_var_133 = m('member')->getMember($_var_21);
			if (empty($_var_133)) {
				return;
			}
			$_var_140 = intval($set['leveltype']);
			if ($_var_140 == 4 || $_var_140 == 5) {
				if (!empty($_var_133['agentnotupgrade'])) {
					return;
				}
				$_var_141 = $this->getLevel($_var_133['openid']);
				if (empty($_var_141['id'])) {
					$_var_141 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
				}
				$_var_142 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_var_21));
				$_var_31 = $_var_142['ordermoney'];
				$_var_30 = $_var_142['ordercount'];
				if ($_var_140 == 4) {
					$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
					if (empty($_var_143)) {
						return;
					}
					if (!empty($_var_141['id'])) {
						if ($_var_141['id'] == $_var_143['id']) {
							return;
						}
						if ($_var_141['ordermoney'] > $_var_143['ordermoney']) {
							return;
						}
					}
				} else if ($_var_140 == 5) {
					$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
					if (empty($_var_143)) {
						return;
					}
					if (!empty($_var_141['id'])) {
						if ($_var_141['id'] == $_var_143['id']) {
							return;
						}
						if ($_var_141['ordercount'] > $_var_143['ordercount']) {
							return;
						}
					}
				}
				pdo_update('ewei_shop_member', array('agentlevel' => $_var_143['id']), array('id' => $_var_133['id']));
				$this->sendMessage($_var_133['openid'], array('nickname' => $_var_133['nickname'], 'oldlevel' => $_var_141, 'newlevel' => $_var_143,), TM_COMMISSION_UPGRADE);
			} else if ($_var_140 >= 0 && $_var_140 <= 3) {
				$_var_77 = array();
				if (!empty($set['selfbuy'])) {
					$_var_77[] = $_var_133;
				}
				if (!empty($_var_133['agentid'])) {
					$m1 = m('member')->getMember($_var_133['agentid']);
					if (!empty($m1)) {
						$_var_77[] = $m1;
						if (!empty($m1['agentid']) && $m1['isagent'] == 1 && $m1['status'] == 1) {
							$m2 = m('member')->getMember($m1['agentid']);
							if (!empty($m2) && $m2['isagent'] == 1 && $m2['status'] == 1) {
								$_var_77[] = $m2;
								if (empty($set['selfbuy'])) {
									if (!empty($m2['agentid']) && $m2['isagent'] == 1 && $m2['status'] == 1) {
										$m3 = m('member')->getMember($m2['agentid']);
										if (!empty($m3) && $m3['isagent'] == 1 && $m3['status'] == 1) {
											$_var_77[] = $m3;
										}
									}
								}
							}
						}
					}
				}
				if (empty($_var_77)) {
					return;
				}
				foreach ($_var_77 as $_var_144) {
					$_var_145 = $this->getInfo($_var_144['id'], array('ordercount3', 'ordermoney3', 'order13money', 'order13'));
					if (!empty($_var_145['agentnotupgrade'])) {
						continue;
					}
					$_var_141 = $this->getLevel($_var_144['openid']);
					if (empty($_var_141['id'])) {
						$_var_141 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
					}
					if ($_var_140 == 0) {
						$_var_31 = $_var_145['ordermoney3'];
						$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid and {$_var_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($_var_143)) {
							continue;
						}
						if (!empty($_var_141['id'])) {
							if ($_var_141['id'] == $_var_143['id']) {
								continue;
							}
							if ($_var_141['ordermoney'] > $_var_143['ordermoney']) {
								continue;
							}
						}
					} else if ($_var_140 == 1) {
						$_var_31 = $_var_145['order13money'];
						$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid and {$_var_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($_var_143)) {
							continue;
						}
						if (!empty($_var_141['id'])) {
							if ($_var_141['id'] == $_var_143['id']) {
								continue;
							}
							if ($_var_141['ordermoney'] > $_var_143['ordermoney']) {
								continue;
							}
						}
					} else if ($_var_140 == 2) {
						$_var_30 = $_var_145['ordercount3'];
						$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($_var_143)) {
							continue;
						}
						if (!empty($_var_141['id'])) {
							if ($_var_141['id'] == $_var_143['id']) {
								continue;
							}
							if ($_var_141['ordercount'] > $_var_143['ordercount']) {
								continue;
							}
						}
					} else if ($_var_140 == 3) {
						$_var_30 = $_var_145['order13'];
						$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
						if (empty($_var_143)) {
							continue;
						}
						if (!empty($_var_141['id'])) {
							if ($_var_141['id'] == $_var_143['id']) {
								continue;
							}
							if ($_var_141['ordercount'] > $_var_143['ordercount']) {
								continue;
							}
						}
					}
					pdo_update('ewei_shop_member', array('agentlevel' => $_var_143['id']), array('id' => $_var_144['id']));
					$this->sendMessage($_var_144['openid'], array('nickname' => $_var_144['nickname'], 'oldlevel' => $_var_141, 'newlevel' => $_var_143,), TM_COMMISSION_UPGRADE);
				}
			}
		}

		function upgradeLevelByAgent($_var_21)
		{
			global $_W;
			if (empty($_var_21)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$_var_133 = m('member')->getMember($_var_21);
			if (empty($_var_133)) {
				return;
			}
			$_var_140 = intval($set['leveltype']);
			if ($_var_140 < 6 || $_var_140 > 9) {
				return;
			}
			$_var_145 = $this->getInfo($_var_133['id'], array());
			if ($_var_140 == 6 || $_var_140 == 8) {
				$_var_77 = array($_var_133);
				if (!empty($_var_133['agentid'])) {
					$m1 = m('member')->getMember($_var_133['agentid']);
					if (!empty($m1)) {
						$_var_77[] = $m1;
						if (!empty($m1['agentid']) && $m1['isagent'] == 1 && $m1['status'] == 1) {
							$m2 = m('member')->getMember($m1['agentid']);
							if (!empty($m2) && $m2['isagent'] == 1 && $m2['status'] == 1) {
								$_var_77[] = $m2;
							}
						}
					}
				}
				if (empty($_var_77)) {
					return;
				}
				foreach ($_var_77 as $_var_144) {
					$_var_145 = $this->getInfo($_var_144['id'], array());
					if (!empty($_var_145['agentnotupgrade'])) {
						continue;
					}
					$_var_141 = $this->getLevel($_var_144['openid']);
					if (empty($_var_141['id'])) {
						$_var_141 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
					}
					if ($_var_140 == 6) {
						$_var_146 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $_var_133['id'], ':uniacid' => $_W['uniacid']), 'id');
						$_var_147 += count($_var_146);
						if (!empty($_var_146)) {
							$_var_148 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($_var_146)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
							$_var_147 += count($_var_148);
							if (!empty($_var_148)) {
								$_var_149 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($_var_148)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
								$_var_147 += count($_var_149);
							}
						}
						$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
					} else if ($_var_140 == 8) {
						$_var_147 = $_var_145['level1'] + $_var_145['level2'] + $_var_145['level3'];
						$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
					}
					if (empty($_var_143)) {
						continue;
					}
					if ($_var_143['id'] == $_var_141['id']) {
						continue;
					}
					if (!empty($_var_141['id'])) {
						if ($_var_141['downcount'] > $_var_143['downcount']) {
							continue;
						}
					}
					pdo_update('ewei_shop_member', array('agentlevel' => $_var_143['id']), array('id' => $_var_144['id']));
					$this->sendMessage($_var_144['openid'], array('nickname' => $_var_144['nickname'], 'oldlevel' => $_var_141, 'newlevel' => $_var_143,), TM_COMMISSION_UPGRADE);
				}
			} else {
				if (!empty($_var_133['agentnotupgrade'])) {
					return;
				}
				$_var_141 = $this->getLevel($_var_133['openid']);
				if (empty($_var_141['id'])) {
					$_var_141 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
				}
				if ($_var_140 == 7) {
					$_var_147 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $_var_133['id'], ':uniacid' => $_W['uniacid']));
					$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
				} else if ($_var_140 == 9) {
					$_var_147 = $_var_145['level1'];
					$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
				}
				if (empty($_var_143)) {
					return;
				}
				if ($_var_143['id'] == $_var_141['id']) {
					return;
				}
				if (!empty($_var_141['id'])) {
					if ($_var_141['downcount'] > $_var_143['downcount']) {
						return;
					}
				}
				pdo_update('ewei_shop_member', array('agentlevel' => $_var_143['id']), array('id' => $_var_133['id']));
				$this->sendMessage($_var_133['openid'], array('nickname' => $_var_133['nickname'], 'oldlevel' => $_var_141, 'newlevel' => $_var_143,), TM_COMMISSION_UPGRADE);
			}
		}

		function upgradeLevelByCommissionOK($_var_21)
		{
			global $_W;
			if (empty($_var_21)) {
				return false;
			}
			$set = $this->getSet();
			if (empty($set['level'])) {
				return false;
			}
			$_var_133 = m('member')->getMember($_var_21);
			if (empty($_var_133)) {
				return;
			}
			$_var_140 = intval($set['leveltype']);
			if ($_var_140 != 10) {
				return;
			}
			if (!empty($_var_133['agentnotupgrade'])) {
				return;
			}
			$_var_141 = $this->getLevel($_var_133['openid']);
			if (empty($_var_141['id'])) {
				$_var_141 = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
			}
			$_var_145 = $this->getInfo($_var_133['id'], array('pay'));
			$_var_150 = $_var_145['commission_pay'];
			$_var_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$_var_150} >= commissionmoney and commissionmoney>0  order by commissionmoney desc limit 1", array(':uniacid' => $_W['uniacid']));
			if (empty($_var_143)) {
				return;
			}
			if ($_var_141['id'] == $_var_143['id']) {
				return;
			}
			if (!empty($_var_141['id'])) {
				if ($_var_141['commissionmoney'] > $_var_143['commissionmoney']) {
					return;
				}
			}
			pdo_update('ewei_shop_member', array('agentlevel' => $_var_143['id']), array('id' => $_var_133['id']));
			$this->sendMessage($_var_133['openid'], array('nickname' => $_var_133['nickname'], 'oldlevel' => $_var_141, 'newlevel' => $_var_143,), TM_COMMISSION_UPGRADE);
		}

		function sendMessage($_var_21 = '', $_var_151 = array(), $_var_152 = '')
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			$_var_153 = $set['tm'];
			$_var_154 = $_var_153['templateid'];
			$_var_23 = m('member')->getMember($_var_21);
			$_var_155 = unserialize($_var_23['noticeset']);
			if (!is_array($_var_155)) {
				$_var_155 = array();
			}
			if ($_var_152 == TM_COMMISSION_AGENT_NEW && !empty($_var_153['commission_agent_new']) && empty($_var_155['commission_agent_new'])) {
				$_var_156 = $_var_153['commission_agent_new'];
				$_var_156 = str_replace('[昵称]', $_var_151['nickname'], $_var_156);
				$_var_156 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_151['childtime']), $_var_156);
				$_var_157 = array('keyword1' => array('value' => !empty($_var_153['commission_agent_newtitle']) ? $_var_153['commission_agent_newtitle'] : '新增下线通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_156, 'color' => '#73a68d'));
				if (!empty($_var_154)) {
					m('message')->sendTplNotice($_var_21, $_var_154, $_var_157);
				} else {
					m('message')->sendCustomNotice($_var_21, $_var_157);
				}
			} else if ($_var_152 == TM_COMMISSION_ORDER_PAY && !empty($_var_153['commission_order_pay']) && empty($_var_155['commission_order_pay'])) {
				$_var_156 = $_var_153['commission_order_pay'];
				$_var_156 = str_replace('[昵称]', $_var_151['nickname'], $_var_156);
				$_var_156 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_151['paytime']), $_var_156);
				$_var_156 = str_replace('[订单编号]', $_var_151['ordersn'], $_var_156);
				$_var_156 = str_replace('[订单金额]', $_var_151['price'], $_var_156);
				$_var_156 = str_replace('[佣金金额]', $_var_151['commission'], $_var_156);
				$_var_156 = str_replace('[商品详情]', $_var_151['goods'], $_var_156);
				$_var_157 = array('keyword1' => array('value' => !empty($_var_153['commission_order_paytitle']) ? $_var_153['commission_order_paytitle'] : '下线付款通知'), 'keyword2' => array('value' => $_var_156));
				if (!empty($_var_154)) {
					m('message')->sendTplNotice($_var_21, $_var_154, $_var_157);
				} else {
					m('message')->sendCustomNotice($_var_21, $_var_157);
				}
			} else if ($_var_152 == TM_COMMISSION_ORDER_FINISH && !empty($_var_153['commission_order_finish']) && empty($_var_155['commission_order_finish'])) {
				$_var_156 = $_var_153['commission_order_finish'];
				$_var_156 = str_replace('[昵称]', $_var_151['nickname'], $_var_156);
				$_var_156 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_151['finishtime']), $_var_156);
				$_var_156 = str_replace('[订单编号]', $_var_151['ordersn'], $_var_156);
				$_var_156 = str_replace('[订单金额]', $_var_151['price'], $_var_156);
				$_var_156 = str_replace('[佣金金额]', $_var_151['commission'], $_var_156);
				$_var_156 = str_replace('[商品详情]', $_var_151['goods'], $_var_156);
				$_var_157 = array('keyword1' => array('value' => !empty($_var_153['commission_order_finishtitle']) ? $_var_153['commission_order_finishtitle'] : '下线确认收货通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_156, 'color' => '#73a68d'));
				if (!empty($_var_154)) {
					m('message')->sendTplNotice($_var_21, $_var_154, $_var_157);
				} else {
					m('message')->sendCustomNotice($_var_21, $_var_157);
				}
			} else if ($_var_152 == TM_COMMISSION_APPLY && !empty($_var_153['commission_apply']) && empty($_var_155['commission_apply'])) {
				$_var_156 = $_var_153['commission_apply'];
				$_var_156 = str_replace('[昵称]', $_var_23['nickname'], $_var_156);
				$_var_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_156);
				$_var_156 = str_replace('[金额]', $_var_151['commission'], $_var_156);
				$_var_156 = str_replace('[提现方式]', $_var_151['type'], $_var_156);
				$_var_157 = array('keyword1' => array('value' => !empty($_var_153['commission_applytitle']) ? $_var_153['commission_applytitle'] : '提现申请提交成功', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_156, 'color' => '#73a68d'));
				if (!empty($_var_154)) {
					m('message')->sendTplNotice($_var_21, $_var_154, $_var_157);
				} else {
					m('message')->sendCustomNotice($_var_21, $_var_157);
				}
			} else if ($_var_152 == TM_COMMISSION_CHECK && !empty($_var_153['commission_check']) && empty($_var_155['commission_check'])) {
				$_var_156 = $_var_153['commission_check'];
				$_var_156 = str_replace('[昵称]', $_var_23['nickname'], $_var_156);
				$_var_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_156);
				$_var_156 = str_replace('[金额]', $_var_151['commission'], $_var_156);
				$_var_156 = str_replace('[提现方式]', $_var_151['type'], $_var_156);
				$_var_157 = array('keyword1' => array('value' => !empty($_var_153['commission_checktitle']) ? $_var_153['commission_checktitle'] : '提现申请审核处理完成', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_156, 'color' => '#73a68d'));
				if (!empty($_var_154)) {
					m('message')->sendTplNotice($_var_21, $_var_154, $_var_157);
				} else {
					m('message')->sendCustomNotice($_var_21, $_var_157);
				}
			} else if ($_var_152 == TM_COMMISSION_PAY && !empty($_var_153['commission_pay']) && empty($_var_155['commission_pay'])) {
				$_var_156 = $_var_153['commission_pay'];
				$_var_156 = str_replace('[昵称]', $_var_23['nickname'], $_var_156);
				$_var_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_156);
				$_var_156 = str_replace('[金额]', $_var_151['commission'], $_var_156);
				$_var_156 = str_replace('[提现方式]', $_var_151['type'], $_var_156);
				$_var_157 = array('keyword1' => array('value' => !empty($_var_153['commission_paytitle']) ? $_var_153['commission_paytitle'] : '佣金打款通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_156, 'color' => '#73a68d'));
				if (!empty($_var_154)) {
					m('message')->sendTplNotice($_var_21, $_var_154, $_var_157);
				} else {
					m('message')->sendCustomNotice($_var_21, $_var_157);
				}
			} else if ($_var_152 == TM_COMMISSION_UPGRADE && !empty($_var_153['commission_upgrade']) && empty($_var_155['commission_upgrade'])) {
				$_var_156 = $_var_153['commission_upgrade'];
				$_var_156 = str_replace('[昵称]', $_var_23['nickname'], $_var_156);
				$_var_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $_var_156);
				$_var_156 = str_replace('[旧等级]', $_var_151['oldlevel']['levelname'], $_var_156);
				$_var_156 = str_replace('[旧一级分销比例]', $_var_151['oldlevel']['commission1'] . '%', $_var_156);
				$_var_156 = str_replace('[旧二级分销比例]', $_var_151['oldlevel']['commission2'] . '%', $_var_156);
				$_var_156 = str_replace('[旧三级分销比例]', $_var_151['oldlevel']['commission3'] . '%', $_var_156);
				$_var_156 = str_replace('[新等级]', $_var_151['newlevel']['levelname'], $_var_156);
				$_var_156 = str_replace('[新一级分销比例]', $_var_151['newlevel']['commission1'] . '%', $_var_156);
				$_var_156 = str_replace('[新二级分销比例]', $_var_151['newlevel']['commission2'] . '%', $_var_156);
				$_var_156 = str_replace('[新三级分销比例]', $_var_151['newlevel']['commission3'] . '%', $_var_156);
				$_var_157 = array('keyword1' => array('value' => !empty($_var_153['commission_upgradetitle']) ? $_var_153['commission_upgradetitle'] : '分销等级升级通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_156, 'color' => '#73a68d'));
				if (!empty($_var_154)) {
					m('message')->sendTplNotice($_var_21, $_var_154, $_var_157);
				} else {
					m('message')->sendCustomNotice($_var_21, $_var_157);
				}
			} else if ($_var_152 == TM_COMMISSION_BECOME && !empty($_var_153['commission_become']) && empty($_var_155['commission_become'])) {
				$_var_156 = $_var_153['commission_become'];
				$_var_156 = str_replace('[昵称]', $_var_151['nickname'], $_var_156);
				$_var_156 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_151['agenttime']), $_var_156);
				$_var_157 = array('keyword1' => array('value' => !empty($_var_153['commission_becometitle']) ? $_var_153['commission_becometitle'] : '成为分销商通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_156, 'color' => '#73a68d'));
				if (!empty($_var_154)) {
					m('message')->sendTplNotice($_var_21, $_var_154, $_var_157);
				} else {
					m('message')->sendCustomNotice($_var_21, $_var_157);
				}
			}
		}

		function perms()
		{
			return array('commission' => array('text' => $this->getName(), 'isplugin' => true, 'child' => array('cover' => array('text' => '入口设置'), 'agent' => array('text' => '分销商', 'view' => '浏览', 'check' => '审核-log', 'edit' => '修改-log', 'agentblack' => '黑名单操作-log', 'delete' => '删除-log', 'user' => '查看下线', 'order' => '查看推广订单(还需有订单权限)', 'changeagent' => '设置分销商'), 'level' => array('text' => '分销商等级', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'apply' => array('text' => '佣金审核', 'view1' => '浏览待审核', 'view2' => '浏览已审核', 'view3' => '浏览已打款', 'view_1' => '浏览无效', 'export1' => '导出待审核-log', 'export2' => '导出已审核-log', 'export3' => '导出已打款-log', 'export_1' => '导出无效-log', 'check' => '审核-log', 'pay' => '打款-log', 'cancel' => '重新审核-log'), 'notice' => array('text' => '通知设置-log'), 'increase' => array('text' => '分销商趋势图'), 'changecommission' => array('text' => '修改佣金-log'), 'set' => array('text' => '基础设置-log'))));
		}
	}
}