<?php
$sql="DROP TABLE IF EXISTS `ims_ewei_shop_coupon_usesendtasks`;
CREATE TABLE `ims_ewei_shop_coupon_usesendtasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `usecouponid` int(11) DEFAULT '0',
  `couponid` int(11) DEFAULT '0',
  `starttime` int(11) DEFAULT '0',
  `endtime` int(11) DEFAULT '0',
  `sendnum` int(11) DEFAULT '1',
  `num` int(11) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ewei_shop_pc_adv`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_pc_adv` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL,
  `advname` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `src` varchar(255) NOT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `enabled` tinyint(3) unsigned NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `width` int(11) unsigned NOT NULL,
  `height` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ims_ewei_shop_pc_link`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_pc_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL,
  `linkname` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `displayorder` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ims_ewei_shop_pc_menu`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_pc_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL,
  `type` int(11) unsigned DEFAULT '0',
  `displayorder` int(11) unsigned DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `enabled` tinyint(3) unsigned DEFAULT '1',
  `createtime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ims_ewei_shop_pc_slide`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_pc_slide` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned DEFAULT '0',
  `type` int(11) unsigned DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `backcolor` varchar(255) DEFAULT NULL,
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  `shopid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_displayorder` (`displayorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;	

DROP TABLE IF EXISTS `ims_ewei_shop_member_mergelog`;
CREATE TABLE `ims_ewei_shop_member_mergelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `mergetime` int(11) NOT NULL DEFAULT '0',
  `openid_a` varchar(30) NOT NULL,
  `openid_b` varchar(30) NOT NULL,
  `mid_a` int(11) NOT NULL,
  `mid_b` int(11) NOT NULL,
  `detail_a` text,
  `detail_b` text,
  `detail_c` text,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`) USING BTREE,
  KEY `idx_mid_a` (`mid_a`) USING BTREE,
  KEY `idx_mid_b` (`mid_b`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ewei_shop_payment`;
CREATE TABLE `ims_ewei_shop_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `appid` varchar(255) DEFAULT '',
  `mch_id` varchar(50) NOT NULL DEFAULT '',
  `apikey` varchar(50) NOT NULL DEFAULT '',
  `sub_appid` varchar(50) DEFAULT '',
  `sub_appsecret` varchar(50) DEFAULT '',
  `sub_mch_id` varchar(50) DEFAULT '',
  `cert_file` text,
  `key_file` text,
  `root_file` text,
  `is_raw` tinyint(1) DEFAULT '0',
  `createtime` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ewei_shop_polyapi_key`;
CREATE TABLE `ims_ewei_shop_polyapi_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `merchid` int(11) NOT NULL DEFAULT '0',
  `appkey` varchar(200) NOT NULL DEFAULT '',
  `token` varchar(200) NOT NULL DEFAULT '',
  `appsecret` varchar(200) NOT NULL DEFAULT '',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `updatetime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_appkey` (`appkey`),
  KEY `idx_token` (`token`),
  KEY `idx_appsecret` (`appsecret`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ewei_shop_task`;
CREATE TABLE `ims_ewei_shop_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` int(11) NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `dotime` int(11) NOT NULL DEFAULT '0',
  `donetime` int(11) NOT NULL DEFAULT '0',
  `timelimit` float(11,1) NOT NULL,
  `keyword` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `explain` text,
  `require_data` text NOT NULL,
  `reward_data` text NOT NULL,
  `period` int(11) NOT NULL DEFAULT '0',
  `repeat` int(11) NOT NULL DEFAULT '0',
  `maxtimes` int(11) NOT NULL DEFAULT '0',
  `everyhours` float(11,1) NOT NULL DEFAULT '0.0',
  `logo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ewei_shop_task_extension`;
CREATE TABLE `ims_ewei_shop_task_extension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taskname` varchar(255) NOT NULL DEFAULT '',
  `taskclass` varchar(25) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `classify` varchar(255) NOT NULL DEFAULT '',
  `classify_name` varchar(255) NOT NULL DEFAULT '',
  `verb` varchar(255) NOT NULL DEFAULT '',
  `unit` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ewei_shop_task_extension_join`;
CREATE TABLE `ims_ewei_shop_task_extension_join` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL,
  `taskid` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `require_data` text NOT NULL,
  `progress_data` text NOT NULL,
  `reward_data` text NOT NULL,
  `completetime` int(11) NOT NULL DEFAULT '0',
  `pickuptime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `dotime` int(11) NOT NULL DEFAULT '0',
  `rewarded` text NOT NULL,
  `logo` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ewei_shop_express`;
CREATE TABLE `ims_ewei_shop_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '',
  `express` varchar(50) DEFAULT '',
  `status` tinyint(1) DEFAULT '1',
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `code` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=utf8;

INSERT INTO `ims_ewei_shop_express` VALUES ('1', '顺丰', 'shunfeng', '1', '0', 'JH_014');
INSERT INTO `ims_ewei_shop_express` VALUES ('2', '申通', 'shentong', '1', '0', 'JH_005');
INSERT INTO `ims_ewei_shop_express` VALUES ('3', '韵达快运', 'yunda', '1', '0', 'JH_003');
INSERT INTO `ims_ewei_shop_express` VALUES ('4', '天天快递', 'tiantian', '1', '0', 'JH_004');
INSERT INTO `ims_ewei_shop_express` VALUES ('5', '圆通速递', 'yuantong', '1', '0', 'JH_002');
INSERT INTO `ims_ewei_shop_express` VALUES ('6', '中通速递', 'zhongtong', '1', '0', 'JH_006');
INSERT INTO `ims_ewei_shop_express` VALUES ('7', 'ems快递', 'ems', '1', '0', 'JH_001');
INSERT INTO `ims_ewei_shop_express` VALUES ('8', '百世汇通', 'huitongkuaidi', '1', '0', 'JH_012');
INSERT INTO `ims_ewei_shop_express` VALUES ('9', '全峰快递', 'quanfengkuaidi', '1', '0', 'JH_009');
INSERT INTO `ims_ewei_shop_express` VALUES ('10', '宅急送', 'zhaijisong', '1', '0', 'JH_007');
INSERT INTO `ims_ewei_shop_express` VALUES ('11', 'aae全球专递', 'aae', '1', '0', 'JHI_049');
INSERT INTO `ims_ewei_shop_express` VALUES ('12', '安捷快递', 'anjie', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('13', '安信达快递', 'anxindakuaixi', '1', '0', 'JH_131');
INSERT INTO `ims_ewei_shop_express` VALUES ('14', '彪记快递', 'biaojikuaidi', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('15', 'bht', 'bht', '1', '0', 'JHI_008');
INSERT INTO `ims_ewei_shop_express` VALUES ('16', '百福东方国际物流', 'baifudongfang', '1', '0', 'JH_062');
INSERT INTO `ims_ewei_shop_express` VALUES ('17', '中国东方（COE）', 'coe', '1', '0', 'JHI_038');
INSERT INTO `ims_ewei_shop_express` VALUES ('18', '长宇物流', 'changyuwuliu', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('19', '大田物流', 'datianwuliu', '1', '0', 'JH_050');
INSERT INTO `ims_ewei_shop_express` VALUES ('20', '德邦物流', 'debangwuliu', '1', '0', 'JH_011');
INSERT INTO `ims_ewei_shop_express` VALUES ('21', 'dhl', 'dhl', '1', '0', 'JHI_002');
INSERT INTO `ims_ewei_shop_express` VALUES ('22', 'dpex', 'dpex', '1', '0', 'JHI_011');
INSERT INTO `ims_ewei_shop_express` VALUES ('23', 'd速快递', 'dsukuaidi', '1', '0', 'JH_049');
INSERT INTO `ims_ewei_shop_express` VALUES ('24', '递四方', 'disifang', '1', '0', 'JHI_080');
INSERT INTO `ims_ewei_shop_express` VALUES ('25', 'fedex（国外）', 'fedex', '1', '0', 'JHI_014');
INSERT INTO `ims_ewei_shop_express` VALUES ('26', '飞康达物流', 'feikangda', '1', '0', 'JH_088');
INSERT INTO `ims_ewei_shop_express` VALUES ('27', '凤凰快递', 'fenghuangkuaidi', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('28', '飞快达', 'feikuaida', '1', '0', 'JH_151');
INSERT INTO `ims_ewei_shop_express` VALUES ('29', '国通快递', 'guotongkuaidi', '1', '0', 'JH_010');
INSERT INTO `ims_ewei_shop_express` VALUES ('30', '港中能达物流', 'ganzhongnengda', '1', '0', 'JH_033');
INSERT INTO `ims_ewei_shop_express` VALUES ('31', '广东邮政物流', 'guangdongyouzhengwuliu', '1', '0', 'JH_135');
INSERT INTO `ims_ewei_shop_express` VALUES ('32', '共速达', 'gongsuda', '1', '0', 'JH_039');
INSERT INTO `ims_ewei_shop_express` VALUES ('33', '恒路物流', 'hengluwuliu', '1', '0', 'JH_048');
INSERT INTO `ims_ewei_shop_express` VALUES ('34', '华夏龙物流', 'huaxialongwuliu', '1', '0', 'JH_129');
INSERT INTO `ims_ewei_shop_express` VALUES ('35', '海红', 'haihongwangsong', '1', '0', 'JH_132');
INSERT INTO `ims_ewei_shop_express` VALUES ('36', '海外环球', 'haiwaihuanqiu', '1', '0', 'JHI_013');
INSERT INTO `ims_ewei_shop_express` VALUES ('37', '佳怡物流', 'jiayiwuliu', '1', '0', 'JH_035');
INSERT INTO `ims_ewei_shop_express` VALUES ('38', '京广速递', 'jinguangsudikuaijian', '1', '0', 'JH_041');
INSERT INTO `ims_ewei_shop_express` VALUES ('39', '急先达', 'jixianda', '1', '0', 'JH_040');
INSERT INTO `ims_ewei_shop_express` VALUES ('40', '佳吉物流', 'jiajiwuliu', '1', '0', 'JH_030');
INSERT INTO `ims_ewei_shop_express` VALUES ('41', '加运美物流', 'jymwl', '1', '0', 'JH_054');
INSERT INTO `ims_ewei_shop_express` VALUES ('42', '金大物流', 'jindawuliu', '1', '0', 'JH_079');
INSERT INTO `ims_ewei_shop_express` VALUES ('43', '嘉里大通', 'jialidatong', '1', '0', 'JH_060');
INSERT INTO `ims_ewei_shop_express` VALUES ('44', '晋越快递', 'jykd', '1', '0', 'JHI_046');
INSERT INTO `ims_ewei_shop_express` VALUES ('45', '快捷速递', 'kuaijiesudi', '1', '0', 'JH_008');
INSERT INTO `ims_ewei_shop_express` VALUES ('46', '联邦快递（国内）', 'lianb', '1', '0', 'JH_122');
INSERT INTO `ims_ewei_shop_express` VALUES ('47', '联昊通物流', 'lianhaowuliu', '1', '0', 'JH_021');
INSERT INTO `ims_ewei_shop_express` VALUES ('48', '龙邦物流', 'longbanwuliu', '1', '0', 'JH_019');
INSERT INTO `ims_ewei_shop_express` VALUES ('49', '立即送', 'lijisong', '1', '0', 'JH_044');
INSERT INTO `ims_ewei_shop_express` VALUES ('50', '乐捷递', 'lejiedi', '1', '0', 'JH_043');
INSERT INTO `ims_ewei_shop_express` VALUES ('51', '民航快递', 'minghangkuaidi', '1', '0', 'JH_100');
INSERT INTO `ims_ewei_shop_express` VALUES ('52', '美国快递', 'meiguokuaidi', '1', '0', 'JHI_044');
INSERT INTO `ims_ewei_shop_express` VALUES ('53', '门对门', 'menduimen', '1', '0', 'JH_036');
INSERT INTO `ims_ewei_shop_express` VALUES ('54', 'OCS', 'ocs', '1', '0', 'JHI_012');
INSERT INTO `ims_ewei_shop_express` VALUES ('55', '配思货运', 'peisihuoyunkuaidi', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('56', '全晨快递', 'quanchenkuaidi', '1', '0', 'JH_055');
INSERT INTO `ims_ewei_shop_express` VALUES ('57', '全际通物流', 'quanjitong', '1', '0', 'JH_127');
INSERT INTO `ims_ewei_shop_express` VALUES ('58', '全日通快递', 'quanritongkuaidi', '1', '0', 'JH_029');
INSERT INTO `ims_ewei_shop_express` VALUES ('59', '全一快递', 'quanyikuaidi', '1', '0', 'JH_020');
INSERT INTO `ims_ewei_shop_express` VALUES ('60', '如风达', 'rufengda', '1', '0', 'JH_017');
INSERT INTO `ims_ewei_shop_express` VALUES ('61', '三态速递', 'santaisudi', '1', '0', 'JH_065');
INSERT INTO `ims_ewei_shop_express` VALUES ('62', '盛辉物流', 'shenghuiwuliu', '1', '0', 'JH_066');
INSERT INTO `ims_ewei_shop_express` VALUES ('63', '速尔物流', 'sue', '1', '0', 'JH_016');
INSERT INTO `ims_ewei_shop_express` VALUES ('64', '盛丰物流', 'shengfeng', '1', '0', 'JH_082');
INSERT INTO `ims_ewei_shop_express` VALUES ('65', '赛澳递', 'saiaodi', '1', '0', 'JH_042');
INSERT INTO `ims_ewei_shop_express` VALUES ('66', '天地华宇', 'tiandihuayu', '1', '0', 'JH_018');
INSERT INTO `ims_ewei_shop_express` VALUES ('67', 'tnt', 'tnt', '1', '0', 'JHI_003');
INSERT INTO `ims_ewei_shop_express` VALUES ('68', 'ups', 'ups', '1', '0', 'JHI_004');
INSERT INTO `ims_ewei_shop_express` VALUES ('69', '万家物流', 'wanjiawuliu', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('70', '文捷航空速递', 'wenjiesudi', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('71', '伍圆', 'wuyuan', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('72', '万象物流', 'wxwl', '1', '0', 'JH_115');
INSERT INTO `ims_ewei_shop_express` VALUES ('73', '新邦物流', 'xinbangwuliu', '1', '0', 'JH_022');
INSERT INTO `ims_ewei_shop_express` VALUES ('74', '信丰物流', 'xinfengwuliu', '1', '0', 'JH_023');
INSERT INTO `ims_ewei_shop_express` VALUES ('75', '亚风速递', 'yafengsudi', '1', '0', 'JH_075');
INSERT INTO `ims_ewei_shop_express` VALUES ('76', '一邦速递', 'yibangwuliu', '1', '0', 'JH_064');
INSERT INTO `ims_ewei_shop_express` VALUES ('77', '优速物流', 'youshuwuliu', '1', '0', 'JH_013');
INSERT INTO `ims_ewei_shop_express` VALUES ('78', '邮政快递包裹', 'youzhengguonei', '1', '0', 'JH_077');
INSERT INTO `ims_ewei_shop_express` VALUES ('79', '邮政国际包裹挂号信', 'youzhengguoji', '1', '0', '');
INSERT INTO `ims_ewei_shop_express` VALUES ('80', '远成物流', 'yuanchengwuliu', '1', '0', 'JH_024');
INSERT INTO `ims_ewei_shop_express` VALUES ('81', '源伟丰快递', 'yuanweifeng', '1', '0', 'JH_141');
INSERT INTO `ims_ewei_shop_express` VALUES ('82', '元智捷诚快递', 'yuanzhijiecheng', '1', '0', 'JH_126');
INSERT INTO `ims_ewei_shop_express` VALUES ('83', '运通快递', 'yuntongkuaidi', '1', '0', 'JH_145');
INSERT INTO `ims_ewei_shop_express` VALUES ('84', '越丰物流', 'yuefengwuliu', '1', '0', 'JH_068');
INSERT INTO `ims_ewei_shop_express` VALUES ('85', '源安达', 'yad', '1', '0', 'JH_067');
INSERT INTO `ims_ewei_shop_express` VALUES ('86', '银捷速递', 'yinjiesudi', '1', '0', 'JH_148');
INSERT INTO `ims_ewei_shop_express` VALUES ('87', '中铁快运', 'zhongtiekuaiyun', '1', '0', 'JH_015');
INSERT INTO `ims_ewei_shop_express` VALUES ('88', '中邮物流', 'zhongyouwuliu', '1', '0', 'JH_027');
INSERT INTO `ims_ewei_shop_express` VALUES ('89', '忠信达', 'zhongxinda', '1', '0', 'JH_086');
INSERT INTO `ims_ewei_shop_express` VALUES ('90', '芝麻开门', 'zhimakaimen', '1', '0', 'JH_026');
INSERT INTO `ims_ewei_shop_express` VALUES ('91', '安能物流', 'annengwuliu', '1', '0', 'JH_059');
INSERT INTO `ims_ewei_shop_express` VALUES ('92', '京东快递', 'jd', '1', '0', 'JH_046');

DROP TABLE IF EXISTS `ims_ewei_shop_plugin`;
CREATE TABLE `ims_ewei_shop_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayorder` int(11) DEFAULT '0',
  `identity` varchar(50) DEFAULT '',
  `name` varchar(50) DEFAULT '',
  `version` varchar(10) DEFAULT '',
  `author` varchar(20) DEFAULT '',
  `status` int(11) DEFAULT '0',
  `category` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `desc` text,
  `iscom` tinyint(3) DEFAULT '0',
  `deprecated` tinyint(3) DEFAULT '0',
  `isv2` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_identity` (`identity`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

INSERT INTO `ims_ewei_shop_plugin` VALUES ('1', '1', 'qiniu', '七牛存储', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/qiniu.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('2', '2', 'taobao', '商品助手', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/taobao.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('3', '3', 'commission', '人人分销', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/commission.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('4', '4', 'poster', '超级海报', '1.2', '官方', '1', 'sale', '../addons/ewei_shopv2/static/images/poster.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('5', '5', 'verify', 'O2O核销', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/verify.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('6', '6', 'tmessage', '会员群发', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/tmessage.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('7', '7', 'perm', '分权系统', '1.0', '官方', '1', 'help', '../addons/ewei_shopv2/static/images/perm.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('8', '8', 'sale', '营销宝', '1.0', '官方', '1', 'sale', '../addons/ewei_shopv2/static/images/sale.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('9', '9', 'designer', '店铺装修V1', '1.0', '官方', '1', 'help', '../addons/ewei_shopv2/static/images/designer.jpg', null, '0', '1', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('10', '10', 'creditshop', '积分商城', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/creditshop.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('11', '11', 'virtual', '虚拟物品', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/virtual.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('12', '11', 'article', '文章营销', '1.0', '官方', '1', 'help', '../addons/ewei_shopv2/static/images/article.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('13', '13', 'coupon', '超级券', '1.0', '官方', '1', 'sale', '../addons/ewei_shopv2/static/images/coupon.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('14', '14', 'postera', '活动海报', '1.0', '官方', '1', 'sale', '../addons/ewei_shopv2/static/images/postera.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('15', '15', 'system', '系统工具', '1.0', '官方', '0', 'help', '../addons/ewei_shopv2/static/images/system.jpg', null, '0', '1', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('16', '16', 'diyform', '自定表单', '1.0', '官方', '1', 'help', '../addons/ewei_shopv2/static/images/diyform.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('17', '17', 'exhelper', '快递助手', '1.0', '官方', '1', 'help', '../addons/ewei_shopv2/static/images/exhelper.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('18', '18', 'groups', '人人拼团', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/groups.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('19', '19', 'diypage', '店铺装修', '2.0', '官方', '1', 'help', '../addons/ewei_shopv2/static/images/designer.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('20', '20', 'globonus', '全民股东', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/globonus.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('21', '21', 'merch', '多商户', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/merch.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('22', '22', 'qa', '帮助中心', '1.0', '官方', '1', 'help', '../addons/ewei_shopv2/static/images/qa.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('23', '23', 'sms', '短信提醒', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/sms.jpg', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('24', '24', 'sign', '积分签到', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/sign.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('25', '25', 'sns', '全民社区', '1.0', '官方', '1', 'sale', '../addons/ewei_shopv2/static/images/sns.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('26', '26', 'wap', '全网通', '1.0', '官方', '1', 'tool', '', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('27', '27', 'h5app', 'H5APP', '1.0', '官方', '1', 'tool', '', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('28', '28', 'abonus', '区域代理', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/abonus.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('29', '29', 'printer', '小票打印机', '1.0', '官方', '1', 'tool', '', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('30', '30', 'bargain', '砍价活动', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/bargain.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('31', '31', 'task', '任务中心', '1.0', '官方', '1', 'sale', '../addons/ewei_shopv2/static/images/task.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('32', '32', 'cashier', '收银台', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/cashier.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('33', '33', 'messages', '消息群发', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/messages.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('34', '34', 'seckill', '整点秒杀', '1.0', '官方', '1', 'sale', '../addons/ewei_shopv2/static/images/seckill.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('35', '35', 'exchange', '兑换中心', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/exchange.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('36', '36', 'lottery', '游戏营销', '1.0', '官方', '1', 'sale', '../addons/ewei_shopv2/static/images/lottery.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('37', '37', 'mmanage', '手机端商家管理中心', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/mmanage.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('38', '38', 'polyapi', '进销存-网店管家', '1.0', '官方', '1', 'tool', '../addons/ewei_shopv2/static/images/polyapi.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('40', '41', 'quick', '快速购买', '1.0', '官方', '1', 'biz', '../addons/ewei_shopv2/static/images/quick.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('41', '44', 'pc', 'PC端', '1.0', '微信魔方', '1', 'tool', '../addons/ewei_shopv2/static/images/pc.jpg', '', '0', '0', '0');
";
pdo_run($sql);
if(!pdo_fieldexists('ewei_shop_bargain_account',  'sharestyle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_bargain_account')." ADD COLUMN `sharestyle`  tinyint(1) NOT NULL DEFAULT 0;");
}
if(!pdo_fieldexists('ewei_shop_cashier_pay_log',  'refundsn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_cashier_pay_log')." ADD COLUMN `refundsn`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_dispatch',  'freeprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_dispatch')." ADD COLUMN `freeprice`  decimal(10,2) NOT NULL DEFAULT 0.00;");
}
if(!pdo_fieldexists('ewei_shop_express',  'code')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_express')." ADD COLUMN `code`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_order_peerpay_payinfo',  'refundstatus')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_peerpay_payinfo')." ADD COLUMN `refundstatus`  tinyint(1) NOT NULL DEFAULT 0;");
}
if(!pdo_fieldexists('ewei_shop_order_peerpay_payinfo',  'tid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_peerpay_payinfo')." ADD COLUMN `tid`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_order_peerpay_payinfo',  'refundprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_peerpay_payinfo')." ADD COLUMN `refundprice`  decimal(10,2) NOT NULL DEFAULT 0.00;");
}
if(!pdo_fieldexists('ewei_shop_order_peerpay_payinfo',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_peerpay_payinfo')." ADD COLUMN `openid`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
}
if(!pdo_fieldexists('ewei_shop_task_join',  'needcount')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_task_join')." MODIFY COLUMN `needcount`  int(11) NOT NULL DEFAULT 0;");
}
if(!pdo_fieldexists('ewei_shop_task_join',  'completecount')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_task_join')." MODIFY COLUMN `completecount`  int(11) NOT NULL DEFAULT 0;");
}
if(!pdo_fieldexists('ewei_shop_quick',  'merchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_quick')." ADD COLUMN `merchid` int(11) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('ewei_shop_task_default',  'bgimg')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_task_default')." ADD COLUMN `bgimg` varchar(255) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_task_default',  'open')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_task_default')." ADD COLUMN `open` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('ewei_shop_order',  'istrade')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." ADD COLUMN   `istrade` tinyint(3) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('ewei_shop_merch_reg',  'uname')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_merch_reg')." ADD COLUMN `uname` varchar(50) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_merch_reg',  'upass')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_merch_reg')." ADD COLUMN `upass` varchar(255) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_merch_user',  'uname')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_merch_user')." ADD COLUMN `uname` varchar(50) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_merch_user',  'upass')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_merch_user')." ADD COLUMN `upass` varchar(255) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_goods',  'taobaoid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD COLUMN `taobaoid` varchar(255) DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_goods',  'taobaourl')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD COLUMN `taobaourl` varchar(255) DEFAULT '';");
}
if(!pdo_fieldexists('ewei_shop_goods',  'saleupdate30424')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD COLUMN `saleupdate30424` tinyint(3) DEFAULT '0';");
}
if(!pdo_fieldexists('ewei_shop_goods',  'showsales')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." ADD COLUMN `showsales` tinyint(3) NOT NULL DEFAULT '1';");
}
?>