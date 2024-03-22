layui.use(['layer','miniTab'], function(){
    var layer = layui.layer;
    var $ = layui.$;
    var miniTab = layui.miniTab;
    
    $("#new_ver").append('<span id="sysup" style="cursor:pointer;color:darkgray;">&nbsp;更新系统&nbsp;</span> ');
    $("#new_ver").append('<i class="fa fa-spinner fa-spin update" style="cursor:pointer;color: rgb(127 137 141);"     title="正在检测更新中"></i>');
    
    
    // 获取最新信息
    $.post(get_api('other_services','get_notice') + '&t=' + Math.round(new Date() / 1000),function(data,status){
        if(data.code == 200) {
            $("#new_ver a").text(data.version);
            $('#notice_link').text('');
            if (Array.isArray(data.notice) && data.notice.length > 0) {  
                data.notice.forEach(notice => {
                    $('#notice_link').append(`<div class="layuimini-notice"><div class="layuimini-notice-title"><a href="${notice.url}" target="_blank">${notice.title}</a></div></div>`);
                });
            }else{
                $('.notice1').remove();  
            }
            if(data.message.length > 0){
                $('#notice_text').html(data.message);
            }else{
                $('.notice2').remove();  
            }
            
        }
        init_update();
        $(".update").remove();
    }).fail(function () {
        $(".update").remove();
        layer.msg('请求失败', {icon: 5});
    });
    
    function init_update(){
        //获取最新版本
        let latest_version = $("#new_ver").text();

        //获取当前版本 
        let current_version = $("#ver").text();

        let pattern = /v(\d+\.\d+\.\d+)/;
        current_version = pattern.exec(current_version)[0];
        latest_version = pattern.exec(latest_version)[0];
    
        //如果当前版本小于最新版本，则提示更新
        if( current_version < latest_version ) {
            $("#sysup").css("color", "red");
            if($("#layuiminiHomeTabId",parent.document).attr('class') == 'layui-this'){
                $('html,body').animate({scrollTop : $("#msg").offset().top - 20});
                layer.tips("点击此处更新到最新版","#sysup",{tips: [3, "#ff5722"],time: 60*1000,anim: 6});
                layer.msg(' 检测到新版本,请尽快更新 ', {offset: 'b',anim: 6,time: 60*1000});
            }
            //点击更新事件
            $('#sysup').on('click', function(){
                let tip = layer.open({
                    title:"系统更新"
                    ,content: "1.更新有风险请备份后再更新<br />2.更新后检查主题是否可更新<br />3.更新时请勿有其他操作<br />4.更新时请勿刷新或关闭页面<br />5.确保所有文件(夹)是可写权限"
                    ,btn: ['确定更新', '更新内容', '取消']
                    ,yes: function(index, layero){
                        let fail = false;
                        let up_info = {'code':0};
                        let i=0;
                        layer.close(tip);
                        layer.load(1, {shade:[0.3,'#fff']});//加载层
                        let msg_id = layer.msg('正在准备更新,请勿操作.', {icon: 16,time: 1000*300});
                        //设置同步模式
                        $.ajaxSetup({ async : false }); 
                        
                        //获取更新信息
                        $.post(get_api("other_upsys"),{"i":0}, function(data, status) {
                            up_info = data;
                        });
                        
                        //如果失败
                        if(up_info.code != 1){
                            layer.closeAll();
                            layer.alert(up_info.msg || "错误代码：404",{icon:2,title:'更新失败',anim: 2,shadeClose: false,closeBtn: 0,btn: ['知道了']});
                            return;
                        }
                        //设为异步模式
                        $.ajaxSetup({ async : true }); 
                        //开始请求更新
                        request_update(); let msg = '';
                        function request_update(){
                            if( i >= up_info.info.length){
                                layer.closeAll();
                                layer.alert('更新完毕,请刷新页面!',{icon:1,title:'更新成功',anim: 2,shadeClose: false,closeBtn: 0,btn: ['刷新页面']},function () {parent.location.reload();});
                                return;
                            }else{
                                i++;
                            }
                            $("#layui-layer"+ msg_id+" .layui-layer-padding").html('<i class="layui-layer-face layui-icon layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop"></i>[ ' + i + ' / ' +     up_info.info.length + ' ] ' + up_info.info[i-1]);
                            
                            $.post(get_api("other_upsys"),{"i":i}, function(data, status) {
                                if (data.code == 1) { 
                                    request_update();
                                }else{
                                    layer.closeAll();
                                    layer.alert(data.msg || "未知错误,请联系开发者!",{icon:5,title:up_info.info[i-1],anim: 2,shadeClose: false,closeBtn: 0,btn: ['知道了']});
                                } 
                            });
                        }
                    },btn2: function(index, layero){
                        window.open("https://gitee.com/tznb/TwoNav/releases");
                    },btn3: function(index, layero){
                        return true;
                    },cancel: function(){ 
                        return true;
                    }
                });
            });
        }else{
            $("#sysup").css("color", "rgb(1, 170, 237)");
            $('#sysup').on('click', function(){
                layer.alert("暂无可用更新,当前为最新版本",{icon:1,title:"更新系统",anim: "slideDown",shadeClose: true,closeBtn: 0,btn: ['知道了']});
            });
        }
    }
    
    //查看更新日志
    $('#ver').css({"cursor":"pointer","color":"#01AAED"}); //设置鼠标形状和字体颜色
    $('#ver').attr("title","点击查看更新日志");
    $('#ver').on('click', function(){
        miniTab.openNewTabByIframe({
            href:'updatelog',
            title:"更新日志",
        });
    });
});

function get_api(method,type=null){
    return './index.php?c=api&method=' + method + (type?'&type='+type:'') + '&u=' + u ;
}