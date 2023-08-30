<?php if(!defined('DIR')){header('HTTP/1.1 404 Not Found');header("status: 404 Not Found");exit;}
$type = $_REQUEST['type'];

switch ($type) {
    case "share_pwd":
        $share = get_db('user_share','*',['uid'=>UID,'sid'=>Get('share')]);
        if(empty($share)){
            msg(-1,'未找到分享书签'.Get('share'));
        }

        //判断是否加密
        if(!empty($share['pwd'])){
            if($_POST['Password'] === $share['pwd']){
                session_start();
                $_SESSION['verify']['share'][$share['id']] = $share['pwd'];
                msg(1,'success');
            }else{
                msg(-1,'提取码错误');
            }
        }else{
            msg(-1,'分享未加密');
        }
        break;
    case "pwd2":
        $LoginConfig = unserialize($USER_DB['LoginConfig']);
        if($_POST['Password'] === $LoginConfig['Password2']){
            setcookie($USER_DB['User'].'_Password2', md5($USER_DB['Password'].$_COOKIE[U.'_key'].$_POST['Password']), 0,'','',false,true);
            msg(1,'二级密码正确!');
        }else{
            msg(-1,'二级密码错误!');
        }
        break;
    case "link_pwd":
        //读取链接信息
        $link = get_db('user_links',['pid','fid','property'],['uid'=>UID,'lid'=>$_GET['id'],'status'=>1]);
        if(empty($link)){
            msg(-1,'链接不存在'); //查找链接失败
        }
        //链接加密
        if(!empty($link['pid'])){
            $password = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$link['pid']]);
            if(empty($password)){
                msg(-1,'验证失败,错误代码:100'); //密码为空
            }
            if($password == $_POST['Password']){
                session_start();
                $_SESSION['verify']['link'][$_GET['id']] = $password;
                msg(1,'验证通过');
            }else{
                msg(-1,'密码错误!');
            }
        }
        //读取父分类和祖分类信息
        $category_parent = get_db('user_categorys',['cid','pid','fid'],['uid'=>UID,'cid'=>$link['fid'],'status'=>1]);
        if(empty($category_parent)){
            msg(-1,'验证失败,错误代码:101'); //查找父分类失败
        }
        //尝试读取祖分类
        $category_ancestor  = empty($category_parent['fid']) ? [] : get_db('user_categorys',['cid','pid','status'],['uid'=>UID,'cid'=>$category_parent['fid'],'status'=>1]);
        //存在祖分类,且为空姑且认为已停用
        if($category_parent['fid'] > 0 && empty($category_ancestor)){
            msg(-1,'验证失败,错误代码:102'); //查找祖分类失败
        }
        
        //父分类加密
        if(!empty($category_parent['pid'])){
            $password = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$category_parent['pid']]);
            if(empty($password)){
                msg(-1,'验证失败,错误代码:103'); //密码为空
            }
            if($password == $_POST['Password']){
                session_start();
                $_SESSION['verify']['category'][$category_parent['cid']] = $password;
                msg(1,'验证通过');
            }else{
                msg(-1,'密码错误!');
            }
        }
        
        //祖分类加密
        if(!empty($category_ancestor['pid'])){
            $password = get_db('user_pwd_group','password',['uid'=>UID,'pid'=>$category_ancestor['pid']]);
            if(empty($password)){
                msg(-1,'验证失败,错误代码:104'); //密码为空
            }
            if($password == $_POST['Password']){
                session_start();
                $_SESSION['verify']['category'][$category_ancestor['cid']] = $password;
                msg(1,'验证通过');
            }else{
                msg(-1,'密码错误!');
            }
        }
        
        msg(-1,'验证失败,该链接没有加密');
        break;

    default:
        msg(-1,'类型错误');
}