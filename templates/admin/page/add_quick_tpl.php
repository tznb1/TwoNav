<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
	<meta http-equiv="Cache-Control" content="no-transform">
	<meta name="applicable-device" content="pc,mobile">
	<meta name="MobileOptimized" content="width">
	<meta name="HandheldFriendly" content="true">
	<title>快速添加</title>
	<link rel="stylesheet" type="text/css" href="./templates/admin/css/add_quick_tpl.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $libs?>/Layui/v2.8.3/css/layui.css" />
</head>
<body>
	<div class="quick-main">
		<div class="title"><i class="layui-icon layui-icon-add-1"></i>快速添加当前连接</div>
		<form class="layui-form" lay-filter="form">
			<div class="list"><input type="text" name="url" id="url" required  lay-verify="required" placeholder="URL" autocomplete="off"></div>
			<div class="list"><input type="text" name="title" id="title" required  lay-verify="required" placeholder="标题" autocomplete="off"></div>
			<div class="list">
			    <select name="fid" lay-verify="required" lay-search>
                    <option value=""></option><?php echo_category(true); ?>
                </select>
			</div>
			<div class="list list-2"><div class="li">是否私有<input type="checkbox" lay-skin="switch" lay-text="是|否" name="property" value = "1"></div></div>
			<div class="list">
				<textarea name="description" id = "description" placeholder="请输入站点描述（选填）" ></textarea>
			</div>
			<div class="list-3">
				<button class="close">关闭</button>
				<button id="add_link" lay-submit lay-filter="add_link">添加</button>
			</div>
		</form>
	</div>
    <script src="<?php echo $libs?>/jquery/jquery-3.6.0.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $libs?>/Layui/v2.8.3/layui.js" type="text/javascript" charset="utf-8"></script>
	<script>
		var u = '<?php echo $u?>';
		var Auto_Off = Get('Auto_Off');
		var Auto_add = Get('Auto_add');
		var property = Get('property');
		layui.use('form', function() {
			var form = layui.form;
			var urls = decodeURIComponent(Get("url"));
			var titles = decodeURIComponent(Get("title"));
			$('input#url').val(urls);
			$('input#title').val(titles);
			$('button.close').click(function() {window.close();});
			form.val('form',{'fid':Get('fid'),'property':property == 1});
			form.on('submit(add_link)', function(data) {
			    $.post('./index.php?c=api&method=write_link&type=add&u='+u, data.field, function(data, status) {
			        if(data.code == 1) {
			            layer.msg('保存成功', {icon: 1,time: 5000});
			            if(Auto_Off > 0){
			                setTimeout(() => {window.close();}, Auto_Off);
						}
					}else{
						layer.msg(data.msg, {icon: 5});
					}
				});
				return false;
			});
			if(Auto_add == 1 && Get('fid') > 0){
                $("button#add_link").click();
                return false;
            }
		});
		function Get(variable) {
			var query = window.location.search.substring(1);
			var vars = query.split("&");
			for(var i = 0; i < vars.length; i++) {
				var pair = vars[i].split("=");
				if(pair[0] == variable) {
					return pair[1];
				}
			}
			return false;
		};
	</script>
</body>
</html>