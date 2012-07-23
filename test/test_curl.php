<?php


//          $url="https://portal.neusoft.com/dana-na/auth/url_default/welcome.cgi";
//          $url2="http://www.baidu.com";
//          
//          //$url="https://www.dropbox.com";
//          //$url = "http://openapi.vdisk.me/?m=auth&a=get_token";
//$r = fetch($url);
//echo($r);
//
//echo(fetch($url2));


/**
  * 处理URL，返回文本
  */
function fetch($url, $array=null) {
    if(!empty($url)) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// 下面两行是为了处理https
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		if($array != null)
		{
		    curl_setopt($curl, CURLOPT_POST, true);
		    curl_setopt($curl, CURLOPT_POSTFIELDS, $array);
		}
		$data = curl_exec($curl);
		curl_close($curl);
		
		return $data;
    }
}


?>

<?php


?>