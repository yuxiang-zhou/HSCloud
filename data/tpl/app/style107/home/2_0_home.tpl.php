<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('slide', TEMPLATE_INCLUDEPATH)) : (include template('slide', TEMPLATE_INCLUDEPATH));?>
<link rel="stylesheet" type="text/css" href="./themes/style107/style/reset.css" media="all" />
<link type="text/css" rel="stylesheet" href="./themes/style107/style/list.css" />
</head>
<body>

<div class="mainshowtop">
<div class="wz_17_wrapper">

<?php  if(is_array($navs)) { foreach($navs as $key => $nav) { ?>
	<?php  $key = $key+1;?>
	<?php  if($key % 2 != 0) { ?>
        <div class="wz_17_line add_qq_more">
             <a href="<?php  echo $nav['url'];?>">
                 <div class="wz_17_text wz_17_color_0<? echo $key;?> wz_17_flat_left m_text"><?php  echo $nav['name'];?></div>
                 <div class="wz_17_img "><img src="<?php  echo $_W['attachurl'];?><?php  echo $nav['icon'];?>" class="m_pic"></div>
             </a>
             <div class="clfl"></div>
        </div>
	<?php  } else { ?>
	<div class="wz_17_line add_qq_more">
             <a href="<?php  echo $nav['url'];?>">
                 <div class="wz_17_text wz_17_color_0<? echo $key;?> wz_17_flat_right m_text"><?php  echo $nav['name'];?></div>
                 <div class="wz_17_img "><img src="<?php  echo $_W['attachurl'];?><?php  echo $nav['icon'];?>" class="m_pic"></div>
             </a>
             <div class="clfl"></div>
        </div>
	<?php  } ?>
<?php  } } ?>
</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>