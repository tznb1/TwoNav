<?php $title='链接排序';$awesome=true; require 'header.php';  ?>
<style>
    .layui-table-tool-temp  {padding-right:1px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        
        <div class="layui-inline layui-form" style="padding-bottom: 5px;">
            <label class="layui-form-label " style="width:60px;padding-left: 5px;padding-right: 5px;">分类筛选:</label>
            <div class="layui-input-inline">
                <select id="fid" lay-filter="fid" name="categorys" lay-search><?php echo_category(true); ?></select>
            </div>
        </div>
        
        <table id="table" class="layui-table" lay-filter="table" style="margin: -3px 0;"></table>
    </div>
</div>
<!-- 表头工具栏 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-group">
        <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="refresh">刷新</button>
        <button class="layui-btn layui-btn-sm" lay-event="up_top">到顶</button>
        <button class="layui-btn layui-btn-sm" lay-event="down_bottom">到底</button>
        <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="up_tr">上移</button>
        <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="down_tr">下移</button>
        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="save" id="btn-save">保存</button>
        <button class="layui-btn layui-btn-sm" lay-event="tip" id="btn-save">提示</button>
    </div>
</script>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script src = "./templates/admin/js/link_sort.js?v=<?php echo $Ver;?>"></script>
</body>
</html>