//取基本URL,是否支持复制
var baseUrl = Get_baseUrl();
var isSupported = ClipboardJS.isSupported();

//书签搜索
var h = holmes({
    input: '.search',
    find: '.link-space',
    placeholder: '<h3>未搜索到匹配结果！</h3>',
    mark: false,
	hiddenAttr: true,
    class: {
      visible: 'visible',
      hidden: 'mdui-hidden'
    },
    onFound(el) {
	  $(".cat-title").addClass("mdui-hidden");
    },
    onInput(el) {
		$(".cat-title").addClass("mdui-hidden");
    },
    onVisible(el) {
		$(".cat-title").removeClass("mdui-hidden");
    },
    onEmpty(el) {
		$(".cat-title").removeClass("mdui-hidden");
    }
  });

//菜单
var menu = {
    "open":{name: "打开",icon:"fa-external-link",callback:function(key,opt){
        var link_id = $(this).attr('id');
        link_id = link_id.replace('id_','');
        var tempwindow=window.open('_blank');
        tempwindow.location='./index.php?c=click&id='+link_id+"&u="+u;
    }},
    "edit": {name: "编辑", icon: "edit",callback:function(key,opt){
        var link_id = $(this).attr('id');
        link_id = link_id.replace('id_','');
        var tempwindow=window.open('_blank');
        tempwindow.location='./index.php?c=admin&page=link_edit&id='+link_id+"&u="+u;
    }},
    "delete": {name: "删除", icon: "delete",callback:function(){
        var link_id = $(this).attr('id');
        link_id = link_id.replace('id_','');
        mdui.confirm('确认删除？'
            ,function(){
                $.post(get_api('write_link','del'),{lid:link_id},function(data,status){
                    if(data.code == 1) {
                        $("#id_" + link_id).remove();
                    }else{
                        mdui.alert(data.msg);
                    }
                });
            },function(){ 
                return true;
            },{
                confirmText:'确定',cancelText:'取消'
            }
        );
    }},
    "sep1": "---------",
    "qrcode": {name: "二维码", icon:"fa-qrcode",callback:function(data,status){
        let url = $(this).attr('link-url');
        url = url.substr(0, 11) == "./index.php" ? baseUrl + url.slice(11) : url;
        mdui.dialog({
            'title': $(this).attr('link-title'),
            'cssClass':'show_qrcode',
            'content':'<div id="qr" style="display:none;"></div><div id="qrcode"></div>'
        });
        //生成二维码
        $('#qr').qrcode({render: "canvas",width: 200,height: 200,text: encodeURI(url)});
        //转换处理,为了兼容微信长按识别
        $('#qrcode').append(convertCanvasToImage(document.getElementsByTagName('canvas')[0]));
    }},
    "copy":{name:"复制链接",icon:"copy",callback:function(){
        let url = $(this).attr('link-url');
        url = url.substr(0, 11) == "./index.php" ? baseUrl + url.slice(11) : url;
        if(isSupported){
            ClipboardJS.copy(url);
            layer.msg('复制成功', {icon: 1});
		}else{
			layer.msg('复制失败,您的浏览器不支持', {icon: 5});
		}
    }}
};

//未登录时移除编辑和删除
if(!is_login){
    delete menu.edit;
    delete menu.delete;
}
//加载右键菜单
$.contextMenu({
    selector: '.link-space',
    items: menu
});

//主题设置
$("#config").click(function(){
    layer.open({
        type: 2,
        scrollbar: false,
        title: '主题配置',
        shadeClose: true,
        area : ['550px' , '99%'],
        anim: 5,
        offset: 'rt',
        content: './index.php?c=admin&page=config_home&u='+u+'&theme='+t+'&fn=home'
    });
});

//添加链接
$("#add").click(function(){
    layer.open({
        type: 2,
        title: '添加链接',
        maxmin: false,
        shadeClose: true,
        area : /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? ['100%' , '100%']:['620px' , '475px'],
        content: './index.php?c=admin&page=add_link_tpl&source=tpl&u='+u
    });
});

//搜索框失去焦点
$(".search").blur(function(data,status){
	if( $(".search").val() == ''){
		$(".cat-title").removeClass("mdui-hidden");
	}
});

//弹窗
function msg(text){
  $html = '<div class = "msg">' + text + '</div>';
  $("body").append($html);
  $(".msg").fadeIn();
  $(".msg").fadeOut(3000);
}

// 到顶部
function gotop(){
	$("html,body").animate({scrollTop: '0px'}, 600);
}

//canvas转Image
function convertCanvasToImage(canvas) {
    var image = new Image();
    image.src = canvas.toDataURL("image/png");
    return image;
}

//取API地址
function get_api(method,type=null){
    return './index.php?c=api&method=' + method + (type?'&type='+type:'') + '&u=' + u ;
}

//取基本URL
function Get_baseUrl() {
    let url = window.location.href,
        hostname = window.location.hostname,
        protocol = window.location.protocol,
        port = window.location.port,
        pathname = window.location.pathname;
        pathname = pathname.substring(0, pathname.lastIndexOf("/") + 1),
        baseUrl = protocol + "//" + hostname + (port ? ":" + port : "") + pathname;
    return baseUrl;
}
