<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
	body{
		padding:0;
		margin:0;
		min-height:480px;
		background-image:url('<?php  if(empty($_W['styles']['indexbgimg'])) { ?>./themes/style21/images/bg_index.jpg<?php  } else { ?><?php  echo $_W['styles']['indexbgimg'];?><?php  } ?>');
		background-size:cover;
		color:<?php  echo $_W['styles']['fontnavcolor'];?>;
		background-color:<?php  echo $_W['styles']['indexbgcolor'];?>;<?php  echo $_W['styles']['indexbgextra'];?>;
		<?php  echo $_W['styles']['indexbgextra'];?>
	}
	a{color:<?php  echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
	<?php  echo $_W['styles']['css'];?>
	.box{width:100%; overflow:hidden; margin-top:10px;}
	.box .box-item{display:block; width:40%; color:#333; height:3.3em; line-height:3.3em; padding-left:15.4%; padding-right:10px; overflow:hidden; border-top-right-radius:0.4em; border-bottom-right-radius:0.4em; box-shadow:0 0 0.3em rgba(0,0,0,0.17); background-color:#f5f5f5; margin-bottom:1.8%; position:relative; }
	.box .box-item:nth-child(1){width:35.7%}
	.box .box-item:nth-child(2){width:38.5%;}
	.box .box-item:nth-child(3){width:40.5%}
	.box .box-item:nth-child(4){width:45.9%}
	.box .box-item:nth-child(5){width:43.3%}
	.box .box-item:hover{background-color:#e98283; color:#fff;}
	.box .box-item span{color:<?php  echo $_W['styles']['fontnavcolor'];?>; display:inline-block; width:95%; vertical-align:middle; overflow:hidden;}
	.box .box-item i{display:block; width:35px; height:35px; font-size:35px; line-height:35px; position:absolute; left:10px; margin-right:10px;  background-size:cover; top:5px;}
</style>
<div class="box clearfix">
	<?php  $site_navs = modulefunc('site', 'site_navs', array (
  'func' => 'site_navs',
  'item' => 'row',
  'limit' => 10,
  'index' => 'iteration',
  'multiid' => 0,
  'uniacid' => 2,
  'acid' => 0,
)); if(is_array($site_navs)) { $i=0; foreach($site_navs as $i => $row) { $i++; $row['iteration'] = $i; ?>
	<?php  echo $row['html'];?>
	<?php  } } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>