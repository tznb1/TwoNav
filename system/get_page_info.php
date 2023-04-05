<?php 
// 来源  https://blog.mimvp.com/article/23089.html

function get_page_info($output, $friend_link = '', $curl_info=array()) {
    $page_info = array();
    $page_info['site_title'] = '';  //标题
    $page_info['site_description'] = ''; //描述
    $page_info['site_keywords'] = ''; //关键字
    $page_info['friend_link_status'] = 0; //友情链接检测
    $page_info['site_home_size'] = 0; //字符串长度
     
    if(empty($output)) return $page_info;
     
    // 获取网页编码，把非utf-8网页编码转成utf-8，防止网页出现乱码
    $meta_content_type = '';
    if(isset($curl_info['content_type']) && strstr($curl_info['content_type'], "charset=") != "") {
        $meta_content_type = explode("charset=", $curl_info['content_type'])[1];
    }
    if($meta_content_type == '') {
        preg_match('/<META\s+http-equiv="Content-Type"\s+content="([\w\W]*?)"/si', $output, $matches);       // 中文编码，如 http://www.qq.com
        if (empty($matches[1])) {
            preg_match('/<META\s+content="([\w\W]*?)"\s+http-equiv="Content-Type"/si', $output, $matches);
        }
        if (empty($matches[1])) {
            preg_match('/<META\s+charset="([\w\W]*?)"/si', $output, $matches);       // 特殊字符编码，如 http://www.500.com
        }
        if (!empty($matches[1]) && strstr($matches[1], "charset=") != "") {
            $meta_content_type = explode("charset=", $matches[1])[1];
        }
    }
    if(!in_array(strtolower($meta_content_type), array('','utf-8','utf8'))) {
        $output = mb_convert_encoding($output, "utf-8", $meta_content_type);        // gbk, gb2312
    }
     
    // 若网页仍然有乱码，有乱码则gbk转utf-8
    if(json_encode( $output ) == '' || json_encode( $output ) == null) {
        $output = mb_convert_encoding($output, "utf-8", 'gbk');
    }
     
    $page_info['site_home_size'] = strlen($output);
     
    // 标题
    preg_match('/<TITLE>([\w\W]*?)<\/TITLE>/si', $output, $matches);
    if (!empty($matches[1])) {
        $page_info['site_title'] = $matches[1];
    }
     
    // 正则匹配，获取全部的meta元数据
    preg_match_all('/<META(.*?)>/si', $output, $matches);
    $meta_str_array = $matches[0];
     
    $meta_array = array();
    $meta_array['description'] = '';
    $meta_array['keywords'] = '';
     
    foreach($meta_str_array as $meta_str) {
        preg_match('/<META\s+name="([\w\W]*?)"\s+content="([\w\W]*?)"/si', $meta_str, $res);
        if(!empty($res)) $meta_array[strtolower($res[1])] = $res[2];
         
        preg_match('/<META\s+content="([\w\W]*?)"\s+name="([\w\W]*?)"/si', $meta_str, $res);
        if(!empty($res)) $meta_array[strtolower($res[2])] = $res[1];
         
        preg_match('/<META\s+http-equiv="([\w\W]*?)"\s+content="([\w\W]*?)"/si', $meta_str, $res);
        if(!empty($res)) $meta_array[strtolower($res[1])] = $res[2];
         
        preg_match('/<META\s+content="([\w\W]*?)"\s+http-equiv="([\w\W]*?)"/si', $meta_str, $res);
        if(!empty($res)) $meta_array[strtolower($res[2])] = $res[1];
         
        preg_match('/<META\s+scheme="([\w\W]*?)"\s+content="([\w\W]*?)"/si', $meta_str, $res);
        if(!empty($res)) $meta_array[strtolower($res[1])] = $res[2];
         
        preg_match('/<META\s+content="([\w\W]*?)"\s+scheme="([\w\W]*?)"/si', $meta_str, $res);
        if(!empty($res)) $meta_array[strtolower($res[2])] = $res[1];
    }
     
    $page_info['site_keywords'] = $meta_array['keywords'];
    $page_info['site_description'] = $meta_array['description'];
    //$page_info['meta_array'] = $meta_array; //暂时不需要全部meta
     
    # 判断是否存在友链
    if(!empty($friend_link) && strstr($output, $friend_link) != "") {
        $page_info['friend_link_status'] = 1;
    }
     
    return $page_info;
}