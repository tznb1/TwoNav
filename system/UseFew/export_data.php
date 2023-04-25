<?php 
if(!defined('DIR')){
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    exit;
}

global $USER_DB;
session_start();
//请求生成数据,验证密码,成功返回Key
if($_GET['type'] == 'create' ){
    $pwd = Get_MD5_Password($_POST['pwd'],$USER_DB["RegTime"]) === $USER_DB["Password"];
    if($pwd){
        $key = md5(uniqid().Get_Rand_Str(8));
        try { 
            $tempnam = create_data();
        }catch(Exception $e){
            if(Debug){
                msgA(['code'=>-1,'msg'=>'导出失败','Message'=>$e->getMessage(),'debug'=>debug_backtrace()]);
            }else{
                msg(-1,'导出失败');
            }
        }
        $_SESSION['download']["$key"] = $tempnam;
        msgA(['code'=>1,'msg'=>'success','key'=>$key]);
    }else{
        msg(-1,'密码错误');
    }
}

//验证Key
if(!is_file($_SESSION['download'][$_GET['key']])){
    exit("Key错误,请在后台重新导出!".$_SESSION['download']["{$_GET['key']}"]);
}else{
    if($_GET['type'] == 'html' ){
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=TwoNav_bookmarks_'.date("Ymd_His").'.html');
        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: binary"); 
        header('Content-Length: '. filesize($_SESSION['download'][$_GET['key']]));
        readfile($_SESSION['download'][$_GET['key']]);
        unlink ($_SESSION['download'][$_GET['key']]);//删除临时文件
        unset($_SESSION['download'][$_GET['key']]); //删除Key
    }
    
    if($_GET['type'] == 'db3' ){
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=TwoNav_bookmarks_'.date("Ymd_His").'.db3'); //文件名
        header("Content-Type: application/octet-stream"); 
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($_SESSION['download'][$_GET['key']])); //告诉浏览器，文件大小
        readfile($_SESSION['download'][$_GET['key']]);
        unlink ($_SESSION['download'][$_GET['key']]);//删除临时文件
        unset($_SESSION['download'][$_GET['key']]); //删除Key
    }
}
//生成数据
function create_data(){
    if($_POST['type'] == 'html' ){
        $key = md5(uniqid().Get_Rand_Str(8));
        $tempnam = DIR ."/data/temp/export_html_{$key}.html";
        $file = fopen($tempnam, "w") or msg(-1,'载入临时文件失败');
        fwrite($file,base64_decode("PCFET0NUWVBFIE5FVFNDQVBFLUJvb2ttYXJrLWZpbGUtMT4NCjwhLS0gVGhpcyBpcyBhbiBhdXRvbWF0aWNhbGx5IGdlbmVyYXRlZCBmaWxlLg0KICAgICBJdCB3aWxsIGJlIHJlYWQgYW5kIG92ZXJ3cml0dGVuLg0KICAgICBETyBOT1QgRURJVCEgLS0+DQo8TUVUQSBIVFRQLUVRVUlWPSJDb250ZW50LVR5cGUiIENPTlRFTlQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCI+DQo8VElUTEU+T25lTmF2IEV4dGVuZCBCb29rbWFya3M8L1RJVExFPg0KPEgxPk9uZU5hdiBFeHRlbmQgQm9va21hcmtzPC9IMT4NCjxETD48cD4NCg=="));
        fwrite($file,'    <DT><H3 ADD_DATE="1677783783" LAST_MODIFIED="1677783783" PERSONAL_TOOLBAR_FOLDER="true">书签栏</H3>'."\n");
        fwrite($file,"    <DL><p>\n");
        //获取父分类
        $category_parent = get_category_parent();
        foreach ($category_parent as $category) {
            fwrite($file,'            <DT><H3 ADD_DATE="'.$category['add_time'].'" LAST_MODIFIED="'.$category['add_time'].'">'.$category['name']."</H3>\n");
            fwrite($file,"            <DL><p>\n");
            //二级分类
            $category_subs = get_category_subs($category['id']);
            foreach ($category_subs as $category_sub) {
                fwrite($file,'              <DT><H3 ADD_DATE="'.$category['add_time'].'" LAST_MODIFIED="'.$category['add_time'].'">'.$category_sub['name']."</H3>\n");
                fwrite($file,"              <DL><p>\n");
                $links = get_links($category_sub['id']);
                foreach ($links as $link) {
                    fwrite($file,'                  <DT><A HREF="'.$link["url"].'" ADD_DATE="'.$link["add_time"].'">'.$link["title"].'</A>'."\n");
                }
                fwrite($file,"              </DL><p>\n");
            }
            //二级分类End
            $links = get_links($category['id']);
            foreach ($links as $link) {
                fwrite($file,'                <DT><A HREF="'.$link["url"].'" ADD_DATE="'.$link["add_time"].'">'.$link["title"].'</A>'."\n");
            }
            fwrite($file,"            </DL><p>\n");
        }
        fwrite($file,"    </DL><p>");
        fwrite($file,base64_decode("DQo8L0RMPjxwPg0K"));
        fclose($file);
        return $tempnam;
    }
    
    if($_POST['type'] == 'db3'){
        $key = md5(uniqid().Get_Rand_Str(8));
        $tempnam = DIR ."/data/temp/export_db3_{$key}.db3";
        try {  //初始化数据库
            class MyDB extends SQLite3 {function __construct() {} } 
            $MyDB = new MyDB();
            $MyDB -> open($tempnam);
            if(!$MyDB) {
                msg(-1,'打开SQLite3数据库失败:'.$MyDB->lastErrorMsg());
            }
            if(!$MyDB->exec(sql_found_table())){
                msg(-1,'执行SQL语句失败:'.$MyDB->lastErrorMsg());
            }
            $MyDB->close();
        }catch(Exception $e){
            msg(-1,'初始化SQLite3失败:'.$e->getMessage());
        }
    
        try {
            $temp_db = new Medoo\Medoo(['type'=>'sqlite','database'=>$tempnam ]);
        }catch (Exception $e) {
            msg(-1,'载入数据库失败'); 
        }
        //处理分类
        $categorys = get_category_db3();
        foreach ($categorys as $key => $data) {
            //去掉fa 头,适应OneNav Extend
            $categorys[$key]['Icon'] = str_replace("fa ","",$data['font_icon']);;
        }
        //处理分类和链接
        foreach ($categorys as $category) {
            //添加分类
            $temp_db->insert('on_categorys',[
                    'id'        =>  $category['id'],
                    'name'      =>  $category['name'],
                    'fid'       =>  $category['fid'],
                    'add_time'  =>  $category['add_time'],
                    'up_time'   =>  $category['up_time'],
                    'weight'    =>  $category['weight'],
                    'property'  =>  $category['property'],
                    'description'=> $category['description'],
                    'Icon'      =>  $category['Icon'],
                    'font_icon' =>  $category['font_icon'],
            ]);

            $links = get_links_db3($category['id']);
            foreach ($links as $link) {
                $temp_db->insert('on_links',[
                    'id'        =>  $link['lid'],
                    'fid'       =>  $link['fid'],
                    'title'     =>  $link['title'],
                    'url'       =>  $link['url'],
                    'description'=>  $link['description'],
                    'add_time'  =>  $link['add_time'],
                    'up_time'   =>  $link['up_time'],
                    'weight'    =>  $link['weight'],
                    'property'  =>  $link['property'],
                    'click'     =>  $link['click'],
                    'topping'   =>  0,
                    'url_standby'   =>  $link['url_standby'],
                    'iconurl'   =>  $link['icon']
                ]);
            }
        }
        return $tempnam;
    }
}


function sql_found_table(){
return
"DROP TABLE IF EXISTS \"on_categorys\";\n".
"CREATE TABLE \"on_categorys\" (\n".
"  \"id\" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,\n".
"  \"name\" TEXT(32) NOT NULL,\n".
"  \"add_time\" TEXT(10) NOT NULL,\n".
"  \"up_time\" TEXT(10) DEFAULT '',\n".
"  \"weight\" integer(3) NOT NULL DEFAULT 0,\n".
"  \"property\" integer(1) NOT NULL DEFAULT 0,\n".
"  \"description\" TEXT(128) DEFAULT '',\n".
"  \"Icon\" TEXT(128),\n".
"  \"font_icon\" TEXT(32),\n".
"  \"fid\" INTEGER NOT NULL DEFAULT 0,\n".
"  CONSTRAINT \"name\" UNIQUE (\"name\" ASC)\n".
");\n".
"DROP TABLE IF EXISTS \"on_links\";\n".
"CREATE TABLE \"on_links\" (\n".
"  \"id\" INTEGER NOT NULL,\n".
"  \"fid\" INTEGER(5) NOT NULL,\n".
"  \"title\" TEXT(64) NOT NULL,\n".
"  \"url\" TEXT(256) NOT NULL,\n".
"  \"description\" TEXT(256),\n".
"  \"add_time\" TEXT(10) NOT NULL,\n".
"  \"up_time\" TEXT(10),\n".
"  \"weight\" integer(3) NOT NULL DEFAULT 0,\n".
"  \"property\" integer(1) NOT NULL DEFAULT 0,\n".
"  \"click\" integer NOT NULL DEFAULT 0,\n".
"  \"topping\" INTEGER NOT NULL DEFAULT 0,\n".
"  \"url_standby\" TEXT(256),\n".
"  \"iconurl\" TEXT(256),\n".
"  \"tagid\" INTEGER DEFAULT 0,\n".
"  PRIMARY KEY (\"id\"),\n".
"  CONSTRAINT \"url\" UNIQUE (\"url\" ASC)\n".
");";
}

//导出db3
function get_category_db3(){
    $content = ['cid(id)','name','add_time','up_time','weight','property','description','font_icon','fid'];
    $where['uid'] = UID; 
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'ASC'];
    return select_db('user_categorys',$content,$where);
}
function get_links_db3($fid) {
    $where["uid"] = UID;
    $where['fid'] = intval($fid);
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'DESC'];
    $where['ORDER']['lid'] = 'ASC';
    $links = select_db('user_links','*',$where);
    return $links;
}


//导出html
function get_category_parent(){
    $content = ['cid(id)','name','add_time'];
    $where['uid'] = UID; 
    $where['fid'] = 0;
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'ASC'];
    return select_db('user_categorys',$content,$where);
}
function get_category_subs($fid){
    $content = ['cid(id)','name','add_time'];
    $where['uid'] = UID; 
    $where['fid'] = $fid;
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'ASC'];
    return select_db('user_categorys',$content,$where);
}
function get_links($fid) {
    $where["uid"] = UID;
    $where['fid'] = intval($fid);
    $where['status'] = 1;
    $where['ORDER'] = ['weight'=>'DESC'];
    $where['ORDER']['lid'] = 'ASC';
    $links = select_db('user_links',['title','url','add_time'],$where);
    return $links;
}