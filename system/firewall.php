<?php //错误码定义,20开头XSS拦截,21开头SQL拦截!

foreach($_POST as $key =>$value){
    //只检测文本类型
    if(!is_string($value)){
        continue;
    }
    //拦截XSS
    if($global_config['XSS_WAF'] == 1 ){
        //站点设置:放行头部和底部代码
        if($method =='write_site_setting' && ($key =='custom_header' || $key =='custom_footer')){
            continue;
        }
        if($method == 'write_article'){
            continue;
        }
        if(preg_match('/<(iframe|script|body|img|layer|div|meta|style|base|object|input)/i',$value)){
            $code = 2001;
        }elseif(preg_match('/(onmouseover|onerror|onload)\=/i',$value)){
            $code = 2002;
        }
    }
    //拦截SQL注入
    if(!isset($code) && $global_config['SQL_WAF'] == 1 ){
        if(preg_match("/\s+(or|xor|and)\s+(=|<|>|'|".'")/i',$value)){
            $code = 2101;
        }elseif(preg_match("/select.+(from|limit)/i",$value)){
            $code = 2102;
        }elseif(preg_match("/(?:(union(.*?)select))/i",$value)){
            $code = 2103;
        }elseif(preg_match("/sleep\((\s*)(\d*)(\s*)\)/i",$value)){
            $code = 2105;
        }elseif(preg_match("/benchmark\((.*)\,(.*)\)/i",$value)){
            $code = 2106;
        }elseif(preg_match("/(?:from\W+information_schema\W)/i",$value)){
            $code = 2107;
        }elseif(preg_match("/(?:(?:current_)user|database|schema|connection_id)\s*\(/i",$value)){
            $code = 2108;
        }elseif(preg_match("/into(\s+)+(?:dump|out)file\s*/i",$value)){
            $code = 2109;
        }elseif(preg_match("/group\s+by.+\(/i",$value)){
            $code = 2110;
        }
    }
    
    if(!empty($code)){
        $tips = $code <= 2100 ? 
            '<br />如果您是站长,请前往系统设置关闭防XSS脚本<br />如果您是用户,请联系站长处理': 
            '<br />如果您是站长,请前往系统设置关闭防SQL注入<br />如果您是用户,请联系站长处理';
        msgA(['code'=>$code,'msg'=>$code.':已拦截不合法参数！'.$tips,'key'=>$key,'Value'=>$value,'method'=>$method ]);
    }
}
