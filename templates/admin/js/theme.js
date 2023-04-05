
layui.use(['form','miniTab'], function () {
    var form = layui.form,
        layer = layui.layer,
        miniTab = layui.miniTab;
        miniTab.listen();
        layer.photos({photos: '.img-list',anim: 5});
    
    //监听按钮
    $(".layui-btn-group .layui-btn").click(function () {
        var dir= $(this).parent().attr("id");//取目录名key
        var fn= $(this).parent().parent().attr("id");//取模板类型
        var type = $(this).attr("id");//取事件类型
        var data = datas[dir].info;
        //console.log(data);alert('目录:'+dir+',类型:'+type+',模板类型:'+fn);
        
        if(type === 'dw' || type === 'up' ){ //下载或更新
            if (data.desc != null && data.desc.length != 0){ //存在描述时弹窗显示描述
                layer.open({title:data.name,content: data.desc,btn: ['下载', '取消']
                    ,yes: function(index, layero){
                        theme_download(dir,data.name,data.desc,fn);
                    },btn2: function(index, layero){
                        return true;
                    },cancel: function(){ 
                        return true;
                    }
                });
            }else{
                theme_download(dir,data.name,data.desc,fn);
            }
        }else if(type === 'del' ){ //删除
            layer.confirm('确认删除?',{icon: 3, title:'温馨提示'}, function(index){
                theme_del(dir,fn);
            });
            
        }else if(type === 'config' ){ //配置
            theme_config(dir,data.name,fn);
        }else if(type === 'preview' ){ //预览
            if(fn == 'home'){
                window.open('./index.php?theme='+dir+'&u='+u);
            }else{
                layer.msg('不支持预览此模板', {icon: 3});return;
            }
            
        }else if(type === 'set' ){ //使用
            if(fn == 'home'){
                set_theme(dir,data.name,fn);
            }else{
                set_theme2(dir,'',fn);
            }
            
        }else if(type === 'detail' ){ //详情
            theme_detail(data);
        }
        
    //监听End
    })
    
    
});

//懒加载预览图
$('.screenshot').lazyload({placeholder:"./templates/admin/img/loading.gif",threshold : 600});

//下载主题
function theme_download(dir,name,desc,fn){
    layer.load(1, {shade:[0.1,'#fff']});//加载层
    layer.msg('下载安装中,请稍后..', {offset: 'b',anim: 1,time: 60*1000});
    $.post(get_api('write_theme','download'),{dir:dir,name:name,fn:fn},function(data,status){
        layer.closeAll();
        if( data.code == 1 ) {
            layer.msg(data.msg, {icon: 1});
            setTimeout(() => {location.reload();}, 500);//延迟刷新
        }else{
            layer.msg(data.msg, {icon: 5});
        }
    });
}
//删除主题
function theme_del(dir,fn){
    layer.load(1, {shade:[0.1,'#fff']});//加载层
    layer.msg('正在删除,请稍后..', {offset: 'b',anim: 1,time: 60*1000});
    $.post(get_api('write_theme','del'),{dir:dir,fn:fn},function(data,status){
        layer.closeAll();
        if( data.code == 1 ) {
            layer.msg(data.msg, {icon: 1});
            setTimeout(() => {location.reload();}, 500);
        }else{
            layer.msg(data.msg, {icon: 5});
        }
    });
}
//载入主题配置
function theme_config(key,name,fn){
     layer.open({
        type: 2,
        title: name + ' - 主题配置',
        shadeClose: true,
        area : [( $(window).width() < 768 ? '100%' : '568px' ),'100%'],
        scrollbar: false,
        resize: false,
        offset: 'rt',
        content: './index.php?c=admin&page=config_home&u='+u+'&theme='+key+'&fn='+fn+'&source=admin',
    });
}
//使用主题提示框
function set_theme(key,name,fn) {
    layer.open({
        title:name
        ,content: '请选择要应用的设备类型 ?'
        ,btn: ['全部', 'PC', 'Pad']
        ,yes: function(index, layero){
            set_theme2(key,'PC/Pad',fn);
        },btn2: function(index, layero){
            set_theme2(key,'PC',fn);
        },btn3: function(index, layero){
            set_theme2(key,'Pad',fn);
        },cancel: function(){ 
            return true;
        }
    });
}
//使用主题
function set_theme2(name,type,fn) {
    console.log(type,name);
    $.post(get_api('write_theme','set'),{type:type,name:name,fn:fn},function(data,status){
        if( data.code == 1 ) {
            layer.msg(data.msg, {icon: 1});
            setTimeout(() => {location.reload();}, 500);
        }else{
            layer.msg(data.msg, {icon: 5});
        }
    });
}
//主题详情
function theme_detail(data){
    layer.open({type: 1,scrollbar: false,maxmin: false,shadeClose: true,resize: false,title: data.name + ' - 主题详情',area: ['60%', '59%'],content: '<body class="layui-fluid"><div class="layui-row" style = "margin-top:1em;"><div class="layui-col-sm9" style = "border-right:1px solid #e2e2e2;"><div style = "margin-left:1em;margin-right:1em;"><img src="'+data.screenshot+'" alt="" style = "max-width:100%;"></div></div><div class="layui-col-sm3"><div style = "margin-left:1em;margin-right:1em;"><h1>'+data.name+'</h1><p>描述：'+data.description+'</p><p>版本：'+data.version+'</p><p>更新时间：'+data.update+'</p><p>作者：'+data.author+'</p><p>主页：<a style = "color:#01AAED;" href="'+data.homepage+'" target="_blank" rel = "nofollow">访问主页</a></p></div></div></div></body>'});
                    
}