/**
 * 随机背景图
 */
layui.define(['dropdown'], function (exports) {
    var background = {
        render: function (elem) {
            layui.dropdown.render({elem: elem
                ,data: [{
                    title: '博天(自适应/动漫)'
                    ,url: 'https://api.btstu.cn/sjbz/api.php?lx=dongman&method=zsy'
                    ,author:'https://api.btstu.cn/doc/sjbz.php'
                },{
                    title: '博天(自适应/妹子)'
                    ,url: 'https://api.btstu.cn/sjbz/api.php?lx=meizi&method=zsy'
                    ,author:'https://api.btstu.cn/doc/sjbz.php'
                },{
                    title: '博天(自适应/风景)'
                    ,url: 'https://api.btstu.cn/sjbz/api.php?lx=fengjing&method=zsy'
                    ,author:'https://api.btstu.cn/doc/sjbz.php'
                },{
                    title: '博天(自适应/随机)'
                    ,url: 'https://api.btstu.cn/sjbz/api.php?lx=suiji&method=zsy'
                    ,author:'https://api.btstu.cn/doc/sjbz.php'
                },{ 
                    title: '姬长信(PC/每日必应)'
                    ,url: 'https://api.isoyu.com/bing_images.php'
                    ,author:'https://api.isoyu.com'
                },{
                    title: '樱花(PC/动漫)'
                    ,url: 'https://www.dmoe.cc/random.php'
                    ,author:'https://www.dmoe.cc'
                },{
                    title: '梁炯灿(PC/动漫)'
                    ,url: 'https://tuapi.eees.cc/api.php?category=dongman&type=302'
                    ,author:'https://tuapi.eees.cc'
                },{
                    title: '梁炯灿(PC/风景)'
                    ,url: 'https://tuapi.eees.cc/api.php?category=fengjing&type=302'
                    ,author:'https://tuapi.eees.cc'
                },{
                    title: '梁炯灿(PC/必应)'
                    ,url: 'https://tuapi.eees.cc/api.php?category=biying&type=302'
                    ,author:'https://tuapi.eees.cc'
                },{
                    title: '梁炯灿(PC/美女)'
                    ,url: 'https://tuapi.eees.cc/api.php?category=meinv&type=302'
                    ,author:'https://tuapi.eees.cc'
                },{
                    title: '苏晓晴(PC/动漫)'
                    ,url: 'https://acg.toubiec.cn/random.php'
                    ,author:'https://acg.toubiec.cn'
                },{
                    title: '墨天逸(PC/动漫)'
                    ,url: 'https://api.mtyqx.cn/api/random.php'
                    ,author:'https://api.mtyqx.cn/'
                },{
                    title: '小歪(PC/动漫)'
                    ,url: 'https://api.ixiaowai.cn/api/api.php'
                    ,author:'https://api.ixiaowai.cn'
                },{
                    title: '小歪(PC/MC酱)'
                    ,url: 'https://api.ixiaowai.cn/mcapi/mcapi.php'
                    ,author:'https://api.ixiaowai.cn'
                },{
                    title: '小歪(PC/风景)'
                    ,url: 'https://api.ixiaowai.cn/gqapi/gqapi.php'
                    ,author:'https://api.ixiaowai.cn' 
                },{
                    title: '保罗(PC/动漫)'
                    ,url: 'https://api.paugram.com/wallpaper/?source=sina'
                    ,author:'https://api.paugram.com/help/wallpaper'
                },{
                    title: '樱道(PC/动漫)'
                    ,url: 'https://api.r10086.com/img-api.php?type=动漫综合1'
                    ,author:'https://img.r10086.com/'
                }],click: function(obj){
                        this.elem.val(obj.url);
                },style: 'width: 235px;'
            });
        }
    };

    exports("background",background);
});