<?php
//判断是否需要更新缓存
function is_Update_Sitemap($sitemap_config,$sitemap_path){
    if (file_exists($sitemap_path)) {
        $up_time = filemtime($sitemap_path);
        $timeIntervals = [
            'monthly' => 30 * 24 * 60 * 60, // 30天
            'weekly' => 7 * 24 * 60 * 60, // 7天
            'daily' => 24 * 60 * 60, // 1天
            'hourly' => 60 * 60, // 1小时
            'minute' => 60, //1分钟
            'second' => 1 //1秒
        ];
        
        $interval_seconds = $timeIntervals[$sitemap_config['changefreq']] ?? 86400; //间隔秒
        if (time() - $up_time >= $interval_seconds){
            return true;
        }else{
            return false;
        }
    //缓存文件不存在时重新创建地图
    }else{
        return true;
    }
}

//创建地图数据函数
function create_sitemap($sitemap_config,$sitemap_path,$u){
    //创建一个空的 XML 文档
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    //创建根元素
    $urlset = $xml->createElement('urlset');
    $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $xml->appendChild($urlset);
    //今天
    $today = date("Y-m-d\TH:i:s", time());
    //域名
    $host = $_SERVER['HTTP_HOST']; // 获取主机名
    $port = isset($_SERVER['SERVER_PORT']) ? ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':'.$_SERVER['SERVER_PORT']) : ''; // 获取端口号
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://'; // 获取协议
    $host = $scheme.$host.$port;
    //用户主页 0.关闭 1.动态地址 2.静态地址 3.二级域名
    if($sitemap_config['user_homepage'] > 0){
        //读取用户列表
        $user_list = select_db('global_user','User','');
        if($sitemap_config['user_homepage'] == '3'){
            $strings = explode('.',$_SERVER['HTTP_HOST']);
            if(count($strings) == 3){
                $root_domain = "{$strings[1]}.{$strings[2]}";
            }elseif(count($strings) == 2){
                $root_domain = $_SERVER['HTTP_HOST'];
            }else{
                $sitemap_config['user_homepage'] == '1';
            }
        }
        //遍历用户列表
        foreach($user_list as $user){
            if($sitemap_config['user_homepage'] == '2'){
                $locurl = "{$host}/{$user}.html";
            }elseif($sitemap_config['user_homepage'] == '3'){
                $locurl = "{$scheme}{$user}.{$root_domain}";
            }else{
                $locurl = "{$host}/index.php?u={$user}";
            }
            //生成数据
            $url = createUrlElement($xml, $locurl, $today, $sitemap_config['user_homepage_changefreq'], $sitemap_config['user_homepage_weight']);
            $urlset->appendChild($url);
        }

    }
    
    //过度页面 0.关闭 1.动态 2.静态
    if($sitemap_config['click_page'] > 0){
        $category_parent = []; //父分类
        $categorys = []; //全部分类
        //查找条件 - 分类
        $where['uid'] = UID; 
        $where['fid'] = 0;
        $where['pid'] = 0;
        $where['status'] = 1;
        $where['ORDER'] = ['weight'=>'ASC'];
        $where['property'] = 0;
        //查找一级分类
        $category_parent = select_db('user_categorys','cid',$where);
        //遍历二级分类
        foreach ($category_parent as $cid) {
            $where['fid'] = $cid;
            $category_subitem = select_db('user_categorys','cid',$where);
            array_push($categorys,$cid);
            $categorys = array_merge ($categorys,$category_subitem);
        }

        //遍历链接
        foreach ($categorys as $cid) {
            $where['fid'] = $cid;
            $links = select_db('user_links',['lid','up_time'],$where);
            foreach ($links as $link) {
                if($sitemap_config['click_page'] == '2'){
                    $locurl = "{$host}/{$u}/click/{$link['lid']}.html";
                }else{
                    $locurl = "{$host}/index.php?c=click&id={$link['lid']}&u={$u}";
                }
                $url = createUrlElement($xml, $locurl, date("Y-m-d\TH:i:s", $link['up_time']), $sitemap_config['click_page_changefreq'], $sitemap_config['click_page_weight']);
                $urlset->appendChild($url);
            }
        }
    }
    
    //文章页面
    if($sitemap_config['article_page'] > 0){
        $article_list = select_db('user_article_list',['id','up_time'],['state'=>1,'uid'=>UID]);
        foreach ($article_list as $data) {
            if($sitemap_config['article_page'] == '2'){
                $locurl = "{$host}/{$u}/article/{$data['id']}.html";
            }else{
                $locurl = "{$host}/index.php?c=article&id={$data['id']}&u={$u}";
            }
            $url = createUrlElement($xml, $locurl, date("Y-m-d\TH:i:s", $data['up_time']), $sitemap_config['article_page_changefreq'], $sitemap_config['article_page_weight']);
            $urlset->appendChild($url);
        }
    }

    //保存 XML 内容到文件
    $xml->save($sitemap_path);
    
    //返回内容
    return $xml->saveXML();
}

// 生成URL元素
function createUrlElement($xml, $loc, $lastmod, $changefreq, $priority) {
    $url = $xml->createElement('url');
    
    $locElem = $xml->createElement('loc', htmlspecialchars($loc));
    $url->appendChild($locElem);
    
    $lastmodElem = $xml->createElement('lastmod', $lastmod);
    $url->appendChild($lastmodElem);
    
    $changefreqElem = $xml->createElement('changefreq', $changefreq);
    $url->appendChild($changefreqElem);
    
    $priorityElem = $xml->createElement('priority', $priority);
    $url->appendChild($priorityElem);
    
    return $url;
}