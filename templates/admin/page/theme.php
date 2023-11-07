<?php $title='主题管理';$awesome=true; define('referrer','same-origin'); require 'header.php';?>
<style>
.tab-header .layui-btn.layui-this{border-color: #1E9FFF; color: #1E9FFF;}
.screenshot{
    width: 99%;  
    height: 99%;  
    max-width: 100%;  
    max-height: 100%;   
    aspect-ratio:16/9;
}
.layui-btn-container .layui-btn {
    margin-right: 5px;
}
#default #del {display: none;}
</style>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="tab-header layui-btn-container" id="tab" style="margin-left: 5px;"></div>
        <div class="layui-bg-gray" style="padding: 1px;" >
            <div class="layui-row layui-col-space15"></div>
        </div>
    </div>
</div>
<script src="<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src="<?php echo $libs;?>/jquery/jquery.lazyload.min.js"></script>
<script src="./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>
    const is_admin = <?php echo $USER_DB['UserGroup'] === 'root' ? 'true' : 'false'; ?>;
    const theme_set = <?php echo check_purview('theme_set',1) ? 'true' : 'false'; ?>;
    const apply = <?php echo check_purview('apply',1) ? 'true' : 'false'; ?>;
    const guestbook = <?php echo check_purview('guestbook',1) ? 'true' : 'false'; ?>;
    const article = <?php echo check_purview('article',1) ? 'true' : 'false'; ?>;
    const loginAddress = '<?php echo $USER_DB['Login']; ?>';
</script>
<script src="./templates/admin/js/theme.js?v=<?php echo $Ver;?>"></script>