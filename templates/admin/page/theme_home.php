<?php $title='主题设置';$awesome=true; require 'header.php'; ?>
<style type="text/css">
.screenshot{
    width: 99%;  
    height: 99%;  
    max-width: 100%;  
    max-height: 100%;   
    aspect-ratio:16/9;
}
</style> 
<body>
<div class="layuimini-container">
  <div class="layuimini-main">
    <blockquote class="layui-elem-quote layuimini-form" style="margin-top: 0px;border-left: 5px solid <?php echo $cache?"#1e9fff":($global_config['offline']?"":"#639d11") ?>;padding: 6px;">
        <span class="layui-breadcrumb" lay-separator="|">
            <a href="./index.php?c=admin&page=theme_home&cache=no&u=<?php echo U;?>" >刷新数据</a>
            <a href="javascript:;" layuimini-content-href="theme_login" data-title="登录模板">登录模板</a>
            <a href="javascript:;" layuimini-content-href="theme_transit" data-title="过渡模板">过渡模板</a>
            <?php  if($USER_DB['UserGroup'] === 'root'){echo '<a href="javascript:;" layuimini-content-href="theme_register" data-title="注册模板">注册模板</a>';} ?>
            
        </span>
    </blockquote>
    <div class="layui-bg-gray" style="padding: 1px;" >
        <div class="layui-row layui-col-space15">
<?php 
$Space = '                            ';//占位符,强迫症想让输出的源码好看点而已...
foreach ($themes as $key => $theme) { 
$online = !empty($theme['info']['md5']); //在线主题!
if($s_templates['home_pc'] == $key && $s_templates['home_pad'] == $key){
    $icon ='<i class="layui-icon layui-icon-cellphone" style="color: #03a9f4;" title = "移动终端正在使用此主题"> </i><i class="fa fa-tv" style="color: #03a9f4;" title = "PC终端正在使用此主题"></i> ';
}elseif($s_templates['home_pc'] == $key){
    $icon ='<i class="fa fa-tv" style="color: #03a9f4;" title = "PC终端正在使用此主题"></i> ';
}elseif($s_templates['home_pad'] == $key){
    $icon ='<i class="layui-icon layui-icon-cellphone" style="color: #03a9f4;" title = "移动终端正在使用此主题"></i> ';
}else{
    $icon ='';
}
$color = ($s_templates['home_pc'] == $key || $s_templates['home_pad'] == $key ?"color: #03a9f4;":"");
?>
            <!--主题卡片--> 
            <div class="layui-col-xs layui-col-sm4 layui-col-md3 ">
                <div class="layui-card">
                    <div class="layui-card-header"> 
                        <div style="float:left; cursor:pointer;<?php echo $color; ?>" title="<?php echo $key; ?>"><?php echo $icon.$theme['info']['name']; ?></div>
                        <div style="float:right;cursor:pointer;" title="<?php echo $theme['info']['update']; ?>"><?php echo $theme['info']['version']; ?></div>
                    </div>
                    <div class="layui-card-body">
                        <div class="img-list"><img class="screenshot" layer-src="<?php echo $theme['info']['screenshot']; ?>" data-original="<?php echo $theme['info']['screenshot']; ?>"></div>
                    </div>
                    <div class="layui-card-header" style="height: 1px;"></div>
                    <div class="layui-card-header" style="height: auto;" id="home">
                        <div class="layui-btn-group" id="<?php echo $key;?>">
<?php 
    if($online){ //如果是在线主题则显示下载
        echo $Space.'<button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="dw">下载</button>'."\n";
    }elseif($theme['info']['up'] == 1){ //如果有更新则同时显示下载和使用
        echo $Space.'<button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="up">更新</button>'."\n";
        echo $Space.'<button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="set">使用</button>'."\n";
    }else{ //其他情况仅显示使用
        echo $Space.'<button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="set">使用</button>'."\n";
    }
        echo $Space.'<button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="detail">详情</button>'."\n";
    if(!$online){ //本地主题显示预览
        echo $Space.'<button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="preview">预览</button>'."\n";
    }
    if($theme['info']['config'] == '1'){ //支持配置的主题显示配置
        echo $Space.'<button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="config">配置</button>'."\n";
    }
    if($USER_DB['UserGroup'] === 'root' && !$online){ //管理员&本地主题>显示删除
        echo $Space.'<button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="del">删除</button>'."\n";
    }
?>
                        </div>
                    </div>
                </div>
            </div>
            <!--主题卡片End-->
<?php }?>            
            
        </div> 
    </div>
  </div>
</div>
<script src = "<?php echo $libs;?>/jquery/jquery-3.6.0.min.js"></script>
<script src = "<?php echo $libs;?>/jquery/jquery.lazyload.min.js"></script>
<script src = "./templates/admin/js/public.js?v=<?php echo $Ver;?>"></script>
<?php load_static('js');?>
<script>var datas = <?php echo json_encode($themes)?>;</script>
<script src = "./templates/admin/js/theme.js?v=<?php echo $Ver;?>"></script>
</body>
</html>