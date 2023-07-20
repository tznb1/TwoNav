<?php $title='添加链接';$awesome=true; require 'header.php';
?>
<style>
    .layui-textarea {min-height: 70px;}
    .obtn {padding-left: 75px;}
    span .layui-btn{padding: 0 11px;}
    .layuimini-upload-show{margin-top:10px;margin-bottom:0}
    .layuimini-upload-show li{position:relative;display:inline-block;padding:5px 0 5px 0;padding-left:10px;padding-right:10px;border:1px solid #e2e2e2}
    .layuimini-upload-show a img{height:80px;object-fit:cover}
    .layuimini-upload-show .uploads-delete-tip{position:absolute;right:10px;font-size:12px}
    .bg-red{background-color:#e74c3c!important}
    .color-red{color:#e74c3c!important}
    .badge{display:inline-block;min-width:10px;padding:3px 7px;font-size:11px;font-weight:700;color:#fff;line-height:1;vertical-align:middle;white-space:nowrap;text-align:center;background-color:#777;border-radius:10px}
</style>
<div class="layuimini-container">
    <div class="layuimini-main">
     <form class="layui-form layuimini-form" lay-filter="form">
         
        <div class="layui-form-item">
            <label class="layui-form-label required" >主链接</label>
            <div class="layui-input-block">
                <input type="url" id = "url" name="url"  lay-verify="required" placeholder="请输入有效链接" autocomplete="off" class="layui-input obtn">
                <div style="position: absolute; top:0px;">
                    <span><a class="layui-btn layui-btn-primary" id="add_standby_url"><i class="fa fa-plus"></i> 备用</a></span>
                </div>
            </div>
        </div>
        <div id='backup_link'></div>
        <div class="layui-form-item">
            <label class="layui-form-label required">链接名称</label>
            <div class="layui-input-block">
                <input type="text" id = "title" name="title" lay-verify="required" placeholder="请输入链接名称" autocomplete="off" class="layui-input obtn">
                <div style="position: absolute; top:0px;">
                    <span><a class="layui-btn layui-btn-primary" onclick="get_link_info()"><i class="fa fa-pencil"></i> 识别</a></span>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">所属分类</label>
            <div class="layui-input-block">
                <select name="fid" id="fid" lay-verify="required">
                    <?php echo_category(true); ?>
                </select> 
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">链接加密</label>
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
            <label class="layui-form-label">链接图标</label>
            <div class="layui-input-block layuimini-upload" >
                <input type="text" id = "icon" name="icon" autocomplete="off" class="layui-input obtn" placeholder="自定义链接图标,留空则使用API接口!">
                <div style="position: absolute; top:0px;">
                    <span><a class="layui-btn layui-btn-primary" id="<?php echo check_purview('Upload_icon',1)?'up_icon':'no_purview'?>"><i class="fa fa-upload"></i> 上传</a></span>
                </div>
                
            </div>
            <ul class="layui-input-block layuimini-upload-show" id='ico_preview' style="display:none;">
                <li>
                    <a><img id="icon_img" src="" ></a>
                    <small id="del_icon" class="uploads-delete-tip bg-red badge" style="cursor:pointer;">×</small>
                </li>
            </ul>
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
<?php 
//判断全局是否开启扩展
if($global_config['link_extend'] && check_purview('link_extend',1)){
    require 'link_extend.php';
}?>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="checkbox" id="continuity" lay-skin="primary" title="连续添加">
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block layui-btn-group">
                <button class="layui-btn layui-btn-warm" type="button" id="close" >关闭</button>
                <button type="reset" class="layui-btn layui-btn-normal" id="reset">重置</button>
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="add_link" id="add_link">添加</button>
            </div>
        </div>
     </form>
    </div>
</div>

<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<script>
var form_data = {};
</script>
<?php load_static('js');?>
<script src = "./templates/admin/js/link.js?v=<?php echo $Ver;?>"></script>
</body>
</html>