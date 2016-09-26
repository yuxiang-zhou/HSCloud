<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/slide', TEMPLATE_INCLUDEPATH)) : (include template('common/slide', TEMPLATE_INCLUDEPATH));?>
<style>
	body{
	font:<?php  echo $_W['styles']['fontsize'];?> <?php  echo $_W['styles']['fontfamily'];?>;
	color:<?php  echo $_W['styles']['fontcolor'];?>;
	padding:0;
	margin:0;
	background-image:url('<?php  if(empty($_W['styles']['indexbgimg'])) { ?>./themes/style18/images/bg_index.jpg<?php  } else { ?><?php  echo $_W['styles']['indexbgimg'];?><?php  } ?>');background-size:cover;
	background-size:cover;
	background-color:<?php  if(empty($_W['styles']['indexbgcolor'])) { ?>#370f05<?php  } else { ?><?php  echo $_W['styles']['indexbgcolor'];?><?php  } ?>;
	<?php  echo $_W['styles']['indexbgextra'];?>
	}
	a{color:<?php  echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
	<?php  echo $_W['styles']['css'];?>
	.box{width:98%; overflow:hidden; margin: 4.5% 0 0 3%;}
	.box .box-item{float:left; text-align:center; display:block; text-decoration:none; outline:none;width:30.9%; margin:0 2% 5% 0; height:90px; position:relative; color:#ffffff; overflow:hidden;}
	.box .box-item i{display:block; width:51px; height:51px; line-height:51px; font-size:35px; color:#FFF; overflow:hidden; margin:12px auto 2px auto;}
	.box .box-item img{display:inline-block; height:65px; margin:0 auto;}
	.box .box-item span{color:<?php  echo $_W['styles']['fontnavcolor'];?>;display:block; font-size:14px; width:100%; text-align:center; overflow:hidden; height:25px; line-height:25px;}
	.box .box-item.pic i,.box .box-item.pic span{color:#444;}
	.footer{color:#ffffff;}
</style>
<div class="box clearfix">
	<?php  $num = 0;?>
	<?php  if(is_array($navs)) { foreach($navs as $nav) { ?>
	<?php  if($num == 0) $bg = '#1d8090';?>
	<?php  if($num == 1) $bg = '#4481d3';?>
	<?php  if($num == 2) $bg = '#911b97';?>
	<?php  if($num == 3) $bg = '#1d921e';?>
	<?php  if($num == 4) $bg = '#c46446';?>
	<?php  if($num == 5) $bg = '#a43553';?>
	<?php  if($num == 6) $bg = '#8134a8';?>
	<?php  if($num == 7) $bg = '#2559a3';?>
	<?php  if($num == 8) $bg = '#e4e2e3';?>
	<a href="<?php  echo $nav['url'];?>" class="box-item <?php  if($num == 8) { ?>pic<?php  } else { ?>icon<?php  } ?>" style="background:<?php  echo $bg;?>;">
		<?php  if(!empty($nav['icon'])) { ?>
		<img src="<?php  echo $_W['attachurl'];?><?php  echo $nav['icon'];?>">
		<?php  } else { ?>
		<i class="fa <?php  echo $nav['css']['icon']['icon'];?>" style="<?php  echo $nav['css']['icon']['style'];?>"></i>
		<?php  } ?>
		<span style="<?php  echo $nav['css']['name'];?>" title="<?php  echo $nav['name'];?>"><?php  echo $nav['name'];?></span>
	</a>
	<?php  $num++; if($num > 8) $num = 0;?>
	<?php  } } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>