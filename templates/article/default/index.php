<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <meta name="renderer" content="webkit"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=5.0">
  <meta http-equiv="Cache-Control" content="no-siteapp"/>
  <title><?php echo $data['title'];?> - <?php echo $site['title']; ?></title>
  <meta name="keywords" content="<?php echo $data['summary']; ?>">
  <meta name="description" content="<?php echo $data['summary']; ?>">
  <link rel="shortcut icon" href="<?php echo $favicon;?>">
  <link rel="stylesheet" href="<?php echo $libs?>/MDUI/v1.0.1/css/mdui.min.css">
  <link rel="stylesheet" href="<?php echo $theme_dir?>/index.css">
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-indigo mdui-theme-accent-pink mdui-theme-layout-auto">
<header class="appbar mdui-appbar mdui-appbar-fixed">
  <div class="mdui-toolbar mdui-color-theme">
    <span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#main-drawer', swipe: true}">
      <i class="mdui-icon material-icons">menu</i>
    </span>
    <a href="" class="mdui-typo-headline mdui-hidden-xs"><?php echo $site['logo'];?></a>
    <a href="" class="mdui-typo-title"><?php echo $data['title'];?></a>
    <div class="mdui-toolbar-spacer"></div>
  </div>
</header>
<div class="mdui-drawer" id="main-drawer">
    <div class="mdui-collapse-item-header mdui-list-item mdui-ripple" data_id="to_top">
        <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-blue">&#xe25a;</i>
        <div class="mdui-list-item-content">文章开始</div>
    </div>
    <div class="mdui-collapse-item-header mdui-list-item mdui-ripple" data_id="to_bottom" id="to_bottom">
        <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-blue">&#xe258;</i>
        <div class="mdui-list-item-content">文章结尾</div>
    </div>
</div>
<div class="container p-download mdui-container" style="max-width: <?php echo $theme_config['container_width'];?> ;">
    <?php echo $data['content'];?>
</div>
<script src="<?php echo $libs?>/MDUI/v1.0.1/js/mdui.min.js"></script>
<script src="<?php echo $libs?>/jquery/jquery-3.6.0.min.js"></script>
<script src="<?php echo $theme_dir?>/index.js"></script>
</body>
</html>