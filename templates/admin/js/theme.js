layui.use(function(){
    var datas,local_theme,active;
    var buttons = [
      {'name':'主页模板','dir':'home','display':true},
      {'name':'过渡模板','dir':'transit','display':true},
      {'name':'登录模板','dir':'login','display':true},
      {'name':'验证模板','dir':'verify','display':true},
      {'name':'收录模板','dir':'apply','display':apply},
      {'name':'留言模板','dir':'guestbook','display':guestbook},
      {'name':'文章模板','dir':'article','display':article},
      {'name':'注册模板','dir':'register','display':is_admin},
      {'name':'引导页模板','dir':'guide','display':is_admin}
    ];
    var $tab = $('#tab');
    $tab.append('<button class="layui-btn layui-btn-primary layui-border-green layui-btn-sm" id="refresh" title="刷新数据"><i class="layui-icon layui-icon-refresh"></i></button>');
    $tab.append('<button class="layui-btn layui-btn-primary layui-border-green layui-btn-sm" id="tips" title="提示信息"><i class="layui-icon layui-icon-tips"></i></button>');
    $tab.append('<button class="layui-btn layui-btn-primary layui-border-green layui-btn-sm" style="display: none;" id="set_up" title="设置"><i class="layui-icon layui-icon-set"></i></button>');
    buttons.forEach(item => {
        if(item.display){
            $tab.append(`<button class="layui-btn layui-btn-sm layui-btn-primary dir" dir="${item.dir}">${item.name}</button>`);
        }
    });
    
    var tag_btns = $('#tab .dir'); 
    local_theme = localStorage.getItem(u + "_theme_active") || 'home'; 
    local_theme = tag_btns.filter('[dir="' + local_theme + '"]');
    active =  local_theme.length > 0 ? local_theme : tag_btns.first();
    $(active).addClass('layui-this'); //激活第一个
    active = $(active).attr('dir'); //取激活的dir
    load_data(active); //加载数据
    //刷新按钮
    $('#refresh').click(function() {
        load_data(active,true);
    });
    //预览按钮
    $("#preview").click(function() {
        window.open(`./index.php?c=${loginAddress}&u=${u}`); 
    });
    //提示信息
    $("#tips").click(function() {
        let tip,url;
        let title = $("#tab .layui-this:first").text();
        if(active == 'home'){
            tip = '部分模板来自其它开源项目, 本程序仅做适配 <br />主题版权归原作者所有, 如有问题请联系! <br />注意: 部分模板可能不支持书签分享';
        }else if(active == 'login'){
            tip = '只有使用您的专属登录入口时才会生效,即:概要页面中的专属地址>登录';
            url = `./index.php?c=${loginAddress}&u=${u}`;
        }else if(active == 'verify'){
            tip = '验证加密链接/加密分类/二级密码的页面样式';
        }else if(active == 'apply'){
            tip = '收录页面的样式,需在收录管理>设置>申请收录>开启';
            url = `./index.php?c=apply&u=${u}`;
        }else if(active == 'guestbook'){
            tip = '留言板的页面样式,需在留言管理>当前设置>允许留言(点击蓝字切换)';
            url = `./index.php?c=guestbook&u=${u}`;
        }else if(active == 'article'){
            tip = '浏览文章页面的样式,前端显示样式与后端编辑器不一致属正常现象!';
        }else if(active == 'register'){
            tip = '注册页面的样式';
        }else if(active == 'guide'){
            tip = '引导页面的样式,需将系统设置>默认页面>改为引导页面 <br />未登录时直接访问域名显示引导页 <br />登录后将显示用户主页';
        }
        if(url != undefined){
            layer.alert(tip, {title:title,shadeClose: true,anim: 2,closeBtn: 0,
                btn: ['预览', '确定'],btn1: function(){
                    layer.closeAll();
                    setTimeout(function() { window.open(url) }, 288);
                }
            });
        }else{
            layer.alert(tip,{title:title,shadeClose: true,anim: 2,closeBtn: 0});
        }
    });
    
    //设置(目前仅用于过渡页)
    $('#set_up').click(function() {
        if(active == 'transit'){
            layer_open2('过渡页面设置',`/?c=admin&page=set_transit&u=${u}`);
        }else if(active == 'verify'){
            layer_open2('过渡页面设置',`/?c=admin&page=set_verify&u=${u}`);
        }
    });
    
    //切换tab按钮 
    tag_btns.click(function() {
        const dir = $(this).attr('dir');
        if(active == dir) return;
        active = dir;
        tag_btns.removeClass('layui-this').filter(this).addClass('layui-this');
        load_data(active);
        localStorage.setItem(u + "_theme_active",active);
    });
    
    function layer_open2(title,url) {
        layer.open({type: 2,title: title,shadeClose: true,area : ['100%','100%'],scrollbar: false,resize: false,content: url});
    }
    //加载数据
    function load_data(dir,cache = false) {
        const set_up = (dir == 'transit' || dir == 'verify');
        $("#set_up")[ set_up ? "show" : "hide"]();
        $("#tips")[ !set_up ? "show" : "hide"]();
        layer.load(1, {shade: [0.5,'#fff']});//加载层
        layer.msg('正在获取数据..', {icon: 16,time: 1000*300});
        $.post(`./index.php?c=api&method=read_theme&dir=${dir}&u=${u}&cache=${cache ? 'no':'yes'}`, function (r, status) {
            layer.closeAll();
            if (r.code == 1) {
                datas = r.data;
                render_data(r);
            } else {
                layer.alert("获取数据失败,请重试!",{icon:5,title:'错误',anim: 2,closeBtn: 0,btn: ['刷新页面']},function () {location.reload();});
            }
        }).fail(function () {
            layer.alert("获取数据异常,请重试!",{icon:5,title:'错误',anim: 2,closeBtn: 0,btn: ['刷新页面']},function () {location.reload();});
        });
    }
    
    //渲染数据
    function render_data(d){
        $row = $('.layui-row');
        $row.html('');
        for (const key in d.data) {
            const t = d.data[key];
            let upordw = '';
            if(is_admin){
                if(t.state == 'dw' || t.state == 'up'){
                    upordw = `<button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="${t.state}">${t.state == 'dw' ? '下载' : '更新' }</button>`;
                }
            }
            
            let html = 
                `<div class="layui-col-xs layui-col-sm4 layui-col-md3" id="col_${key}">
                    <div class="layui-card">
                        <div class="layui-card-header"> 
                            <div clas="left" style="float:left; cursor:pointer;" title="${key}" id="t_${key}">${t.name}</div>
                            <div style="float:right;cursor:pointer;" title="${t.update}">${t.version}</div>
                        </div>
                        <div class="layui-card-body">
                            <div class="img-list"><img class="screenshot" layer-src="${t.screenshot}" data-original="${t.screenshot}"></div>
                        </div>
                        <div class="layui-card-header" style="height: 1px;"></div>
                        <div class="layui-card-header" style="height: auto;" id="${active}">
                            <div class="layui-btn-group" id="${key}">
                                ${upordw}
                                ${t.state == 'local' ||  t.state == 'up' ? '<button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="set">使用</button>':''}
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="detail">详情</button>
                                ${(t.state == 'local' || t.state == 'up') &&  active == 'home' ? '<button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="preview">预览</button>':''} 
                                ${t.config == '1' && theme_set == true ? '<button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="config">配置</button>':''} 
                                ${(t.state == 'local' || t.state == 'up' ) && is_admin == true ? '<button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="del">删除</button>':''} 
                            </div>
                        </div>
                    </div>
                </div>`;
            $row.append(html);
        }
        
        //标记当前模板,使用中靠前显示
        if(active == 'home'){
            var current1 = $(`#t_${d.current.home_pc}`);
            current1.css('color','#03a9f4');
            current1.prepend('</i><i class="fa fa-tv" title="PC终端正在使用此主题"></i> ');
            $(`#col_${d.current.home_pc}`).prependTo($row);
            var current2 = $(`#t_${d.current.home_pad}`);
            current2.css('color','#03a9f4');
            current2.prepend('<i class="layui-icon layui-icon-cellphone" title="移动终端正在使用此主题"> ');
            $(`#col_${d.current.home_pad}`).prependTo($row);
            //if(current1.is(current2)){ $("#set:first").remove(); }
            
        }else{
            if(d.current[active] !== null && d.current[active] !== undefined && d.current[active].length > 0){
                var current = $(`#t_${d.current[active]}`);
                current.css('color','#03a9f4');
                current.prepend('<i class="fa fa-magic" style="color: #03a9f4;" title="正在使用"></i> ');
                $(`#col_${d.current[active]}`).prependTo($row);
                //$("#set:first").remove();
            }
        }
        $(`#col_default`).prependTo($row);
        //点击图片放大
        layer.photos({photos: '.img-list',anim: 5});
        
        //懒加载预览图
        $('.screenshot').lazyload({placeholder:"./templates/admin/img/loading.gif",threshold : 600});
        
        //监听按钮
        $(".layui-btn-group .layui-btn").click(function () {
            var dir= $(this).parent().attr("id");//取目录名key
            var fn= $(this).parent().parent().attr("id");//取模板类型
            var type = $(this).attr("id");//取事件类型
            var data = datas[dir];
            //console.log('目录:'+dir+',类型:'+type+',模板类型:'+fn);
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
        });
    }
    
    //下载主题
    function theme_download(dir,name,desc,fn){
        layer.msg('下载安装中,请稍后..', {shade:[0.5,'black'],anim: 1,icon: 16,time: 1000*300});
        $.post(get_api('write_theme','download'),{dir:dir,name:name,fn:fn},function(data,status){
            layer.closeAll();
            if( data.code == 1 ) {
                layer.msg(data.msg, {icon: 1});
                setTimeout(() => {load_data(active);}, 800);
            }else{
                layer.alert(data.msg,{icon:5,title:"错误",anim: "slideDown",shadeClose: true,closeBtn: 0,btn: ['知道了']});
            }
        });
    }
    
    //删除主题
    function theme_del(dir,fn){
        layer.load(1, {shade:[0.5,'black']});//加载层
        layer.msg('正在删除,请稍后..', {offset: 'b',anim: 1,time: 60*1000});
        $.post(get_api('write_theme','del'),{dir:dir,fn:fn},function(data,status){
            layer.closeAll();
            if( data.code == 1 ) {
                layer.msg(data.msg, {icon: 1});
                setTimeout(() => {load_data(active);}, 800);
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
            area : [( $(window).width() < 768 ? '100%' : '666px' ),'100%'],
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
        $.post(get_api('write_theme','set'),{type:type,name:name,fn:fn},function(data,status){
            if( data.code == 1 ) {
                layer.msg(data.msg, {icon: 1});
                setTimeout(() => {load_data(active);}, 800);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        });
    }
    
    //主题详情
    function theme_detail(data){
        layer.open({type: 1,scrollbar: false,maxmin: false,shadeClose: true,resize: false,title: data.name + ' - 主题详情',area: ['60%', '59%'],content: '<body class="layui-fluid"><div class="layui-row" style = "margin-top    :1em;"><div class="layui-col-sm9" style = "border-right:1px solid #e2e2e2;"><div style = "margin-left:1em;margin-right:1em;"><img src="'+data.screenshot+'" alt="" style = "max-width:100%;"></div></div><div class    ="layui-col-sm3"><div style = "margin-left:1em;margin-right:1em;"><h1>'+data.name+'</h1><p>描述：'+data.description+'</p><p>版本：'+data.version+'</p><p>更新时间：'+data.update+'</p><p>作者：'+data.author+'</p><p    >主页：<a style = "color:#01AAED;" href="'+data.homepage+'" target="_blank" rel = "nofollow">访问主页</a></p></div></div></div></body>'});
                        
    }
});