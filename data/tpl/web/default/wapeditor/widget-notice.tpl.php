<?php defined('IN_IA') or exit('Access Denied');?><div ng-controller="noticeCtrl">
<?php  if($_GPC['iseditor']) { ?> 
	<!--公告-->
	<div class="app-notice-edit">
		<div class="arrow-left"></div>
		<div class="inner">
			<div class="panel panel-default">
				<div class="panel-body form-horizontal">
					<div class="form-group" style="margin-bottom:0;">
						<label class="col-xs-2 control-label">公告</label>
						<div class="col-xs-10">
							<input type="text" ng-model="activeItem.params.notice" value="" class="form-control" placeholder="请填写内容,如果过长,将会在手机上滚动显示">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--end 公告-->
<?php  } else { ?>
	<!--app公告-->
	<div class="app-notice">
		<div class="inner">
			<div class="notice-box">
				<div class="scrollNotice">
					<span class="js-scroll-notice">公告: {{module.params.notice || '请填写内容,如果过长,将会在手机上滚动显示!'}}</span>
				</div>
			</div>
		</div>
	</div>
	<!--end 公告-->
<?php  } ?>
</div>
