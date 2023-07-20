<?php $title='添加链接'; require 'header.php';  ?>
<style>
    body {
        margin: 0px 0px 0px 0px; 
        background: bottom; 
    }
    .layui-textarea {min-height: 70px;}
    .layui-form-select dl {max-height: 190px;}
</style>
<div class="layuimini-container">
    <div class="layuimini-main" style="margin-left: 0px;">
     <form class="layui-form layuimini-form" style="padding-bottom: 20px;">
         
        <div class="layui-form-item">
            <label class="layui-form-label required" >URL</label>
            <div class="layui-input-block">
                <input type="url" id = "url" name="url" required  lay-verify="required" placeholder="请输入有效链接" autocomplete="off" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label required">链接名称</label>
            <div class="layui-input-block">
                <input type="text" id = "title" name="title" required  lay-verify="required" placeholder="请输入链接名称" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">所属分类</label>
            <div class="layui-input-block">
                <select name="fid" id="fid" lay-verify="required">
                    <option value=""></option><?php echo_category(true); ?>
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
            <label class="layui-form-label">关键字</label>
            <div class="layui-input-block">
                <input type="text" id="keywords" name="keywords" placeholder="可留空" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">描述</label>
            <div class="layui-input-block">
                <textarea name="description" id="description" placeholder="可留空" class="layui-textarea"></textarea>
            </div>
        </div>
    
    
        <div class="layui-form-item">
            <div class="layui-input-block layui-btn-group">
                <button type="reset" class="layui-btn layui-btn-normal">重置</button>
                <button class="layui-btn layui-btn-normal" type="button" onclick="get_link_info()" >识别</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="add_link">添加</button>
            </div>
        </div>
     </form>
    </div>
</div>

<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script src = "./templates/admin/js/link.js?v=<?php echo $Ver;?>"></script>
</body>
</html>