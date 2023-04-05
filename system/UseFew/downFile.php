<?php
/**
* CURL下载文件 成功返回true，失败返回false
*/
function downFile($url, $file = '', $savePath = './data/temp/'){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); //超时/秒
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //不直接输出
    curl_setopt($ch, CURLOPT_HEADER, FALSE);  //不需要response header
    curl_setopt($ch, CURLOPT_NOBODY, FALSE);  //需要response body
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //允许重定向(适应网盘下载)
    
    try{
        $res = curl_exec($ch);
    }finally{
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }
    
    if ($code == '200') { //状态码正常
        
        if(empty($file)){ //如果文件名为空
            $file = date('Ymd_His').'.tmp';
        }
        $fullName = rtrim($savePath, '/') . '/' . $file;
        return file_put_contents($fullName, $res);
    }else{
        return false;
    }
}