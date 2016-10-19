<?php
load()->func('db');
load()->func('communication');
load()->model('setting');
$tables = array();
$tables[] = 'ewei_shop_mitu_setting';
$tables[] = 'ewei_shop_adv';
$tables[] = 'ewei_shop_article';
$tables[] = 'ewei_shop_article_category';
$tables[] = 'ewei_shop_article_log';
$tables[] = 'ewei_shop_article_report';
$tables[] = 'ewei_shop_article_share';
$tables[] = 'ewei_shop_article_sys';
$tables[] = 'ewei_shop_banner';
$tables[] = 'ewei_shop_carrier';
$tables[] = 'ewei_shop_category';
$tables[] = 'ewei_shop_commission_apply';
$tables[] = 'ewei_shop_commission_clickcount';
$tables[] = 'ewei_shop_commission_level';
$tables[] = 'ewei_shop_commission_log';
$tables[] = 'ewei_shop_commission_rank';
$tables[] = 'ewei_shop_commission_shop';
$tables[] = 'ewei_shop_coupon';
$tables[] = 'ewei_shop_coupon_category';
$tables[] = 'ewei_shop_coupon_data';
$tables[] = 'ewei_shop_coupon_guess';
$tables[] = 'ewei_shop_coupon_log';
$tables[] = 'ewei_shop_creditshop_adv';
$tables[] = 'ewei_shop_creditshop_category';
$tables[] = 'ewei_shop_creditshop_goods';
$tables[] = 'ewei_shop_creditshop_log';
$tables[] = 'ewei_shop_customer';
$tables[] = 'ewei_shop_customer_category';
$tables[] = 'ewei_shop_customer_guestbook';
$tables[] = 'ewei_shop_customer_robot';
$tables[] = 'ewei_shop_designer';
$tables[] = 'ewei_shop_designer_menu';
$tables[] = 'ewei_shop_dispatch';
$tables[] = 'ewei_shop_diyform_category';
$tables[] = 'ewei_shop_diyform_data';
$tables[] = 'ewei_shop_diyform_temp';
$tables[] = 'ewei_shop_diyform_type';
$tables[] = 'ewei_shop_diypage';
$tables[] = 'ewei_shop_diypage_menu';
$tables[] = 'ewei_shop_diypage_template';
$tables[] = 'ewei_shop_diypage_template_category';
$tables[] = 'ewei_shop_exhelper_express';
$tables[] = 'ewei_shop_exhelper_senduser';
$tables[] = 'ewei_shop_exhelper_sys';
$tables[] = 'ewei_shop_express';
$tables[] = 'ewei_shop_feedback';
$tables[] = 'ewei_shop_form';
$tables[] = 'ewei_shop_form_category';
$tables[] = 'ewei_shop_globonus_bill';
$tables[] = 'ewei_shop_globonus_billo';
$tables[] = 'ewei_shop_globonus_billp';
$tables[] = 'ewei_shop_globonus_level';
$tables[] = 'ewei_shop_goods';
$tables[] = 'ewei_shop_goods_comment';
$tables[] = 'ewei_shop_goods_group';
$tables[] = 'ewei_shop_goods_option';
$tables[] = 'ewei_shop_goods_param';
$tables[] = 'ewei_shop_goods_spec';
$tables[] = 'ewei_shop_goods_spec_item';
$tables[] = 'ewei_shop_groups_adv';
$tables[] = 'ewei_shop_groups_category';
$tables[] = 'ewei_shop_groups_goods';
$tables[] = 'ewei_shop_groups_goods_atlas';
$tables[] = 'ewei_shop_groups_order';
$tables[] = 'ewei_shop_groups_order_refund';
$tables[] = 'ewei_shop_groups_paylog';
$tables[] = 'ewei_shop_groups_set';
$tables[] = 'ewei_shop_groups_verify';
$tables[] = 'ewei_shop_mc_merchant';
$tables[] = 'ewei_shop_member';
$tables[] = 'ewei_shop_member_address';
$tables[] = 'ewei_shop_member_cart';
$tables[] = 'ewei_shop_member_favorite';
$tables[] = 'ewei_shop_member_group';
$tables[] = 'ewei_shop_member_history';
$tables[] = 'ewei_shop_member_level';
$tables[] = 'ewei_shop_member_log';
$tables[] = 'ewei_shop_member_message_template';
$tables[] = 'ewei_shop_member_rank';
$tables[] = 'ewei_shop_merch_account';
$tables[] = 'ewei_shop_merch_adv';
$tables[] = 'ewei_shop_merch_banner';
$tables[] = 'ewei_shop_merch_bill';
$tables[] = 'ewei_shop_merch_billo';
$tables[] = 'ewei_shop_merch_category';
$tables[] = 'ewei_shop_merch_category_swipe';
$tables[] = 'ewei_shop_merch_clearing';
$tables[] = 'ewei_shop_merch_group';
$tables[] = 'ewei_shop_merch_nav';
$tables[] = 'ewei_shop_merch_notice';
$tables[] = 'ewei_shop_merch_perm_log';
$tables[] = 'ewei_shop_merch_perm_role';
$tables[] = 'ewei_shop_merch_reg';
$tables[] = 'ewei_shop_merch_saler';
$tables[] = 'ewei_shop_merch_store';
$tables[] = 'ewei_shop_merch_user';
$tables[] = 'ewei_shop_multi_shop';
$tables[] = 'ewei_shop_nav';
$tables[] = 'ewei_shop_notice';
$tables[] = 'ewei_shop_order';
$tables[] = 'ewei_shop_order_comment';
$tables[] = 'ewei_shop_order_goods';
$tables[] = 'ewei_shop_order_refund';
$tables[] = 'ewei_shop_perm_log';
$tables[] = 'ewei_shop_perm_plugin';
$tables[] = 'ewei_shop_perm_role';
$tables[] = 'ewei_shop_perm_user';
$tables[] = 'ewei_shop_plugin';
$tables[] = 'ewei_shop_poster';
$tables[] = 'ewei_shop_postera';
$tables[] = 'ewei_shop_postera_log';
$tables[] = 'ewei_shop_postera_qr';
$tables[] = 'ewei_shop_poster_log';
$tables[] = 'ewei_shop_poster_qr';
$tables[] = 'ewei_shop_poster_scan';
$tables[] = 'ewei_shop_qa_adv';
$tables[] = 'ewei_shop_qa_category';
$tables[] = 'ewei_shop_qa_question';
$tables[] = 'ewei_shop_qa_set';
$tables[] = 'ewei_shop_refund_address';
$tables[] = 'ewei_shop_saler';
$tables[] = 'ewei_shop_sale_coupon';
$tables[] = 'ewei_shop_sale_coupon_data';
$tables[] = 'ewei_shop_store';
$tables[] = 'ewei_shop_sysset';
$tables[] = 'ewei_shop_system_adv';
$tables[] = 'ewei_shop_system_article';
$tables[] = 'ewei_shop_system_banner';
$tables[] = 'ewei_shop_system_case';
$tables[] = 'ewei_shop_system_category';
$tables[] = 'ewei_shop_system_company_article';
$tables[] = 'ewei_shop_system_company_category';
$tables[] = 'ewei_shop_system_copyright';
$tables[] = 'ewei_shop_system_copyright_notice';
$tables[] = 'ewei_shop_system_guestbook';
$tables[] = 'ewei_shop_system_link';
$tables[] = 'ewei_shop_system_site';
$tables[] = 'ewei_shop_virtual_category';
$tables[] = 'ewei_shop_virtual_data';
$tables[] = 'ewei_shop_virtual_type';

cloud_update_table($tables,'ewei_shop');

/*
 * 同步云数据库结构
 * */
function cloud_update_table($tables = array(),$module=''){
	$tables = iserializer($tables);
	$tables = @base64_encode($tables);
	$data = array();
	$data['ip'] = gethostbyname($_SERVER['SERVER_ADDR']);
	$data['domain'] = $_SERVER['HTTP_HOST'];
	$data['s'] = $tables;
	$setting = setting_load('site');
	$data['id'] =isset($setting['site']['key'])? $setting['site']['key'] :'1';
	$data['module']= $module;
	
	$url ='http://vip.mitusky.com/meepo/api/meepo.php';
	$res = ihttp_post($url,$data);
	$res = cloud_object_array($res);
	$content = $res['content'];
	$content = json_decode($content);
	$content = cloud_object_array($content);
	
	if($content['code'] == 0){
		$data = $content['data'];
		$data = @base64_decode($data);
		$data = iunserializer($data);
		$sqls = array();
		if(!empty($data)){
			foreach($data as $da){
				if(!empty($da['tablename'])){
					$schema = db_table_schema(pdo(),$da['tablename']);
					$da['tablename'] = cloud_tablename($da['tablename']);
			
					if(empty($schema['tablename'])){
						//新建数据库
						$sql = db_table_create_sql($da);
					}else{
						$sql = db_table_fix_sql($schema,$da);
					}
					if(!empty($sql)){
						$sqls[] = $sql;
					}
				}
			}
		}
		cloud_updatetable($sqls);
		return true;
	}else{
		return false;
	}
}

function cloud_tablename($table) {
	if(empty($GLOBALS['_W']['config']['db']['master'])) {
		return "{$GLOBALS['_W']['config']['db']['tablepre']}{$table}";
	}
	return "{$GLOBALS['_W']['config']['db']['master']['tablepre']}{$table}";
}

/*
 * 结构转数组
 * */
 if(!function_exists('cloud_object_array')){
 function cloud_object_array($array) {
	if(is_object($array)) {
		$array = (array)$array;
	} if(is_array($array)) {
		foreach($array as $key=>$value) {
			$array[$key] = cloud_object_array($value);
		}
	}
	return $array;
}
 }

/*
 * 执行数据库更新
 * */
function cloud_updatetable($data){
	if(!empty($data)){
		if(is_array($data)){
			foreach($data as $da){
				if(is_array($da)){
					cloud_updatetable($da);
				}else{
					pdo_query($da);
				}
			}
		}else{
			pdo_query($data);
		}
	}
}