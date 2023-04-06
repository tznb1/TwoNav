layui.define(["jquery", "layer"], function (exports) {
    var $ = layui.$,
        layer = layui.layer;

    var miniTheme = {
        config: function (bgcolorId) {
            var bgColorConfig = [{headerRightBg:'#ffffff',headerRightBgThis:'#e4e4e4',headerRightColor:'rgba(107, 107, 107, 0.7)',headerRightChildColor:'rgba(107, 107, 107, 0.7)',headerRightColorThis:'#565656',headerRightNavMore:'rgba(160, 160, 160, 0.7)',headerRightNavMoreBg:'#1E9FFF',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#565656',headerLogoBg:'#192027',headerLogoColor:'rgb(191, 187, 187)',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#28333E',leftMenuBgThis:'#1E9FFF',leftMenuChildBg:'#0c0f13',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#1e9fff',},{headerRightBg:'#23262e',headerRightBgThis:'#0c0c0c',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#1aa094',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#0c0c0c',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#23262e',leftMenuBgThis:'#737373',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#23262e',},{headerRightBg:'#ffa4d1',headerRightBgThis:'#bf7b9d',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#ffa4d1',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#e694bd',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#1f1f1f',leftMenuBgThis:'#737373',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#ffa4d1',},{headerRightBg:'#1aa094',headerRightBgThis:'#197971',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#1aa094',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#0c0c0c',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#23262e',leftMenuBgThis:'#1aa094',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#1aa094',},{headerRightBg:'#1e9fff',headerRightBgThis:'#0069b7',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#1e9fff',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#0c0c0c',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#1f1f1f',leftMenuBgThis:'#1e9fff',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#1e9fff',},{headerRightBg:'#ffb800',headerRightBgThis:'#d09600',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#d09600',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#243346',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#2f4056',leftMenuBgThis:'#8593a7',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#ffb800',},{headerRightBg:'#e82121',headerRightBgThis:'#ae1919',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#ae1919',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#0c0c0c',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#1f1f1f',leftMenuBgThis:'#3b3f4b',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#e82121',},{headerRightBg:'#963885',headerRightBgThis:'#772c6a',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#772c6a',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#243346',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#2f4056',leftMenuBgThis:'#586473',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#963885',},{headerRightBg:'#2D8CF0',headerRightBgThis:'#0069b7',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#0069b7',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#0069b7',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#1f1f1f',leftMenuBgThis:'#2D8CF0',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#2d8cf0',},{headerRightBg:'#ffb800',headerRightBgThis:'#d09600',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#d09600',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#d09600',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#2f4056',leftMenuBgThis:'#3b3f4b',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#ffb800',},{headerRightBg:'#e82121',headerRightBgThis:'#ae1919',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#ae1919',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#d91f1f',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#1f1f1f',leftMenuBgThis:'#3b3f4b',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#e82121',},{headerRightBg:'#963885',headerRightBgThis:'#772c6a',headerRightColor:'rgba(255,255,255,.7)',headerRightChildColor:'#676767',headerRightColorThis:'#ffffff',headerRightNavMore:'rgba(255,255,255,.7)',headerRightNavMoreBg:'#772c6a',headerRightNavMoreColor:'#ffffff',headerRightToolColor:'#bbe3df',headerLogoBg:'#772c6a',headerLogoColor:'#ffffff',leftMenuNavMore:'rgb(191, 187, 187)',leftMenuBg:'#2f4056',leftMenuBgThis:'#626f7f',leftMenuChildBg:'rgba(0,0,0,.3)',leftMenuColor:'rgb(191, 187, 187)',leftMenuColorThis:'#ffffff',tabActiveColor:'#963885',}];
            if (bgcolorId === undefined) {
                return bgColorConfig;
            } else {
                return bgColorConfig[bgcolorId];
            }
        },
        render: function (options) {
            options.bgColorDefault = options.bgColorDefault || false;
            options.listen = options.listen || false;
            var bgcolorId = localStorage.getItem(u + '_layuiminiBgcolorId');
            if (bgcolorId === null || bgcolorId === undefined || bgcolorId === '') {
                bgcolorId = options.bgColorDefault;
            }
            miniTheme.buildThemeCss(bgcolorId);
            if (options.listen) miniTheme.listen(options);
        },
        buildThemeCss: function (bgcolorId) {
            if (!bgcolorId) {
                return false;
            }
            var bgcolorData = miniTheme.config(bgcolorId);
            var styleHtml = 
                '.layui-layout-admin .layui-header {\n' +
                '    background-color: ' + bgcolorData.headerRightBg + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layui-layout-admin .layui-header .layuimini-header-content > ul > .layui-nav-item.layui-this, .layuimini-tool i:hover {\n' +
                '    background-color: ' + bgcolorData.headerRightBgThis + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layui-layout-admin .layui-header .layui-nav .layui-nav-item a {\n' +
                '    color:  ' + bgcolorData.headerRightColor + ';\n' +
                '}\n' +
                '.layui-layout-admin .layui-header .layui-nav .layui-nav-item .layui-nav-child a {\n' +
                '    color:  ' + bgcolorData.headerRightChildColor + '!important;\n' +
                '}\n'+
                '\n' +
                '.layui-header .layuimini-menu-header-pc.layui-nav .layui-nav-item a:hover, .layui-header .layuimini-header-menu.layuimini-pc-show.layui-nav .layui-this a {\n' +
                '    color: ' + bgcolorData.headerRightColorThis + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layui-header .layui-nav .layui-nav-more {\n' +
                '    border-top-color: ' + bgcolorData.headerRightNavMore + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layui-header .layui-nav .layui-nav-mored, .layui-header .layui-nav-itemed > a .layui-nav-more {\n' +
                '    border-color: transparent transparent ' + bgcolorData.headerRightNavMore + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layui-header .layui-nav .layui-nav-child dd.layui-this a, .layui-header .layui-nav-child dd.layui-this, .layui-layout-admin .layui-header .layui-nav .layui-nav-item .layui-nav-child .layui-this a {\n' +
                '    background-color: ' + bgcolorData.headerRightNavMoreBg + ' !important;\n' +
                '    color:' + bgcolorData.headerRightNavMoreColor + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layui-layout-admin .layui-header .layuimini-tool i {\n' +
                '    color: ' + bgcolorData.headerRightToolColor + ';\n' +
                '}\n' +
                '\n' +
                '.layui-layout-admin .layuimini-logo {\n' +
                '    background-color: ' + bgcolorData.headerLogoBg + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layui-layout-admin .layuimini-logo h1 {\n' +
                '    color: ' + bgcolorData.headerLogoColor + ';\n' +
                '}\n' +
                '\n' +
                '.layuimini-menu-left .layui-nav .layui-nav-more,.layuimini-menu-left-zoom.layui-nav .layui-nav-more {\n' +
                '    border-top-color: ' + bgcolorData.leftMenuNavMore + ';\n' +
                '}\n' +
                '\n' +
                '.layuimini-menu-left .layui-nav .layui-nav-mored, .layuimini-menu-left .layui-nav-itemed > a .layui-nav-more,   .layuimini-menu-left-zoom.layui-nav .layui-nav-mored, .layuimini-menu-left-zoom.layui-nav-itemed > a .layui-nav-more {\n' +
                '    border-color: transparent transparent  ' + bgcolorData.leftMenuNavMore + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layui-side.layui-bg-black, .layui-side.layui-bg-black > .layuimini-menu-left > ul, .layuimini-menu-left-zoom > ul {\n' +
                '    background-color:  ' + bgcolorData.leftMenuBg + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layuimini-menu-left .layui-nav-tree .layui-this, .layuimini-menu-left .layui-nav-tree .layui-this > a, .layuimini-menu-left .layui-nav-tree .layui-nav-child dd.layui-this, .layuimini-menu-left .layui-nav-tree .layui-nav-child dd.layui-this a, .layuimini-menu-left-zoom.layui-nav-tree .layui-this, .layuimini-menu-left-zoom.layui-nav-tree .layui-this > a, .layuimini-menu-left-zoom.layui-nav-tree .layui-nav-child dd.layui-this, .layuimini-menu-left-zoom.layui-nav-tree .layui-nav-child dd.layui-this a {\n' +
                '    background-color: ' + bgcolorData.leftMenuBgThis + ' !important\n' +
                '}\n' +
                '\n' +
                '.layuimini-menu-left .layui-nav-itemed > .layui-nav-child{\n' +
                '    background-color: ' + bgcolorData.leftMenuChildBg + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layuimini-menu-left .layui-nav .layui-nav-item a, .layuimini-menu-left-zoom.layui-nav .layui-nav-item a {\n' +
                '    color:  ' + bgcolorData.leftMenuColor + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layuimini-menu-left .layui-nav .layui-nav-item a:hover, .layuimini-menu-left .layui-nav .layui-this a, .layuimini-menu-left-zoom.layui-nav .layui-nav-item a:hover, .layuimini-menu-left-zoom.layui-nav .layui-this a {\n' +
                '    color:' + bgcolorData.leftMenuColorThis + ' !important;\n' +
                '}\n' +
                '\n' +
                '.layuimini-tab .layui-tab-title .layui-this .layuimini-tab-active {\n' +
                '    background-color: ' + bgcolorData.tabActiveColor + ';\n' +
                '}\n';
            $('#layuimini-bg-color').html(styleHtml);
        },
        buildBgColorHtml: function (options) {
            options.bgColorDefault = options.bgColorDefault || 0;
            var bgcolorId = parseInt(localStorage.getItem(u + '_layuiminiBgcolorId'));
            if (isNaN(bgcolorId)) bgcolorId = options.bgColorDefault;
            var bgColorConfig = miniTheme.config();
            var html = '';
            $.each(bgColorConfig, function (key, val) {
                if (key === bgcolorId) {
                    html += '<li class="layui-this" data-select-bgcolor="' + key + '">\n';
                } else {
                    html += '<li  data-select-bgcolor="' + key + '">\n';
                }
                html += '<a href="javascript:;" data-skin="skin-blue" style="" class="clearfix full-opacity-hover">\n' +
                    '<div><span style="display:block; width: 20%; float: left; height: 12px; background: ' + val.headerLogoBg + ';"></span><span style="display:block; width: 80%; float: left; height: 12px; background: ' + val.headerRightBg + ';"></span></div>\n' +
                    '<div><span style="display:block; width: 20%; float: left; height: 40px; background: ' + val.leftMenuBg + ';"></span><span style="display:block; width: 80%; float: left; height: 40px; background: #ffffff;"></span></div>\n' +
                    '</a>\n' +
                    '</li>';
            });
            return html;
        },
        listen: function (options) {
            $('body').on('click', '[data-bgcolor]', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                var clientHeight = (document.documentElement.clientHeight) - 60;
                var bgColorHtml = miniTheme.buildBgColorHtml(options);
                var html = '<div class="layuimini-color">\n' +
                    '<div class="color-title">\n' +
                    '<span>配色方案</span>\n' +
                    '</div>\n' +
                    '<div class="color-content">\n' +
                    '<ul>\n' + bgColorHtml + '</ul>\n' +
                    '</div>\n' +
                    '<div class="more-menu-list">\n' +
                    '<a class="more-menu-item" href="https://gitee.com/tznb/TwoNav/wikis/pages" target="_blank"><i class="layui-icon layui-icon-read" style="font-size: 19px;"></i> 使用说明</a>\n' +
                    '<a class="more-menu-item" href="https://gitee.com/tznb/TwoNav" target="_blank"><i class="layui-icon layui-icon-tabs" style="font-size: 16px;"></i> 开源地址</a>\n' +
                    '</div>' +
                    '</div>';
                layer.open({
                    type: 1,
                    title: false,
                    closeBtn: 0,
                    shade: 0.2,
                    anim: 2,
                    shadeClose: true,
                    id: 'layuiminiBgColor',
                    area: ['340px', clientHeight + 'px'],
                    offset: 'rb',
                    content: html,
                    success: function (index, layero) {
                    },
                    end: function () {
                        $('.layuimini-select-bgcolor').removeClass('layui-this');
                    }
                });
                layer.close(loading);
            });

            $('body').on('click', '[data-select-bgcolor]', function () {
                var bgcolorId = $(this).attr('data-select-bgcolor');
                $('.layuimini-color .color-content ul .layui-this').attr('class', '');
                $(this).attr('class', 'layui-this');
                localStorage.setItem(u + '_layuiminiBgcolorId', bgcolorId);
                miniTheme.render({
                    bgColorDefault: bgcolorId,
                    listen: false,
                });
            });
        }
    };
    exports("miniTheme", miniTheme);
});