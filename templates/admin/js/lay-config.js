//layui自定义扩展
window.rootPath = (function (src) {
    src = document.scripts[document.scripts.length - 1].src;
    return src.substring(0, src.lastIndexOf("/") + 1);
})();

layui.config({
    base: rootPath + "lay-module/",
    version: true
}).extend({
    miniAdmin: "layuimini/miniAdmin", // layuimini后台扩展
    miniMenu: "layuimini/miniMenu", // layuimini菜单扩展
    miniTab: "layuimini/miniTab", // layuimini tab扩展
    miniTheme: "layuimini/miniTheme", // layuimini 主题扩展
    treetable: 'treetable-lay/treetable', //table树形扩展
    treetable2: 'treeTable/treeTable', //table树形表格2
    tableSelect: 'tableSelect/tableSelect', // table选择扩展
    iconPickerFa: 'iconPicker/iconPickerFa', // fa图标选择扩展
    xIcon: 'xIcon/xIcon', //图标选择器
    echarts: 'echarts/echarts', // echarts图表扩展
    echartsTheme: 'echarts/echartsTheme', // echarts图表主题扩展
    layarea: 'layarea/layarea', //  省市县区三级联动下拉选择器
    treeSelect: 'treeSelect/treeSelect', // 树形下拉选择器
    background: 'background/background' //随机背景图
});
