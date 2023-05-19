<?php ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>极简留言板</title>
  <link rel='stylesheet' href='<?php echo $libs?>/Layui/v2.8.3/css/layui.css'>
  <style>    
    .layui-form-item {
        margin-bottom: 10px;
        height: 38px;
    }
  </style>
</head>
<body>
<div>
<!-- 内容主体区域 -->

<div class="layui-row" style = "margin-top:18px;">
    
  <div class="layui-container">
  <div class="layui-col-lg10 ">
  <form class="layui-form">
  <fieldset class="layui-elem-field layui-field-title " style="margin-top: 30px;"><legend><a href="https://gitee.com/tznb/TwoNav" target="_blank" rel="nofollow">TwoNav</a> 极简留言板</legend></fieldset>
  <div class="layui-form-item">
    <label class="layui-form-label">反馈类型</label>
    <div class="layui-input-inline">
      <select lay-verify="required"  id="type" name="type" lay-search >
        <option value="投诉建议" >投诉建议</option>
        <option value="问题反馈" selected="" >问题反馈</option>
        <option value="商务合作" >商务合作</option>
        <option value="其他" >其他</option>
      </select>
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">联系方式</label>
    <div class="layui-input-inline" >
        <input  id = "contact" name="contact" value = "" placeholder="仅管理员可见" required  lay-verify="required" class="layui-input" maxlength="64">
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">标题</label>
    <div class="layui-input-block" >
        <input  id = "title" name="title" value = "" placeholder="" required  lay-verify="required" class="layui-input" maxlength="128">
    </div>
  </div>
  <div class="layui-form-text">
    <label class="layui-form-label">内容</label>
    <div class="layui-input-block">
        <textarea id = "content" name="content"  rows = "10" class="layui-textarea" required  lay-verify="required" maxlength="2048"></textarea>
    </div>
  </div>
  <div class="layui-form-item" style="padding-top: 10px;">
    <div class="layui-input-block">
      <?php if($s['allow'] == '1'){ echo '<button class="layui-btn" lay-submit lay-filter="Submit">提交</button>';} ?>
    </div>
  </div>
</form>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
  <legend>Powered by <a href="https://gitee.com/tznb/TwoNav" target="_blank" rel="nofollow">lm21</a></legend>
  <!--非订阅用户请勿去除版权,谢谢-->
</fieldset>
</div>
<!-- 内容主题区域END -->
</div>
<script src = '<?php echo $libs?>/jquery/jquery-3.6.0.min.js'></script>
<script src = '<?php echo $libs?>/Layui/v2.8.3/layui.js'></script>
<script>
layui.use(['form'], function(){
    var form = layui.form;
    form.on('submit(Submit)', function(data){
        $.post('',data.field,function(data,status){
            if(data.code == 0) {
                layer.msg(data.msg, {icon: 1});
                setTimeout(() => {location.reload();}, 1000);
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