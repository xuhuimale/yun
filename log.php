<?php
/**
* Զ�̴�URL
* @param string $url   �򿪵�url������ http://www.baidu.com/123.htm
* @param int $limit   ȡ���ص����ݵĳ���
* @param string $post   Ҫ���͵� POST ���ݣ���uid=1&password=1234
* @param string $cookie Ҫģ��� COOKIE ���ݣ���uid=123&auth=a2323sd2323
* @param bool $bysocket TRUE/FALSE �Ƿ�ͨ��SOCKET��
* @param string $ip   IP��ַ
* @param int $timeout   ���ӳ�ʱʱ��
* @param bool $block   �Ƿ�Ϊ����ģʽ
* @return    ȡ�����ַ���
*/
function uc_fopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
$return = '';
$matches = parse_url($url);
!isset($matches['host']) && $matches['host'] = '';
!isset($matches['path']) && $matches['path'] = '';
!isset($matches['query']) && $matches['query'] = '';
!isset($matches['port']) && $matches['port'] = '';
$host = $matches['host'];
$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
$port = !empty($matches['port']) ? $matches['port'] : 80;
if($post) {
   $out = "POST $path HTTP/1.0\r\n";
   $out .= "Accept: */*\r\n";
   //$out .= "Referer: $boardurl\r\n";
   $out .= "Accept-Language: zh-cn\r\n";
   $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
   $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
   $out .= "Host: $host\r\n";
   $out .= 'Content-Length: '.strlen($post)."\r\n";
   $out .= "Connection: Close\r\n";
   $out .= "Cache-Control: no-cache\r\n";
   $out .= "Cookie: $cookie\r\n\r\n";
   $out .= $post;
} else {
   $out = "GET $path HTTP/1.0\r\n";
   $out .= "Accept: */*\r\n";
   //$out .= "Referer: $boardurl\r\n";
   $out .= "Accept-Language: zh-cn\r\n";
   $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
   $out .= "Host: $host\r\n";
   $out .= "Connection: Close\r\n";
   $out .= "Cookie: $cookie\r\n\r\n";
}
$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
if(!$fp) {
   return '';//note $errstr : $errno \r\n
} else {
   stream_set_blocking($fp, $block);
   stream_set_timeout($fp, $timeout);
   @fwrite($fp, $out);
   $status = stream_get_meta_data($fp);
   if(!$status['timed_out']) {
    while (!feof($fp)) {
     if(($header = @fgets($fp)) && ($header == "\r\n" || $header == "\n")) {
      break;
     }
    }

    $stop = false;
    while(!feof($fp) && !$stop) {
     $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
     $return .= $data;
     if($limit) {
      $limit -= strlen($data);
      $stop = $limit <= 0;
     }
    }
   }
   @fclose($fp);
   return $return;
}
}



echo "php http test";
//echo uc_fopen("https://api.dropbox.com/1/oauth/request_token");


$ch = curl_init("http://www.php.net");
curl_exec($ch);
curl_close($ch);
?>
