<?php
global $_W;
load()->func('db');
load()->model('setting');
load()->func('communication');

$data = array();
$data['ip'] = gethostbyname($_SERVER['SERVER_ADDR']);
$data['domain'] = $_SERVER['HTTP_HOST'];
$setting = setting_load('site');
$data['id'] =isset($setting['site']['key'])? $setting['site']['key'] : '1';
$data['module']= 'ewei_shopv2';
	
$url = 'http://v3.mitusky.com/meepo/api/meepo.php';
$res = ihttp_post($url,$data);
$res = cloud_object_array($res);
$content = $res['content'];
$content = json_decode($content);
$content = cloud_object_array($content);
	
$setting = $content['setting'];

if(!empty($setting) && $setting['status'] == 1){
	if(pdo_tableexists('ewei_shopv2_mitu_setting')){
		$sql = "SELECT * FROM ".tablename('ewei_shopv2_mitu_setting')." WHERE code = :code";
		$params = array(':code'=>'auth');
		$auth = pdo_fetch($sql,$params);
		$data = array();
		$data['code'] = 'auth';
		$data['value'] = iserializer($setting);
	
		if(empty($auth)){
			pdo_insert('ewei_shopv2_mitu_setting',$data);
		}else{
			pdo_update('ewei_shopv2_mitu_setting',$data,array('id'=>$auth['id']));
		}
	}
	message('验证授权成功',referer(),success);
}else{
	message('验证授权失败，请联系客服处理,客服QQ:229364369','','error');
}

/*
 * 结构转数组
 * */
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

