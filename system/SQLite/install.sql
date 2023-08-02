-- 全局配置
DROP TABLE IF EXISTS "global_config";
CREATE TABLE IF NOT EXISTS "global_config" (
  "k" text(32) NOT NULL,
  "v" text DEFAULT "",
  "d" TEXT(32) NOT NULL DEFAULT "",
  CONSTRAINT "k" UNIQUE ("k" ASC)
);

-- 用户配置
CREATE TABLE IF NOT EXISTS "user_config" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "k" text(32) NOT NULL DEFAULT "",
  "v" text NOT NULL DEFAULT "",
  "t" text(32) NOT NULL DEFAULT "",
  "d" TEXT(32) NOT NULL DEFAULT "",
  CONSTRAINT "id" UNIQUE ("id" ASC)
);

-- 统计
CREATE TABLE IF NOT EXISTS "user_count" (
  "uid" integer(10) NOT NULL,
  "k" text(32) NOT NULL DEFAULT "",
  "v" integer(10) NOT NULL DEFAULT 0,
  "t" text(32) NOT NULL DEFAULT "",
  "e" text NOT NULL DEFAULT ""
);

-- 数据库升级记录
CREATE TABLE IF NOT EXISTS "updatadb_logs" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "file_name" TEXT(32) NOT NULL,
  "update_time" integer(10) NOT NULL,
  "status" TEXT(5) NOT NULL DEFAULT "TRUE",
  "extra" TEXT(512) NOT NULL DEFAULT "",
  CONSTRAINT "file_name" UNIQUE ("file_name" ASC)
);
INSERT INTO "updatadb_logs" ("file_name", "update_time", "status", "extra") VALUES ('20230417.php', '1681719049', 'TRUE', '');
INSERT INTO "updatadb_logs" ("file_name", "update_time", "status", "extra") VALUES ('20230420.php', '1681977368', 'TRUE', '');
INSERT INTO "updatadb_logs" ("file_name", "update_time", "status", "extra") VALUES ('20230522.php', '1684762253', 'TRUE', '');
INSERT INTO "updatadb_logs" ("file_name", "update_time", "status", "extra") VALUES ('20230715.php', '1689427853', 'TRUE', '');
INSERT INTO "updatadb_logs" ("file_name", "update_time", "status", "extra") VALUES ('20230723.php', '1690119053', 'TRUE', '');


-- 创建用户表
CREATE TABLE IF NOT EXISTS "global_user" (
  "ID" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "FID" integer(10) NOT NULL,
  "User" TEXT(32) NOT NULL,
  "Password" TEXT(32) NOT NULL,
  "UserGroup" TEXT(32) NOT NULL,
  "Email" TEXT(32) NOT NULL,
  "SecretKey" TEXT(32) NOT NULL DEFAULT "",
  "Token" TEXT(32) NOT NULL DEFAULT "",
  "RegIP" TEXT(64) NOT NULL DEFAULT "",
  "RegTime" integer(10) NOT NULL,
  "Login" TEXT(16) NOT NULL,
  "LoginConfig" TEXT NOT NULL,
  "kct" integer(10) DEFAULT 0,
  "Extend1" TEXT NOT NULL DEFAULT "",
  "Extend2" TEXT NOT NULL DEFAULT "",
  CONSTRAINT "User" UNIQUE ("User" ASC, "Email" ASC)
);

-- 用户分类表
CREATE TABLE IF NOT EXISTS "user_categorys" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "cid" INTEGER(10) NOT NULL,
  "fid" integer(10) NOT NULL,
  "uid" integer(10) NOT NULL,
  "pid" integer(10) NOT NULL,
  "status" integer(1) NOT NULL,
  "property" integer(1) NOT NULL,
  "name" TEXT(128),
  "add_time" integer(10),
  "up_time" integer(10),
  "weight" integer(10),
  "description" TEXT(128) NOT NULL DEFAULT "",
  "font_icon" TEXT DEFAULT "",
  "icon" TEXT DEFAULT "",
  "extend" TEXT DEFAULT ""
);

INSERT INTO "user_categorys"("id", "cid", "fid", "uid", "pid", "status", "property", "name", "add_time", "up_time", "weight", "description", "font_icon", "icon", "extend") VALUES (1, 1, 0, 0, 0, 1, 0, '默认分类', 1672502400, 1672502400, 0, 'TwoNav默认分类', 'fa fa-book', '', '');

-- 用户链接表
CREATE TABLE IF NOT EXISTS "user_links" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "lid" integer(10) NOT NULL,
  "uid" integer(10) NOT NULL,
  "fid" integer(10) NOT NULL,
  "pid" integer(10) NOT NULL DEFAULT 0,
  "status" integer(1) NOT NULL DEFAULT 1,
  "property" integer(1) NOT NULL DEFAULT 0,
  "title" TEXT(128) NOT NULL,
  "url" TEXT(1024) NOT NULL,
  "url_standby" text NOT NULL DEFAULT "",
  "weight" integer(11) NOT NULL DEFAULT 0,
  "keywords" TEXT(128) NOT NULL DEFAULT "",
  "description" TEXT(128) NOT NULL DEFAULT "",
  "icon" text NOT NULL DEFAULT "",
  "click" integer(10) NOT NULL DEFAULT 0,
  "add_time" integer(10) NOT NULL DEFAULT 0,
  "up_time" integer(10) NOT NULL DEFAULT 0,
  "extend" text NOT NULL DEFAULT ""
);
INSERT INTO "user_links"("id", "lid", "uid", "fid", "pid", "status", "property", "title", "url", "url_standby", "weight", "description", "icon", "click", "add_time", "up_time", "extend") VALUES (1, 1, 0, 1, 0, 1, 0, 'TwoNav 源码', 'https://gitee.com/tznb/TwoNav', '', 0, '项目开源地址', '', 0, 1672502400, 1672502400, '');
INSERT INTO "user_links"("id", "lid", "uid", "fid", "pid", "status", "property", "title", "url", "url_standby", "weight", "description", "icon", "click", "add_time", "up_time", "extend") VALUES (2, 2, 0, 1, 0, 1, 0, '使用说明', 'https://gitee.com/tznb/TwoNav/wikis', '', 0, '使用说明', '', 0, 1672502400, 1672502400, '');
 
-- 登录信息表
CREATE TABLE IF NOT EXISTS "user_login_info" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "user" TEXT(32) NOT NULL,
  "ip" TEXT(64) NOT NULL,
  "ua" TEXT NOT NULL,
  "login_time" integer(10) NOT NULL,
  "last_time" integer(10) NOT NULL,
  "expire_time" integer(10) NOT NULL,
  "cookie_key" TEXT(32) NOT NULL
);

-- 日志表
CREATE TABLE IF NOT EXISTS "user_log" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "user" TEXT(32) NOT NULL,
  "ip" TEXT(64) NOT NULL,
  "time" TEXT(13) NOT NULL,
  "type" TEXT(16) NOT NULL,
  "content" TEXT NOT NULL,
  "description" TEXT NOT NULL
);

-- 用户组
DROP TABLE IF EXISTS "user_group";
CREATE TABLE IF NOT EXISTS "user_group" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "code" text(32) NOT NULL DEFAULT "",
  "name" text(32) NOT NULL DEFAULT "",
  "uid" integer(10) NOT NULL,
  "uname" text(32) NOT NULL DEFAULT "",
  "allow" text(64) NOT NULL DEFAULT "",
  "codes" text(64) NOT NULL DEFAULT "",
  CONSTRAINT "code" UNIQUE ("code" ASC)
);


-- 权限列表
CREATE TABLE IF NOT EXISTS "purview_list" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "code" text(64) NOT NULL DEFAULT "",
  "name" text(64) NOT NULL DEFAULT "",
  "description" text(128) NOT NULL DEFAULT ""
);

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
('theme_set', '主题配置', '允许自定义主题配置'),
('icon_pull', '图标拉取', '允许用户拉取链接图标'),
('article', '文章管理', '允许使用文章管理功能'),
('article_image', '文章图片', '允许在文章编辑器上传图片');

-- 注册码列表
CREATE TABLE IF NOT EXISTS "regcode_list" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "regcode" text(64) NOT NULL,
  "u_group" text(64) NOT NULL,
  "use_state" text(64) NOT NULL,
  "add_time" integer(64) NOT NULL,
  "use_time" integer(128) NOT NULL,
  CONSTRAINT "regcode" UNIQUE ("regcode" ASC)
);

-- 加密分组
CREATE TABLE IF NOT EXISTS "user_pwd_group" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "pid" integer(10) NOT NULL,
  "uid" integer(10) NOT NULL,
  "name" text(64) NOT NULL,
  "password" text(64) NOT NULL,
  "description" text(64) NOT NULL DEFAULT '',
  "display" integer(64) NOT NULL DEFAULT '1',
  CONSTRAINT "id" UNIQUE ("id" ASC)
);

-- 收录申请
CREATE TABLE IF NOT EXISTS "user_apply" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "iconurl" TEXT(512) DEFAULT "",
  "title" TEXT(512) DEFAULT "",
  "url" TEXT(512) DEFAULT "",
  "email" TEXT(128) DEFAULT "",
  "ip" TEXT(64) DEFAULT "",
  "ua" TEXT DEFAULT "",
  "time" integer DEFAULT "0",
  "state" integer DEFAULT "0",
  "category_id" INTEGER DEFAULT "0",
  "category_name" TEXT(512) DEFAULT "",
  "description" TEXT(512)
);

-- 书签分享
CREATE TABLE IF NOT EXISTS "user_share" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "sid" TEXT(64) DEFAULT "",
  "name" TEXT(64) DEFAULT "",
  "pwd" TEXT(64) DEFAULT "",
  "add_time" integer(13) DEFAULT "0",
  "up_time" integer(13) DEFAULT "0",
  "expire_time" integer(13) DEFAULT "0",
  "views" integer(13) DEFAULT "0",
  "description" TEXT DEFAULT "",
  "type" integer(1) NOT NULL,
  "data" TEXT,
  "pv" integer(1) DEFAULT "0"
);

-- 图标缓存
CREATE TABLE IF NOT EXISTS "global_icon" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "url_md5" text(32) NOT NULL DEFAULT "",
  "url" text NOT NULL DEFAULT "",
  "ico_url" text NOT NULL DEFAULT "",
  "add_time" integer(10) NOT NULL,
  "update_time" integer(10) NOT NULL,
  "file_name" text NOT NULL DEFAULT "",
  "file_mime" text NOT NULL DEFAULT "",
  "extend" text NOT NULL DEFAULT "",
  CONSTRAINT "id" UNIQUE ("id" ASC)
);

-- 用户文章列表
CREATE TABLE "user_article_list" (
  "id" integer PRIMARY KEY AUTOINCREMENT,
  "uid" integer(10) NOT NULL,
  "title" TEXT NOT NULL DEFAULT "",
  "category" integer NOT NULL,
  "state" integer(1) DEFAULT 0,
  "password" TEXT NOT NULL DEFAULT "",
  "top" integer(10),
  "add_time" integer(10),
  "up_time" integer(10),
  "browse_count" integer DEFAULT 0,
  "summary" TEXT,
  "content" TEXT,
  "cover" TEXT,
  "extend" TEXT,
  CONSTRAINT "id" UNIQUE ("id" ASC)
);