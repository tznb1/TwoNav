<?php $title='分类管理';$awesome=true; require 'header.php';  ?>
<style>
    .layui-table-tool-temp  {padding-right:1px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="table" class="layui-table" lay-filter="table" style="margin: 1px 0;"></table>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="tablebar">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<script type="text/html" id="pwd">
        {{# if(d.pwd_id!=''){ return '<a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="pwd">查看</a>' } }}
</script>

<!-- 表头工具栏 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-group">
        <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="type" id="btn-type">收起</button>
        <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="refresh">刷新</button>
        <button class="layui-btn layui-btn-sm " lay-event="add">新增</button>
        <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="uptr">上移</button>
        <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="downtr">下移</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="save" id="btn-save">保存</button>
    </div>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script src = "./templates/admin/js/category.js?v=<?php echo $Ver;?>"></script>
<!--添加/编辑共用-->
<ul class="add" style = "margin-top:18px;display:none;padding-right: 10px;" >
    <form class="layui-form layuimini-form" lay-filter="add">
        <input type="text" name="type" id="type" value="add" style = "display:none;">
        <input type="text" name="cid" id="cid" value="" style = "display:none;">
        <div class="layui-form-item">
            <label class="layui-form-label required">分类名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" id="name" lay-verify="required" lay-reqtext="分类名称不能为空" placeholder="请输入分类名称"  class="layui-input">
            </div>
        </div>
    
        <div class="layui-form-item">
            <label class="layui-form-label">分类图标</label>
            <div class="layui-input-block">
                <input type="text" name="font_icon" id="font_icon" value="fa fa-star-o" class="hide">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">个性图标</label>
            <div class="layui-input-block">
                <textarea name="icon" id = "icon" placeholder="支持URL地址/SVG代码/Base64代码等,(目前仅六零系主题支持)如果没有，请留空" class="layui-textarea" ></textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">父级分类</label>
            <div class="layui-input-block">
                <select name="fid" id="fid">
                    <option value="0">无</option>
                </select> 
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">分类加密</label>
            <div class="layui-input-block">
                <select name="pwd_id" id="pwd_id">
                    <option value="0">无</option>
                    <?php echo_pwds(); ?>
                </select> 
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">是否私有</label>
            <div class="layui-input-block">
                <input type="checkbox" name="property" value = "1" lay-skin="switch" lay-text="是|否">
            </div>
        </div>
  
        <div class="layui-form-item">
            <label class="layui-form-label">描述</label>
            <div class="layui-input-block">
                <textarea name="description" id="description" placeholder="请输入内容" class="layui-textarea"></textarea>
            </div>
        </div>
    
        <div class="layui-form-item" id="continuity_f">
            <div class="layui-input-block">
                <input type="checkbox" id="continuity" lay-skin="primary" title="连续添加">
            </div>
        </div>
    
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="save" id="save" >保存</button>
            </div>
        </div>
  </form>
</ul>
</body>
</html>