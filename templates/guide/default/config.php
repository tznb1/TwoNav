<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $theme;?> - 主题配置</title>
  <link rel='stylesheet' href='<?php echo $libs?>/Layui/v2.8.3/css/layui.css'>
</head>
<body>
<div class="layui-row" style = "margin-top:18px;">
 <div class="layui-container">
  <div class="layui-col-lg6 layui-col-md-offset3">
   <form class="layui-form" lay-filter="form">

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">页内标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" placeholder="留空则使用默认用户站点配置的主标题" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">页内描述</label>
        <div class="layui-input-block">
            <input type="text" name="p1" placeholder="留空则使用默认用户站点配置的描述" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">背景图</label>
        <div class="layui-input-block">
            <input type="text" name="bg_img" placeholder="请输入背景图URL,留空则使用默认背景图" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item" style="padding-top: 10px;">
     <div class="layui-input-block">
         <button class="layui-btn" lay-submit lay-filter="save">保存</button>
     </div>
    </div>
   </form>
  </div>
 </div>
</div>
<script src = '<?php echo $libs?>/jquery/jquery-3.6.0.min.js'></script>
<script src = '<?php echo $libs?>/Layui/v2.8.3/layui.js'></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>

<script>
var u = '<?php echo $u?>';
var t = '<?php echo $theme;?>';
var s = '<?php echo $_GET['source'];?>';
var api = get_api('write_theme','config') + '&t=' + t;
layui.use(['form'], function(){
    var form = layui.form;
    //表单赋值
    form.val('form', <?php echo json_encode($theme_config);?>);
    
    form.on('submit(save)', function(data){
        $.post(api,data.field,function(data,status){
            if(data.code == 1) {
                if (s == 'admin'){
                    layer.msg(data.msg, {icon: 1});
                    return false;
                }else{
                    layer.msg(data.msg, {icon: 1});
                    setTimeout(() => {parent.location.reload();}, 500);
                }
            }else{
                layer.msg(data.msg, {icon: 5});
            }
            });
    return false; 
    });
});
</script>
</body>
</html>