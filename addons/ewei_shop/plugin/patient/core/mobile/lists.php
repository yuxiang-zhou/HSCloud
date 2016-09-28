<?php


defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid, true);
$shop = m('common')->getSysset('shop');
$uniacid = $_W['uniacid'];
$cateid = intval($_GPC['cate']);
$cate = pdo_fetch('select id,name from ' . tablename('ewei_shop_patient_depart') . ' where id=:id and uniacid=:uniacid limit 1', array(
    ':id' => $cateid,
    ':uniacid' => $uniacid
));
if ($_W['isajax']) {
    if ($operation == 'display') {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition = ' and uniacid = :uniacid and status=1 and deleted=0';
        $params = array(
            ':uniacid' => $_W['uniacid']
        );
        $sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_patient_doctor') . " where 1 {$condition}";
        $total = pdo_fetchcolumn($sql, $params);
        if (!empty($cate)) {
            $condition .= " and cate=" . $cateid;
        }
        $list = array();
        if (!empty($total)) {
            $sql = 'SELECT id,title,type,thumb,technicaltitle,skill FROM ' . tablename('ewei_shop_patient_doctor') . '  where 1 ' . $condition . ' ORDER BY createtime DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $params);
            $list = set_medias($list, 'thumb');
            foreach ($list as &$row) {
                if ($row['credit'] > 0 & $row['money'] > 0) {
                    $row['acttype'] = 0;
                } else if ($row['credit'] > 0) {
                    $row['acttype'] = 1;
                } else if ($goods['money'] > 0) {
                    $row['acttype'] = 2;
                }
            }
            unset($row);
        }
        show_json(1, array(
            'total' => $total,
            'list' => $list,
            'pagesize' => $psize
        ));
    }
}

include $this->template('lists');