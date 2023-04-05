/**
 * XIcon - layui 图标选择器
 *
 * 基于iconHhysFa 1.0改造，包含font-awesome图标和layui的图标
 * @modify imlzw  adai.imlzw.cn imlzw@vip.qq.com
 * @Date 2020-10-27
 * @version 1.0.1
 * 1.0 改造内容：
 * - 采用fixed下拉模式，自动识别上下下拉方向，识别弹出，
 * - 支持zIndex定义，避免下拉被遮挡的问题
 * - 支持同时选择layui icon 与 font-awesome图标选择
 * - 优化冗余代码, 精简配置
 * - 修改下拉样式，采用紧凑风格，所有图标尽收眼底
 *
 * @config 配置参数
 *
 * {
        // 选择器，推荐使用input
        elem: '#demo1',
        // xIcon组件目录路径，用于加载其它相关文件
        base: './xIcon/',
        // 数据类型：layui/awesome，推荐使用layui
        type: 'layui,awesome',
        // 是否开启搜索：true/false，默认true
        search: true,
        // 是否开启分页：true/false，默认true
        page: true,
        // 每页显示数量，默认100
        limit: 100,
        // z-index 下拉层级数
        zIndex: undefined,
         // 初始值
        initValue: undefined,
        // 点击回调
        click: function(data) {
            console.log(data);
        },
        // 渲染成功后的回调
        success: function(d) {
            console.log(d);
        }
 * }
 *
 * Base: iconHhysFa 1.0 字体图标选择
 * User: jackhhy
 * Date: 2020/06/23-11:09
 * Link: https://gitee.com/luckygyl/iconFonts
 */

layui.define(['laypage', 'form', 'jquery', 'layer'], function (exports) {
    "use strict";

    /**
     * 获取当前js目录
     * @returns {string|null}
     */
    function getCurrJSPath() {
        var scripts = document.getElementsByTagName("script")
        if (scripts) {
            for (var i = 0; i < scripts.length; i++) {
                let number = scripts[i].src.indexOf("xIcon.js");
                if (scripts[i] && scripts[i].src) {
                    if (number >= 0) {
                        return scripts[i].src.substring(0, number);
                    }
                }
            }
        }
        return '';
    }

    let XIcon = function () {
            this.v = '1.0';
        },
        base = getCurrJSPath(),
        _MOD = 'xIcon',
        _this = this,
        $ = layui.jquery,
        laypage = layui.laypage,
        form = layui.form,
        BODY = 'body',
        TIPS = '请选择图标';


    /**
     * 渲染组件
     */
    XIcon.prototype.render = function (options) {
        base = base || options.base;
        var opts = options,
            // DOM选择器
            elem = opts.elem,
            // 数据类型：layui/awesome
            type = opts.type == null ? 'layui' : opts.type,
            //当数据类型为awesome 的时候 需要配置 url
            url = opts.url,
            // 是否分页：true/false
            page = opts.page == null ? true : opts.page,
            zIndex = opts.zIndex || undefined,
            // 每页显示数量
            limit = opts.limit == null ? 100 : opts.limit,
            // 是否开启搜索：true/false
            search = opts.search == null ? true : opts.search,
            // 每个图标格子的宽度：'43px'或'20%'
            cellWidth = opts.cellWidth == null ? '20%' : opts.cellWidth,
            // 点击回调
            click = opts.click,
            // 渲染成功后的回调
            success = opts.success,
            // json数据
            data = [''],
            // 唯一标识
            tmp = new Date().getTime(),
            // 初始化时input的值
            ORIGINAL_ELEM_VALUE = opts.initValue || $(elem).val(),
            TITLE = 'layui-select-title',
            TITLE_ID = 'layui-select-title-' + tmp,
            ICON_BODY = 'layui-iconpicker-' + tmp,
            PICKER_BODY = 'layui-iconpicker-body-' + tmp,
            PAGE_ID = 'layui-iconpicker-page-' + tmp,
            LIST = 'layui-iconpicker-list',
            LIST_BOX = 'layui-iconpicker-list-box',
            selected = 'layui-form-selected',
            unselect = 'layui-unselect';

        var a = {
            init: function () {
                if (type.indexOf("layui") > -1) {
                    data = [...data, ...common.getfont["layui"]()];
                }
                if (type.indexOf("awesome") > -1) {
                    data = [...data, ...common.getData(base + 'variables.less')];
                }
                a.hideElem().createSelect().createBody().toggleSelect();
                a.preventEvent().inputListen();
                common.loadCss();
                if (success) {
                    success(this.successHandle());
                }
                return a;
            },
            successHandle: function () {
                var d = {
                    options: opts,
                    data: data,
                    id: tmp,
                    elem: $('#' + ICON_BODY)
                };
                return d;
            },
            /**
             * 隐藏elem
             */
            hideElem: function () {
                $(elem).hide();
                return a;
            },
            /**
             * 绘制select下拉选择框
             */
            createSelect: function () {
                let initValue = ORIGINAL_ELEM_VALUE || '';
                let classValue = initValue.indexOf("layui-icon") >= 0 ? 'layui-icon ' + initValue : initValue;
                classValue = classValue.indexOf("fa-") >= 0 ? 'fa ' + classValue : classValue;
                var oriIcon = '<i class="' + classValue + '"></i>';
                var selectHtml = '<div class="layui-iconpicker layui-unselect layui-form-select" id="' + ICON_BODY + '">' +
                    '<div class="' + TITLE + '" id="' + TITLE_ID + '">' +
                    '<div class="layui-iconpicker-item">' +
                    '<span class="layui-iconpicker-icon layui-unselect">' +
                    oriIcon +
                    '</span>' +
                    '<i class="layui-edge"></i>' +
                    '</div>' +
                    '</div>' +
                    '<div class="layui-anim layui-anim-upbit" style="">' +
                    '123' +
                    '</div>';
                $(elem).after(selectHtml).attr('value', initValue).val(initValue);
                return a;

            },
            computeDropdownStyle: function () {
                var $icon = $('#' + ICON_BODY);
                let rect = $icon[0].getBoundingClientRect();
                let dropdown = $('.layui-anim', $icon);
                //确定下拉框是朝上还是朝下
                dropdown[0].style.width = rect.width + 'px';
                dropdown[0].style.left = rect.left + 'px';
                dropdown[0].style.position = 'fixed';
                dropdown[0].style.zIndex = zIndex;
                dropdown[0].style.visibility = 'hidden';
                dropdown[0].style.display = 'block';
                let dropRect = dropdown[0].getBoundingClientRect();
                dropdown[0].style.display = '';
                dropdown[0].style.visibility = '';
                let y = rect.y || rect.top || 0;
                let clientHeight = document.documentElement.clientHeight;
                let diff = clientHeight - y - rect.height - 20;

                let direction = diff > dropRect.height || y < diff ? 'down' : 'up';

                if (direction == 'down') {
                    dropdown[0].style.top = rect.top + rect.height + 4 + 'px';
                    dropdown[0].style.bottom = 'auto';
                    $('.layui-iconpicker-list', dropdown)[0].style.maxHeight = clientHeight - rect.top - rect.height - 8 - 39 - (page ? 14 : 0) - (search ? 47 : 0) + 'px';
                } else {
                    dropdown[0].style.top = 'auto';
                    dropdown[0].style.bottom = clientHeight - rect.top + 4 + 'px';
                    $('.layui-iconpicker-list', dropdown)[0].style.maxHeight = rect.top - 8 - 39 - (page ? 14 : 0) - (search ? 47 : 0) + 'px';
                }

            },
            /**
             * 展开/折叠下拉框
             */
            toggleSelect: function () {
                var item = '#' + TITLE_ID + ' .layui-iconpicker-item,#' + TITLE_ID + ' .layui-iconpicker-item .layui-edge';
                a.event('click', item, function (e) {
                    var $icon = $('#' + ICON_BODY);
                    if ($icon.hasClass(selected)) {
                        $icon.removeClass(selected).addClass(unselect);
                    } else {
                        a.computeDropdownStyle();
                        // 隐藏其他picker
                        $('.layui-form-select').removeClass(selected);
                        // 显示当前picker
                        $icon.addClass(selected).removeClass(unselect);
                        // 定位搜索框
                        $(".layui-iconpicker-search > input", $icon).focus();
                    }
                    e.stopPropagation();
                });
                return a;

            },

            /**
             * 绘制主体部分
             */
            createBody: function () {
                // 获取数据
                var searchHtml = '';

                if (search) {
                    searchHtml = '<div class="layui-iconpicker-search">' +
                        '<input class="layui-input">' +
                        '<i class="layui-icon">&#xe615;</i>' +
                        '</div>';
                }
                // 组合dom
                var bodyHtml = '<div class="layui-iconpicker-body" id="' + PICKER_BODY + '">' +
                    searchHtml +
                    '<div class="' + LIST_BOX + '"></div> ' +
                    '</div>';
                $('#' + ICON_BODY).find('.layui-anim').eq(0).html(bodyHtml);
                a.search().createList().check().page();
                return a;
            },


            /**
             * 绘制图标列表
             * @param text 模糊查询关键字
             * @returns {string}
             */
            createList: function (text) {
                var d = data,
                    l = d.length,
                    pageHtml = '',
                    $list = $('<div class="layui-iconpicker-list">')//'<div class="layui-iconpicker-list">';

                // 计算分页数据
                var _limit = page ? limit : data.length, // 每页显示数量
                    _pages = l % _limit === 0 ? l / _limit : parseInt(l / _limit + 1), // 总计多少页
                    _id = PAGE_ID;

                // 图标列表
                var icons = [];

                for (var i = 1; i < l; i++) {
                    var obj = d[i];

                    // 判断是否模糊查询
                    if (text && obj.indexOf(text) === -1) {
                        continue;
                    }

                    // 是否自定义格子宽度
                    var style = '';
                    if (cellWidth !== null) {
                        style += ' style="width:' + cellWidth + '"';
                    }
                    // 每个图标dom
                    var icon = '<div class="layui-iconpicker-icon-item" title="' + obj + '" ' + style + '>';

                    if (obj.indexOf("layui-icon") > -1) {
                        icon += '<i class="layui-icon ' + obj + '"></i>';
                    } else if (obj.indexOf("fa-") > -1) {
                        icon += '<i class="fa ' + obj + '"></i>';
                    } else if (!obj){
                        icon += '<i class="empty-icon" title="不选择">空</i>'
                    }
                    icon += '</div>';

                    icons.push(icon);
                }

                // 查询出图标后再分页
                l = icons.length;
                _pages = l % _limit === 0 ? l / _limit : parseInt(l / _limit + 1);
                for (var i = 0; i < _pages; i++) {
                    // 按limit分块
                    var lm = $('<div class="layui-iconpicker-icon-limit" id="layui-iconpicker-icon-limit-' + tmp + (i + 1) + '">');

                    for (var j = i * _limit; j < (i + 1) * _limit && j < l; j++) {
                        lm.append(icons[j]);
                    }
                    $list.append(lm);
                }
                // 无数据
                if (l === 0) {
                    $list.append('<p class="layui-iconpicker-tips">无数据</p>');
                }
                // 判断是否分页
                if (page) {
                    $('#' + PICKER_BODY).addClass('layui-iconpicker-body-page');
                    pageHtml = '<div class="layui-iconpicker-page" id="' + PAGE_ID + '">' +
                        '<div class="layui-iconpicker-page-count">' +
                        '<span id="' + PAGE_ID + '-current">1</span>/' +
                        '<span id="' + PAGE_ID + '-pages">' + _pages + '</span>' +
                        ' (<span id="' + PAGE_ID + '-length">' + l + '</span>)' +
                        '</div>' +
                        '<div class="layui-iconpicker-page-operate">' +
                        '<i class="layui-icon" id="' + PAGE_ID + '-prev" data-index="0" prev>&#xe603;</i> ' +
                        '<i class="layui-icon" id="' + PAGE_ID + '-next" data-index="2" next>&#xe602;</i> ' +
                        '</div>' +
                        '</div>';
                }
                let $page = page ? $(pageHtml) : null;
                let $icon = $('#' + ICON_BODY).find('.layui-anim');
                if ($icon.find("." + LIST)[0]) {
                    $icon.find('.' + LIST).html($list.html());
                    if ($page) {
                        $icon.find('.layui-iconpicker-page').html($page.html());
                    }
                } else {
                    $icon.find('.' + LIST_BOX).html('').append($list).append(pageHtml);
                }
                return a;
            },
            // 阻止Layui的一些默认事件
            preventEvent: function () {
                var item = '#' + ICON_BODY + ' .layui-anim';
                a.event('click', item, function (e) {
                    e.stopPropagation();
                });
                return a;
            },

            // 分页
            page: function () {
                var icon = '#' + PAGE_ID + ' .layui-iconpicker-page-operate .layui-icon';

                $(icon).unbind('click');
                a.event('click', icon, function (e) {
                    var elem = e.currentTarget,
                        total = parseInt($('#' + PAGE_ID + '-pages').html()),
                        isPrev = $(elem).attr('prev') !== undefined,
                        // 按钮上标的页码
                        index = parseInt($(elem).attr('data-index')),
                        $cur = $('#' + PAGE_ID + '-current'),
                        // 点击时正在显示的页码
                        current = parseInt($cur.html());

                    // 分页数据
                    if (isPrev && current > 1) {
                        current = current - 1;
                        $(icon + '[prev]').attr('data-index', current);
                    } else if (!isPrev && current < total) {
                        current = current + 1;
                        $(icon + '[next]').attr('data-index', current);
                    }
                    $cur.html(current);

                    // 图标数据
                    $('#' + ICON_BODY + ' .layui-iconpicker-icon-limit').hide();
                    $('#layui-iconpicker-icon-limit-' + tmp + current).show();
                    e.stopPropagation();
                });
                return a;
            },
            /**
             * 搜索
             */
            search: function () {
                var item = '#' + PICKER_BODY + ' .layui-iconpicker-search .layui-input';
                a.event('input propertychange', item, function (e) {
                    var elem = e.target,
                        t = $(elem).val();
                    a.createList(t);
                });
                return a;
            },
            /**
             * 点击选中图标
             */
            check: function () {
                var item = '#' + PICKER_BODY + ' .layui-iconpicker-icon-item';
                a.event('click', item, function (e) {
                    var el = $(e.currentTarget).find('i'),
                        icon = '';

                    let classValue = el.attr('class');
                    var clsArr = (classValue&&classValue.split(/[\s\n]/)) || [],
                        cls = clsArr[1] || '',
                        icon = cls;
                    $('#' + TITLE_ID).find('.layui-iconpicker-item .layui-iconpicker-icon i').html('').attr('class', clsArr.join(' '));
                    $('#' + ICON_BODY).removeClass(selected).addClass(unselect);
                    let $inputElem = $(elem);
                    $inputElem.val(icon).attr('value', icon);
                    if (icon) {
                        $inputElem.removeClass("layui-form-danger");
                    }
                    // 回调
                    if (click) {
                        click({
                            icon: clsArr[0]+' '+icon
                        });
                    }
                });
                return a;

            },


            // 监听原始input数值改变
            inputListen: function () {
                var el = $(elem);
                a.event('change', elem, function () {
                    var value = el.val();
                })
                // el.change(function(){

                // });
                return a;
            },
            event: function (evt, el, fn) {
                $(BODY).on(evt, el, fn);
            }

        };


        var common = {
            /**
             * 加载样式表
             */
            loadCss: function () {
                var head = document.getElementsByTagName('head')[0];
                var link = document.createElement('link');
                link.href = base + 'xIcon.css';
                link.rel = 'stylesheet';
                link.type = 'text/css';
                head.appendChild(link);
            },
            /**
             * 获取数据
             */
            getfont: {
                layui: function () {
                    var arr = ["layui-icon-rate-half", "layui-icon-rate", "layui-icon-rate-solid", "layui-icon-cellphone", "layui-icon-vercode", "layui-icon-login-wechat", "layui-icon-login-qq", "layui-icon-login-weibo", "layui-icon-password", "layui-icon-username", "layui-icon-refresh-3", "layui-icon-auz", "layui-icon-spread-left", "layui-icon-shrink-right", "layui-icon-snowflake", "layui-icon-tips", "layui-icon-note", "layui-icon-home", "layui-icon-senior", "layui-icon-refresh", "layui-icon-refresh-1", "layui-icon-flag", "layui-icon-theme", "layui-icon-notice", "layui-icon-website", "layui-icon-console", "layui-icon-face-surprised", "layui-icon-set", "layui-icon-template-1", "layui-icon-app", "layui-icon-template", "layui-icon-praise", "layui-icon-tread", "layui-icon-male", "layui-icon-female", "layui-icon-camera", "layui-icon-camera-fill", "layui-icon-more", "layui-icon-more-vertical", "layui-icon-rmb", "layui-icon-dollar", "layui-icon-diamond", "layui-icon-fire", "layui-icon-return", "layui-icon-location", "layui-icon-read", "layui-icon-survey", "layui-icon-face-smile", "layui-icon-face-cry", "layui-icon-cart-simple", "layui-icon-cart", "layui-icon-next", "layui-icon-prev", "layui-icon-upload-drag", "layui-icon-upload", "layui-icon-download-circle", "layui-icon-component", "layui-icon-file-b", "layui-icon-user", "layui-icon-find-fill", "layui-icon-loading", "layui-icon-loading-1", "layui-icon-add-1", "layui-icon-play", "layui-icon-pause", "layui-icon-headset", "layui-icon-video", "layui-icon-voice", "layui-icon-speaker", "layui-icon-fonts-del", "layui-icon-fonts-code", "layui-icon-fonts-html", "layui-icon-fonts-strong", "layui-icon-unlink", "layui-icon-picture", "layui-icon-link", "layui-icon-face-smile-b", "layui-icon-align-left", "layui-icon-align-right", "layui-icon-align-center", "layui-icon-fonts-u", "layui-icon-fonts-i", "layui-icon-tabs", "layui-icon-radio", "layui-icon-circle", "layui-icon-edit", "layui-icon-share", "layui-icon-delete", "layui-icon-form", "layui-icon-cellphone-fine", "layui-icon-dialogue", "layui-icon-fonts-clear", "layui-icon-layer", "layui-icon-date", "layui-icon-water", "layui-icon-code-circle", "layui-icon-carousel", "layui-icon-prev-circle", "layui-icon-layouts", "layui-icon-util", "layui-icon-templeate-1", "layui-icon-upload-circle", "layui-icon-tree", "layui-icon-table", "layui-icon-chart", "layui-icon-chart-screen", "layui-icon-engine", "layui-icon-triangle-d", "layui-icon-triangle-r", "layui-icon-file", "layui-icon-set-sm", "layui-icon-add-circle", "layui-icon-404", "layui-icon-about", "layui-icon-up", "layui-icon-down", "layui-icon-left", "layui-icon-right", "layui-icon-circle-dot", "layui-icon-search", "layui-icon-set-fill", "layui-icon-group", "layui-icon-friends", "layui-icon-reply-fill", "layui-icon-menu-fill", "layui-icon-log", "layui-icon-picture-fine", "layui-icon-face-smile-fine", "layui-icon-list", "layui-icon-release", "layui-icon-ok", "layui-icon-help", "layui-icon-chat", "layui-icon-top", "layui-icon-star", "layui-icon-star-fill", "layui-icon-close-fill", "layui-icon-close", "layui-icon-ok-circle", "layui-icon-add-circle-fine"];
                    return arr;
                }
            },
            /**
             * 获取数据
             */
            getData: function (url) {
                var iconlist = [];
                $.ajax({
                    url: url,
                    type: 'get',
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    async: false,
                    success: function (ret) {
                        var exp = /fa-var-(.*):/ig;
                        var result;
                        while ((result = exp.exec(ret)) != null) {
                            iconlist.push('fa-' + result[1]);
                        }
                    },
                    error: function (xhr, textstatus, thrown) {
                        layer.msg('fa图标接口有误');
                    }
                });
                return iconlist;
            }

        };

        a.init();
        // return new XIcon();
    }

    /**
     * 设置值
     * @param elem 组件元素查询
     * @param iconName 图标名称，自动识别layui/unicode
     */
    XIcon.prototype.setValue = function (elem, iconName) {
        var el = $(elem),
            p = el.next().find('.layui-iconpicker-item .layui-iconpicker-icon i'),
            c = iconName;
        if (c.indexOf('#xe') > -1) {
            p.html(c);
        } else {
            c = c.indexOf("layui-icon") >= 0 ? "layui-icon " + c : c;
            c = c.indexOf("fa-") >= 0 ? "fa " + c : c;
            p.html('').attr('class', c);
        }
        el.attr('value', iconName).val(iconName);
        if (iconName) {
            el.removeClass("layui-form-danger");
        }
    };


    var xIcon = new XIcon();
    exports(_MOD, xIcon);
});