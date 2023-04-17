-- 全局配置
DROP TABLE IF EXISTS `global_config`;
CREATE TABLE IF NOT EXISTS `global_config` (
  `k` varchar(32) NOT NULL COMMENT '键',
  `v` text NOT NULL COMMENT '值',
  `d` varchar(32) DEFAULT '' COMMENT '描述',
  UNIQUE KEY `k` (`k`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 用户配置
DROP TABLE IF EXISTS `user_config`;
CREATE TABLE IF NOT EXISTS `user_config` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `k` varchar(32) NOT NULL COMMENT '键',
  `v` text NOT NULL COMMENT '值',
  `t` varchar(32) NOT NULL COMMENT '类型',
  `d` varchar(32) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


-- 统计
DROP TABLE IF EXISTS `user_count`;
CREATE TABLE IF NOT EXISTS `user_count` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `k` varchar(32) NOT NULL COMMENT '键',
  `v` bigint(10) UNSIGNED DEFAULT '0' COMMENT '值',
  `t` varchar(32) NOT NULL COMMENT '类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 数据库升级记录
DROP TABLE IF EXISTS `updatadb_logs`;
CREATE TABLE IF NOT EXISTS `updatadb_logs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name` varchar(32) NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  `status` varchar(5) NOT NULL DEFAULT 'TRUE',
  `extra` varchar(512) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_name` (`file_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

INSERT INTO "updatadb_logs" ("id", "file_name", "update_time", "status", "extra") VALUES ('1', '20230417.php', '1681719049', 'TRUE', '');

-- 创建用户表
DROP TABLE IF EXISTS `global_user`;
CREATE TABLE IF NOT EXISTS `global_user` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `FID` int(10) UNSIGNED NOT NULL,
  `User` varchar(32) NOT NULL COMMENT '账号',
  `Password` varchar(32) NOT NULL COMMENT '密码',
  `UserGroup` varchar(32) NOT NULL COMMENT '用户组',
  `Email` varchar(32) NOT NULL COMMENT '邮箱',
  `SecretKey` varchar(32) NOT NULL DEFAULT '' COMMENT 'SecretKey',
  `Token` varchar(32) NOT NULL DEFAULT '' COMMENT 'Token',
  `RegIP` varchar(15) NOT NULL COMMENT '注册IP',
  `RegTime` int(10) UNSIGNED NOT NULL COMMENT '注册时间',
  `Login` varchar(16) NOT NULL COMMENT '登录入口',
  `LoginConfig` text NOT NULL COMMENT '登陆配置',
  `kct` int(10) UNSIGNED DEFAULT '0' COMMENT 'Key清理时间',
  `Extend1` text NOT NULL COMMENT '扩展1',
  `Extend2` text NOT NULL COMMENT '扩展2',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `User` (`User`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- 用户分类表
DROP TABLE IF EXISTS `user_categorys`;
CREATE TABLE IF NOT EXISTS `user_categorys` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cid` int(10) UNSIGNED NOT NULL COMMENT '分类ID',
  `fid` int(10) UNSIGNED NOT NULL COMMENT '父分类ID',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `pid` int(10) UNSIGNED NOT NULL COMMENT '加密组id',
  `status` int(1) NOT NULL COMMENT '状态',
  `property` int(1) NOT NULL COMMENT '私有',
  `name` varchar(128) NOT NULL COMMENT '名称',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '添加时间',
  `up_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `weight` int(10) NOT NULL COMMENT '权重',
  `description` varchar(128) NOT NULL DEFAULT '' COMMENT '描述',
  `font_icon` text NOT NULL COMMENT '字体图标',
  `icon` text NOT NULL DEFAULT '' COMMENT '个性图标',
  `extend` text NOT NULL COMMENT '扩展',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户分类';

INSERT INTO `user_categorys` (`id`, `cid`, `fid`, `uid`, `pid`, `status`, `property`, `name`, `add_time`, `up_time`, `weight`, `description`, `font_icon`, `icon`, `extend`) VALUES
(1, 1, 0, 0, 0, 1, 0, '默认分类', 1672502400, 1672502400, 0, 'TwoNav默认分类', 'fa fa-book', '', '');


-- 用户链接表
DROP TABLE IF EXISTS `user_links`;
CREATE TABLE IF NOT EXISTS `user_links` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lid` int(10) UNSIGNED NOT NULL COMMENT '链接id',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `fid` int(10) UNSIGNED NOT NULL COMMENT '分类id',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '加密组id',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `property` int(1) NOT NULL DEFAULT '0' COMMENT '私有',
  `title` varchar(128) NOT NULL COMMENT '标题',
  `url` varchar(1024) NOT NULL COMMENT '主链接',
  `url_standby` text NOT NULL COMMENT '备用链接',
  `weight` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `description` varchar(128) NOT NULL DEFAULT '' COMMENT '描述',
  `icon` text NOT NULL DEFAULT '' COMMENT '图标',
  `click` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点击数',
  `add_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `up_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间',
  `extend` text NOT NULL COMMENT '扩展',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户链接';


INSERT INTO `user_links` (`id`, `lid`, `uid`, `fid`, `pid`, `status`, `property`, `title`, `url`, `url_standby`, `weight`, `description`, `icon`, `click`, `add_time`, `up_time`, `extend`) VALUES
(1, 1, 0, 1, 0, 1, 0, 'TwoNav 源码', 'https://gitee.com/tznb/TwoNav', '', 0, '项目开源地址', '', 0, 1672502400, 1672502400, ''),
(2, 2, 0, 1, 0, 1, 0, '使用说明', 'https://gitee.com/tznb/TwoNav/wikis', '', 0, '使用说明', '', 0, 1672502400, 1672502400, '');



-- 登录信息表
DROP TABLE IF EXISTS `user_login_info`;
CREATE TABLE IF NOT EXISTS `user_login_info` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `user` varchar(32) NOT NULL COMMENT '用户名',
  `ip` varchar(15) NOT NULL COMMENT '登录IP',
  `ua` varchar(256) NOT NULL COMMENT '浏览器UA',
  `login_time` int(10) UNSIGNED NOT NULL COMMENT '登录时间',
  `last_time` int(10) UNSIGNED NOT NULL COMMENT '最后访问时间',
  `expire_time` int(10) UNSIGNED NOT NULL COMMENT '过期时间',
  `cookie_key` varchar(32) NOT NULL COMMENT 'cookie_key',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- 日志表
DROP TABLE IF EXISTS `user_log`;
CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `user` varchar(32) NOT NULL COMMENT '用户名',
  `ip` varchar(15) NOT NULL COMMENT '请求ip',
  `time` varchar(13) NOT NULL COMMENT '请求时间',
  `type` varchar(16) NOT NULL COMMENT '日志类型',
  `content` text NOT NULL COMMENT '请求内容',
  `description` varchar(128) NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='日志';

-- 用户组
DROP TABLE IF EXISTS `user_group`;
CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL COMMENT '分组代号',
  `name` text NOT NULL COMMENT '分组名称',
  `uid` text NOT NULL COMMENT '模板用户id',
  `uname` text NOT NULL COMMENT '模板用户名',
  `allow` text NOT NULL COMMENT '允许权限',
  `codes` text NOT NULL COMMENT '允许代号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 权限列表
DROP TABLE IF EXISTS `purview_list`;
CREATE TABLE IF NOT EXISTS `purview_list` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL COMMENT '代号',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `description` varchar(128) NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `purview_list` (`code`, `name`, `description`) VALUES
('Upload_icon', '上传图标', '允许上传分类和链接图标'),
('Common_home', '公开主页', '允许主页公开访问(原强制私有)'),
('Sub_domain', '二级域名', '允许使用二级域名访问主页'),
('site_info', '站点信息', '允许修改站点信息'),
('header', '头部代码', '允许自定义头部代码(需允许站点信息)'),
('footer', '底部代码', '允许自定义底部代码(需允许站点信息)'),
('category', '分类管理', '允许添加/编辑/删除分类(未勾选时只读)'),
('link', '链接管理', '允许添加/编辑/删除链接(未勾选时只读)'),
('apply', '收录管理', '允许使用收录功能'),
('link_pwd', '加密管理', '允许使用加密管理(未勾选时只读)'),
('guestbook', '留言板', '允许使用留言板功能'),
('link_extend', '链接扩展', '允许使用链接扩展字段'),
('theme_in', '主题设置', '后台显示主题设置菜单'),
('theme_set', '主题配置', '允许自定义主题配置');

-- 注册码列表
DROP TABLE IF EXISTS `regcode_list`;
CREATE TABLE IF NOT EXISTS `regcode_list` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL,
  `regcode` varchar(64) NOT NULL COMMENT '注册码',
  `u_group` varchar(64) NOT NULL COMMENT '用户组',
  `use_state` varchar(64) NOT NULL COMMENT '使用状态',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '生成时间',
  `use_time` int(10) UNSIGNED NOT NULL COMMENT '使用时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `regcode` (`regcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 加密分组
DROP TABLE IF EXISTS `user_pwd_group`;
CREATE TABLE IF NOT EXISTS `user_pwd_group` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL COMMENT '分组id',
  `uid` varchar(32) NOT NULL COMMENT '用户id',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `description` varchar(128) NOT NULL DEFAULT '' COMMENT '描述',
  `display` int(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '主页显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 收录申请
DROP TABLE IF EXISTS `user_apply`;
CREATE TABLE IF NOT EXISTS `user_apply` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL COMMENT '用户id',
  `iconurl` varchar(512) NOT NULL COMMENT '图标url',
  `title` varchar(512) NOT NULL COMMENT '标题',
  `url` varchar(512) NOT NULL COMMENT '链接',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT 'ip',
  `email` varchar(128) NOT NULL DEFAULT '' COMMENT '邮箱',
  `ua` varchar(512) NOT NULL DEFAULT '' COMMENT '浏览器UA',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `state` int(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `category_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类id',
  `category_name` varchar(512) NOT NULL DEFAULT '' COMMENT '分类名',
  `description` varchar(512) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 书签分享
DROP TABLE IF EXISTS `user_share`;
CREATE TABLE IF NOT EXISTS `user_share` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL COMMENT '用户id',
  `sid` varchar(13) NOT NULL DEFAULT '' COMMENT '标识',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `pwd` varchar(64) NOT NULL COMMENT '密码',
  `add_time` Bigint(13) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `up_time` Bigint(13) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `expire_time` Bigint(13) UNSIGNED NOT NULL DEFAULT '0' COMMENT '到期时间',
  `views` Bigint(13) NOT NULL DEFAULT '0' COMMENT '浏览数',
  `description` varchar(13) NOT NULL DEFAULT '' COMMENT '备注',
  `type` int(1) NOT NULL COMMENT '类型',
  `data` text NOT NULL COMMENT '数据',
  `pv` int(1) NOT NULL COMMENT '私有可见',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

