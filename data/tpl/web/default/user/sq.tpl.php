<?php defined('IN_IA') or exit('Access Denied');?>﻿<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
</ol>
<div class="panel panel-default">

  <div class="panel-heading">授权</div>

  <div class="panel-body">

    <form class="form-horizontal" action="" method="POST">
    <input type="hidden" name="uid" class="form-control" id="uid" value="<?php  echo $uid;?>">

  <div class="form-group">

    <label for="inputEmail3" class="col-sm-2 control-label">给他充值金额</label>

    <div class="col-sm-10">

      <input type="text" name="credit" class="form-control" id="credit" value="0">

    </div>

  </div>

  <div class="form-group">

    <label for="inputPassword3" class="col-sm-2 control-label">授权该用户名下公众号：</label>

    <div class="col-sm-10">

      <select class="col-xs-12 col-sm-3 col-md-2"  name="weid" id="weid" style="width:17%;" >
      <option value="">选择公众号</option>
     <?php  if(is_array($gzh)) { foreach($gzh as $row) { ?>
     <?php  $gzhname=pdo_fetch("SELECT * FROM " . tablename('uni_account')."where uniacid=".$row['uniacid']);?>
                <option value="<?php  echo $row['uniacid'];?>"><?php  echo $gzhname['name'];?></option>
      <?php  } } ?>
    </select>	

    </div>

  </div>

  <div class="form-group">

    <label for="inputPassword3" class="col-sm-2 control-label">授权模块：</label>

    <div class="col-sm-10">
<select class="col-xs-12 col-sm-3 col-md-2"  name="module" id="module" style="width:17%;" >
      <option value="">选择模块</option>
     <?php  if(is_array($module)) { foreach($module as $row) { ?>
      
                <option value="<?php  echo $row;?>"><?php  echo $modules[$row]['title'];?></option>
      
      <?php  } } ?>
    </select>	
    </div>

  </div>

  <div class="form-group">

    <label for="inputEmail3" class="col-sm-2 control-label">授权年限：</label>

    <div class="col-sm-10">

      <select class="col-xs-12 col-sm-3 col-md-2"  name="time" id="time" style="width:17%; padding-top: 2px;" >  
					<option value="1">一年 </option>
                    <option value="2">两年 </option>                                        
					<option value="3">三年 </option>
                    <option value="4">四年 </option>
                    <option value="5">五年 </option>
				 </select>
    </div>

  </div>
  <div class="form-group">

    <div class="col-sm-offset-2 col-sm-10">

      <input type="submit" name="submit" class="btn btn-info" value="提交" />

      <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />

    </div>

  </div>

</form>

  </div>

</div>
<?php  echo $pager;?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>