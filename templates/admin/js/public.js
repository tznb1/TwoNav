//获取Cookie
function getCookie(cname){
	let name = cname + "=";
	let ca = document.cookie.split(';');
	for(let i=0; i<ca.length; i++) {
		let c = ca[i].trim();
		if (c.indexOf(name)==0) { return c.substring(name.length,c.length); }
	}
	return "";
}
//取随机字符串
function randomString(length) {
  let str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  let result = '';
  for (let i = length; i > 0; --i) 
    result += str[Math.floor(Math.random() * str.length)];
  return result;
}
//取随机数字
function randomnum(length) {
  let str = '0123456789';
  let result = '';
  for (let i = length; i > 0; --i) 
    result += str[Math.floor(Math.random() * str.length)];
  return result;
}
//取Get参数 top:取最顶端的
function _GET(letiable,top = false){
    let query = top ? window.top.location.search.substring(1) : window.location.search.substring(1);
    let lets = query.split("&");
       for (let i=0;i<lets.length;i++) {
               let pair = lets[i].split("=");
               if(pair[0] == letiable){return pair[1];}
       }
       return false;
}
//时间戳格式化
function  timestampToTime(timestamp) {
    let date =  new  Date(timestamp * 1000);
    let y = date.getFullYear();
    let m = date.getMonth() + 1;
    m = m < 10 ? ('0' + m) : m;
    let d = date.getDate();
    d = d < 10 ? ('0' + d) : d;
    let h = date.getHours();
    h = h < 10 ? ('0' + h) : h;
    let minute = date.getMinutes();
    let second = date.getSeconds();
    minute = minute < 10 ? ('0' + minute) : minute;
    second = second < 10 ? ('0' + second) : second;
    return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;
}
//取API地址
function get_api(method,type=null){
    return './index.php?c=api&method=' + method + (type?'&type='+type:'') + '&u=' + u ;
}
//查询IP归属地(统一调用,方便更换接口)
function query_ip(ip){
    window.open('//ip.rss.ink/result/' + ip);
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

//帮助
if (typeof jQuery != 'undefined') { 
    $("#help").click(function(){
        window.open("https://gitee.com/tznb/TwoNav/wikis/pages?sort_id=" + $(this).attr("sort_id") + "&doc_id=3767990","target");
        return false;
    });
}