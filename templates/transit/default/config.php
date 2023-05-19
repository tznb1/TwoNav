<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $theme;?> - 主题配置</title>
  <link rel='stylesheet' href='<?php echo $libs?>/Layui/v2.8.3/css/layui.css'>
  <style>    
    .layui-form-item {
        margin-bottom: 10px;
    }
    .setting-msg{
        width:100%;
        color: #FF5722;
        border-left: 3px solid #FF5722;
        background-color: #F0F0F0;
        padding:0.8em;
        border-radius: 1px;
        margin-bottom:2em;
        -moz-box-sizing: border-box; /*Firefox3.5+*/
        webkit-box-sizing: border-box; /*Safari3.2+*/
        o-box-sizing: border-box; /*Opera9.6*/
        ms-box-sizing: border-box; /*IE8*/
        box-sizing: border-box; 
    }
    .setting-msg a{
        color:#01AAED;
    }
  </style>
</head>
<body>
<div class="layui-row" style = "margin-top:18px;">
 <div class="layui-container">
  <div class="layui-col-lg6 layui-col-md-offset3">
   <form class="layui-form layui-form-pane" lay-filter="form">

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">过渡页菜单(订阅可用)</label>
        <div class="layui-input-block">
            <textarea name = "menu" placeholder="请参考帮助文档进行设置！" rows = "4" class="layui-textarea"></textarea>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">广告1(订阅可用)</label>
        <div class="layui-input-block">
            <textarea name = "a_d_1" placeholder="请参考帮助文档进行设置！" rows = "2" class="layui-textarea"></textarea>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">广告2(订阅可用)</label>
        <div class="layui-input-block">
            <textarea name = "a_d_2" placeholder="请参考帮助文档进行设置！" rows = "2" class="layui-textarea"></textarea>
        </div>
    </div>

    <div class="layui-form-item" style="padding-top: 10px;">
     <div class="layui-input-block">
         <button class="layui-btn" lay-submit lay-filter="save">保存</button>
         &nbsp; <a href="https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=7968712&doc_id=3767990" target = "_blank" >过渡页使用说明</a>
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