<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>TwoNav - 系统管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="<?php echo $layui['css'];?>" media="all">
    <link rel="stylesheet" href="./templates/admin/css/layuimini.css?v=<?php echo $Ver;?>" media="all">
    <link rel="stylesheet" href="./templates/admin/css/themes/default.css?v=<?php echo $Ver;?>" media="all">
    <link rel="stylesheet" href="<?php echo $libs?>/Font-awesome/4.7.0/css/font-awesome.css" media="all">
    <link rel="shortcut icon" href="<?php echo $favicon;?>">
    <style id="layuimini-bg-color"></style>
</head>
<body class="layui-layout-body layuimini-all">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header">
        <div class="layui-logo layuimini-logo"></div>
        <div class="layuimini-header-content">
            <a><div class="layuimini-tool"><i title="左侧栏展开/收缩" class="fa fa-outdent" style="font-size: 1.1rem;" data-side-fold="1"></i></div></a>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item" lay-unselect><a href="javascript:;" data-home="主页" title="主页"><i class="fa fa-home" style="font-size: 1.08rem;"></i></a></li>
                <li class="layui-nav-item" lay-unselect><a href="javascript:;" data-refresh="刷新" title="刷新"><i class="fa fa-refresh"></i></a></li>
                <li class="layui-nav-item mobile layui-hide-xs" lay-unselect><a href="javascript:;" title="全屏" data-check-screen="full"><i class="fa fa-arrows-alt"></i></a></li>
                <li class="layui-nav-item layuimini-setting">
                    <a href="javascript:;">
                        <!--<img src="./templates/admin/img/head.jpg" class="layui-nav-img" width="50" height="50">-->
                        <?php echo U;?></a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" layuimini-content-href="LoginDevice" data-title="登录设备">登录设备</a></dd>
                        <dd><a href="javascript:;" layuimini-content-href="SecuritySetting" data-title="安全设置">安全设置</a></dd>
                        <dd><a href="javascript:;" layuimini-content-href="UserPassword" data-title="修改密码">修改密码</a></dd>
                        <dd><hr></dd>
                        <dd><a href="javascript:;" id="logout">退出登录</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item layuimini-select-bgcolor" lay-unselect>
                    <a href="javascript:;" data-bgcolor="配色方案"><i class="fa fa-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <!--无限极左侧菜单-->
    <div class="layui-side layui-bg-black layuimini-menu-left"></div>
    <!--手机端遮罩层-->
    <div class="layuimini-make"></div>
    <!-- 移动导航 -->
    <div class="layui-body">
        <div class="layuimini-tab layui-tab-rollTool layui-tab" lay-filter="layuiminiTab" lay-allowclose="true">
            <ul class="layui-tab-title"><li class="layui-this" id="layuiminiHomeTabId" lay-id=""></li></ul>
            <div class="layui-tab-control">
                <li class="layuimini-tab-roll-left layui-icon layui-icon-left"></li>
                <li class="layuimini-tab-roll-right layui-icon layui-icon-right"></li>
                <li class="layui-tab-tool layui-icon layui-icon-down">
                    <ul class="layui-nav close-box">
                        <li class="layui-nav-item">
                            <a href="javascript:;"><span class="layui-nav-more"></span></a>
                            <dl class="layui-nav-child">
                                <dd><a href="javascript:;" layuimini-tab-close="current">关 闭 当 前</a></dd>
                                <dd><a href="javascript:;" layuimini-tab-close="other">关 闭 其 他</a></dd>
                                <dd><a href="javascript:;" layuimini-tab-close="all">关 闭 全 部</a></dd>
                            </dl>
                        </li>
                    </ul>
                </li>
            </div>
            <div class="layui-tab-content"><div id="layuiminiHomeTabIframe" class="layui-tab-item layui-show"></div></div>
        </div>
    </div>
</div>
<script src="<?php echo $layui['js'];?>" charset="utf-8"></script>
<script src="./templates/admin/js/lay-config.js?v=<?php echo $Ver;?>" charset="utf-8"></script>
<script>
var u = "<?php echo U;?>"
layui.config({version:"<?php echo $Ver;?>"});
layui.use(['layer','miniAdmin'], function () {
    var layer = layui.layer;
    layui.miniAdmin.render({
        iniUrl: "./index.php?c=admin&page=menu&u="+u,
        urlHashLocation: true,
        bgColorDefault: false,
        menuChildOpen: true,
        pageAnim: true,
        maxTabNum: 30
    });
});
</script>
</body>
</html>
