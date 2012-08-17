<?php
  ini_set('display_errors', true);
  error_reporting(E_ALL &~ E_NOTICE );
?>
<?php
class File {
	var $fileId;      // 文件编号
	var $fileName;    // 文件名称
	var $fileSize;    // 文件大小（格式化数）
	var $fileBytes;   // 文件大小（字节数）
	var $fileType;    // 文件类型 文件夹或者图片、文档等
	var $fileAddTime; // 文件添加时间
	var $fileDirId;   // 文件所在目录
	var $fileUrl;     // 文件下载链接
	var $filePath;    // 文件在服务器上的路径
}


class Service {
	var $serviceId;   //云服务英文简写
	var $serviceName; //云服务名称
	var $homepage;    //云服务的主页
	var $logoUrl;     //云服务logo地址
	var $dir;         //当前所在的文件夹地址
	var $uploadLimit; //上传的文件大小限制，单位：Bytes
	


    /**
     * Default options for curl.
     */
    protected $default_curl_opts = array (
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:10.0.2) Gecko/20100101 Firefox/10.0.2',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
    );

    /**
     * 使用curl发送request
     * @param string $uri
     * @param string $http_method
     * @param string $file_path  如果需要发送文件，这个参数是文件地址
     * @param string $header  额外的头信息
     * @throws Exception
     */
    protected function request($uri, $http_method = 'GET', $file_path = '', $header = array (), $params = array()) {
        //init
        $uri_parts = parse_url ( $uri );
        $has_content_type = $has_cache_control = $has_connection = $has_keep_alive = false;
        if (!empty($header)) {
            foreach ( $header as $h ) {
                if (strncasecmp ( $h, 'Content-Type:', 13 ) == 0) {
                    $has_content_type = true;
                }
                if (strncasecmp ( $h, 'Cache-Control:', 14 ) == 0) {
                    $has_cache_control = true;
                }
                if (strncasecmp ( $h, 'Connection:', 11 ) == 0) {
                    $has_connection = true;
                }
                if (strncasecmp ( $h, 'Keep-Alive:', 11 ) == 0) {
                    $has_keep_alive = true;
                }
            }
        }
        ! $has_cache_control && $header [] = "Cache-Control: no-cache";
        ! $has_connection && $header [] = "Connection: keep-alive";
        ! $has_keep_alive && $header [] = "Keep-Alive: 300";
        
        $ch = curl_init ($uri);
        $curl_opts = $this->default_curl_opts;
        if (! empty ( $file_path )) {
            if (preg_match ( '/[^a-z0-9\-_.]/i', basename($file_path) )) {
                throw new Exception ( sprintf ( 'Security check: Illegal character in filename "%s".', $file_path ) );
            }
            if ($http_method == 'POST') {//upload file
                //check file
                if (! file_exists ( $file_path )) {
                    throw new Exception ( sprintf ( 'File not exists: "%s".', $file_path ) );
                }
                if (! filesize ( $file_path )) {
                    throw new Exception ( sprintf ( 'File size read error: "%s".', $file_path ) );
                }
                $curl_opts [CURLOPT_POST] = true;
                $curl_opts [CURLOPT_POSTFIELDS] = array ('file' => '@' . $file_path);
            } else { // download file
                // set cookies path
                $cookie_file = tempnam ( sys_get_temp_dir (), 'kp_phpsdk_cookie_' );
                $curl_opts [CURLOPT_COOKIEFILE] = $cookie_file;
                $curl_opts [CURLOPT_COOKIEJAR] = $cookie_file;
                //resource handle for save  file
                $fp = fopen ( $file_path, 'wb' );
                $curl_opts [CURLOPT_FILE] = $fp;
            }
        } else {
            // a 'normal' request, no body to be send
            if ($http_method == 'POST') {
                if (! $has_content_type) {
                    $header [] = 'Content-Type: application/x-www-form-urlencoded';
                    $has_content_type = true;
                }
                $curl_opts [CURLOPT_POST] = true;
                !empty($uri_parts['query']) && $curl_opts [CURLOPT_POSTFIELDS] = $uri_parts['query'];
            }
        }
        //set headers
        $curl_opts[CURLOPT_HTTPHEADER] = $header;

        curl_setopt_array ( $ch, $curl_opts );
        $response = curl_exec ( $ch );

        if ($response === false) {
            $error = curl_error ( $ch );
            curl_close ( $ch );
            isset($fp) && fclose($fp);
            throw new Exception ( 'CURL error: ' . $error );
        }
        unset ( $header, $uri, $http_method, $uri_parts, $file_path );
        if (! empty ( $response )) {
            $code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
            if ($code != 200) {
                $ret = false;
            } else { // parse result
                $ret = $response;
            }
        }
        curl_close ( $ch );
        isset($fp) && fclose($fp);
        return $ret;
    }

	
	/* 构造函数 */
	function __construct($serviceId, $dir) {
		switch($serviceId) 
		{
			case "sina":
				$this -> serviceId = "sina";
				$this -> serviceName = "新浪微盘";
				$this -> homepage = "http://vdisk.weibo.com/";
				$this -> logoUrl = "./image/logo/sina.png";
				$this -> dir = $dir;
				$this -> uploadLimit = 10*1000*1000;
				
				require("./sina/vDisk.class.php");

 
				break;
			case "dropbox":
				$this -> serviceId = "dropbox";
				$this -> serviceName = "DROPBOX";
				$this -> homepage = "https://www.dropbox.com/";
				$this -> logoUrl = "./image/logo/dropbox.png";
				$this -> dir = $dir;
				
				break;
			case "kingsoft":
				$this -> serviceId = "kingsoft";
				$this -> serviceName = "金山快盘";
				$this -> homepage = "http://www.kuaipan.cn/";
				$this -> logoUrl = "./image/logo/klive.ico";
				$this -> dir = $dir;
				
			    break;
			case "baidu":
				$this -> serviceId = "baidu";
				$this -> serviceName = "百度网盘";
				$this -> homepage = "http://pan.baidu.com/infocenter/login";
				$this -> logoUrl = "./image/logo/baidu-logo.ico";
				$this -> dir = $dir;
				
			    break;
			case "microsoft":
				$this -> serviceId = "microsoft";
				$this -> serviceName = "Microsoft Skydriver";
				$this -> homepage = "https://skydrive.live.com/";
				$this -> logoUrl = "./image/logo/skydrive-logo.png";
				$this -> dir = $dir;
				
			    break;
		}
	}
	
	/**
	  *新浪微盘对象
	  */
    private function getSinaVdisk() {
		$appkey = 2106527290;
		$appsecret = '7bddc9daaeae5d45283be7a92a893cb5';
		$username = 'xuhui.male@gmail.com';
		$password = 'huiwolf824613';

		$vdisk = new vDisk($appkey, $appsecret);
        
        if(empty($_SESSION['token'])) {
    		$vdisk->get_token($username, $password, 'sinat');
    		$_SESSION['token'] = $vdisk->token;
		}
		$vdisk->keep_token($_SESSION['token']);
		
		return $vdisk;
    }
   
	/**
	  *Dropbox对象
	  */
    private function getDropboxDisk() {
		require('Dropbox/bootstrap.php');
		
		return $dropbox;
    }

	/**
	  *金山快盘 对象
	  */
    private function getKingsoftDisk() {
		require_once('kuaipan/sdk/kuaipan.class.php');
		$config = require('kuaipan/config.inc.php');

		$kp = new Kuaipan ( $config ['consumer_key'], $config ['consumer_secret'] );

		$oauth_token = isset($_SESSION[Kuaipan::SKEY_ACCESS_TOKEN]) ? $_SESSION[Kuaipan::SKEY_ACCESS_TOKEN] : 
							( isset ( $_REQUEST ['oauth_token'] ) ? $_REQUEST ['oauth_token'] : '') ;
															
		$oauth_verifier = isset($_SESSION[Kuaipan::SKEY_ACCESS_SECRET]) ? $_SESSION[Kuaipan::SKEY_ACCESS_SECRET] : 
							( isset ( $_REQUEST ['oauth_verifier'] ) ? $_REQUEST ['oauth_verifier'] : '');
															
		$_SESSION[Kuaipan::SKEY_ACCESS_TOKEN] = $oauth_token;
		$_SESSION[Kuaipan::SKEY_ACCESS_SECRET] = $oauth_verifier;

		echo "oauth_token=$oauth_token <br>";
		echo "oauth_verifier=$oauth_verifier <br>";
		echo "_SESSION[\"oauth_token\"]=".($_SESSION[Kuaipan::SKEY_ACCESS_TOKEN])."<br>";
		echo "_SESSION[\"oauth_verifier\"]={$_SESSION[Kuaipan::SKEY_ACCESS_SECRET]}<br>";

		return $kp;

    }
	
	/**
	  *百度网盘 对象
	  */
    private function getBaiduDisk() {
		require_once('pcs-php-sdk/pcs.class.php');

        $client_id  = '1WNHyD1cHLQUSFsOrXoYHmfo';
        $api_key    = '1WNHyD1cHLQUSFsOrXoYHmfo';
        $secret_key = 'Iybhit229Hn9FAE9N9XMhPNx9mniUDxA';

        // Check whether to use HTTPS and set the callback URL
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $access_token_code = "pcs_access_token";

        $ret = false;
            unset($_SESSION[$access_token_code]); // 先清空一下access_code
        echo "_SESSION[$access_token_code]".$_SESSION[$access_token_code];
        echo "\n";
        if (isset($_SESSION[$access_token_code])) { // 如果session中有acces_token，已经授权登陆成功，直接返回即可
            $ret = array (
                    'oauth_token' => $_SESSION [$access_token_code]
            );
        }elseif (isset($_REQUEST['code'])) { // 能在request中获取code，是authorize_code，然后获取access_code
            unset($_SESSION[$access_token_code]); // 先清空一下access_code
            echo "string";
            $uri = "https://openapi.baidu.com/oauth/2.0/token?grant_type=authorization_code&code=".$_REQUEST['code'].
                    "&client_id=".$client_id.
                    "&client_secret=".$secret_key.
                    "&scope=netdisk".
                    "&redirect_uri=".$callback;
            $response = $this->request ( $uri );
            if ($response != false) {
                $token = json_decode ( $response, true );
                var_dump($token);
                $_SESSION[$access_token_code] = $token['access_token'];
            }
        }else {
            header("Location:"."https://openapi.baidu.com/oauth/2.0/authorize?response_type=code&".
            						"client_id=".$client_id.
                    				"&scope=netdisk".
            						"&redirect_uri=".($callback));
            exit();
        }

        $access_token = $_SESSION[$access_token_code];
		//echo "access_token=$access_token";
		/* 只需修改这两个参数即可 */
		$auth = array (
			'access_token' => $access_token,
		);	
		$app = 'pcstest_oauth';

		$pcs = new BaiduPCS($auth);
		$pcs->set_ssl(true);
		//var_dump($pcs);
		return $pcs;

    }


	/**
	  *微软网盘 对象
	  */
    private function getMicrosoftDisk() {

        $client_id  = '00000000480D0428';
        $client_secret = 'jb9P9V8b5V0DLawPLAVQlfaAmJlRm-pk';

        // Check whether to use HTTPS and set the callback URL
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $callback = "http://www.xuhui.com/yun/frame.php?drive=microsoft";

        $access_token_code = "skydrive_access_token";

        $ret = false;
        // echo "_SESSION['$access_token_code']".$_SESSION[$access_token_code];
        // echo "<br>";
        if (isset($_SESSION[$access_token_code])) { // 如果session中有acces_token，已经授权登陆成功，直接返回即可
            $ret = array (
                    'oauth_token' => $_SESSION [$access_token_code]
            );
        }elseif (isset($_REQUEST['code'])) { // 能在request中获取code，是authorize_code，然后获取access_code
        	unset($_SESSION[$access_token_code]); // 先清空一下access_code

            $url = "https://login.live.com/oauth20_token.srf?".
            			"code=".$_REQUEST['code'].
            			"&grant_type=authorization_code".
            			"&client_id=$client_id".
            			"&client_secret=$client_secret".
            			"&redirect_uri=".urlencode($callback);
            // echo "url=$url";

            $response = $this->request($url, 'POST');
            if ($response != false) {
                $token = json_decode ( $response, true );
                // var_dump($token);
                $_SESSION[$access_token_code] = $token['access_token'];
            }

        }else {
            header("Location:"."https://login.live.com/oauth20_authorize.srf?response_type=code".
            						"&client_id=".$client_id.
                    				"&scope=wl.basic+wl.signin+wl.skydrive+wl.skydrive_update".
            						"&redirect_uri=".urlencode($callback));
            exit();
        }

        $access_token = $_SESSION[$access_token_code];

		//var_dump($pcs);
		return $access_token;

    }


	/* 获取网盘的容量信息 */
	function get_quota() {
		$result = array();
		switch($this -> serviceId) {
			case "sina":
				//include_once('vDisk.class.php');

				$vdisk = $this -> getSinaVdisk();
				$r = ($vdisk->get_quota());
				//print_r($r);
				$result["used"] = $r['data']['used'];
				$result["total"] = $r['data']['total'];
				break;
			case "dropbox":
    			// Require the bootstrap
                //require('Dropbox/bootstrap.php');
                
                // Retrieve the account information
                $accountInfo = $this -> getDropboxDisk()->accountInfo();
                
				$result["used"] = $accountInfo['body']->quota_info->normal;
				$result["total"] = $accountInfo['body']->quota_info->quota;
				break;
			case "kingsoft":
				$kp = $this -> getKingsoftDisk();
			    $accountInfo = $kp->api ( 'account_info' );
			    if (false === $accountInfo) {
			        $accountInfo = $kp->getError ();
			    }    
				$result["used"] = $accountInfo['quota_used'];
				$result["total"] = $accountInfo['quota_total'];
			    break;
			case 'baidu':
				/* 查询配额空间和已使用空间 */
				$pcs = $this -> getBaiduDisk();
				if (!($data = $pcs->info_quota())) {
					var_dump($pcs);
					return;
				} else {
				    echo json_encode($data);
				}
				break;
			case 'microsoft':
				/* 查询配额空间和已使用空间 */
				$skydrive_access_token = $this -> getMicrosoftDisk();
				$url = "https://apis.live.net/v5.0/me/skydrive/quota?access_token=".$skydrive_access_token;

				//echo "$url";
				$response = $this->request($url, 'GET');
				// var_dump($response);
	            if ($response != false) {
	                $quota = json_decode ( $response, true );
	                
					$result["used"] = $quota['quota'] - $quota['available'];
					$result["total"] = $quota['quota'];
	            }
				break;
			default :
			    break;
		}
		return $result;
	}
	
	/* 通过文件编号获取文件信息 */
	function get_file_info($fileId) {
		
		switch($this -> serviceId) {
			case "sina":
				$list = $this -> get_list($_REQUEST["dir"]);
				$file = $list[$fileId];
				break;
			case "dropbox":
                $file_meta = $this -> getDropboxDisk()->metaData($fileId);
                $file_meta_body = $file_meta["body"];
				if($file_meta["code"] == "200") {
                    $path_in_dropbox = $file_meta_body->path;// Dropbox上获取到的完整路径
                    $filename = substr(strrchr($path_in_dropbox, "/"), 1); // 通过路径截取出文件名
					$file = new File();
					$file -> fileId = $path_in_dropbox;
					$file -> fileName = $filename;
					//$file -> fileDirId = $value["dir_id"];
					
					if(!$file_meta_body->is_dir) { // 代表是一个文件
					    $file -> fileAddTime = date("Y-m-d", strtotime($file_meta_body->modified));
					    $file -> fileSize = $file_meta_body->size;
					    $file -> fileBytes = $file_meta_body->bytes; //获取文件字节数
					    $file -> fileType = $file_meta_body->mime_type;
						//$file -> fileUrl = "https://api-content.dropbox.com/1/files/dropbox".$path_in_dropbox; // 获取文件的下载地址
						$file -> fileUrl = "/yun/fileDownload.php?drive=".$this->serviceId."&file_path=".$path_in_dropbox; // 获取文件的下载地址
					} else { // 如果类型为空，代表是“文件夹”“目录”
						$file -> fileUrl = "/yun/frame.php?drive=".$this->serviceId."&dir=".$path_in_dropbox;
					}
				}
			    break;
			default:
				break;
		}
		return $file;
	}
	
	/* 下载文件 */
	function download_file() {
	    switch($this -> serviceId) {
	        case "sina": 
	            $vdisk = $this -> getSinaVdisk();
	            $singleFileInfo = $vdisk->get_file_info($_REQUEST["fileId"]); // 获取单个文件详细信息
	            $url = $singleFileInfo["data"]["s3_url"]; // 获取文件的下载地址
	            header('Location:'.$url);
	            break;
	        case "dropbox":
    	        $file_path = substr($_REQUEST["file_path"], 1);
                $media = $this -> getDropboxDisk()->media($file_path);
                if($media["code"] == "200") {
                    header('Location:'.$media["body"]->url);
                } else {
                    // Require the bootstrap
                    //require('Dropbox/bootstrap.php');
                    
                    $filename = substr(strrchr($_REQUEST["file_path"], "/"), 1); // 通过路径截取出文件名
                    $encoded_filename = urlencode($filename);
                    $encoded_filename = str_replace("+", "%20", $encoded_filename);
    	            // Set the output file
                    // If $outFile is set, the downloaded file will be written
                    // directly to disk rather than storing file data in memory
                    $outFile = false;
                    
                    $ua = $_SERVER["HTTP_USER_AGENT"];
                    
                    $encoded_filename = urlencode($filename);
                    $encoded_filename = str_replace("+", "%20", $encoded_filename);
                    
                    //header('Content-Type: '. $file['meta']->mime_type);
                    header('Content-Type: application/octet-stream');
                    
                    if (preg_match("/MSIE/", $ua)) {
                    	header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
                    } else if (preg_match("/Firefox/", $ua)) {
                    	header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
                    } else {
                    	header('Content-Disposition: attachment; filename="' . $filename . '"');
                    }
    
                    // Download the file
                    $file = $this -> getDropboxDisk()->getFile($file_path, $outFile);
                    echo $file['data'];
                }

	            break;
	        default:
	            break;
	    }
	}
	   
	/* 获取指定目录下的文件列表 */
	function get_list($dir) {
		$result = array();
		switch($this -> serviceId) {
			case "sina":
				//include_once('vDisk.class.php');
				
				$vdisk = $this -> getSinaVdisk();
				$r = $vdisk->get_list($dir == null ? "0" : $dir);
				//print_r($r);
				
				//echo $r["err_code"];
				if($r["err_code"] == 0) {
					$i = 0;
					foreach ($r["data"] as $value){
						$file = new File();
						$file -> fileId = $value["id"];
						$file -> fileName = $value["name"];
						$file -> fileType = $value["type"];
						$file -> fileAddTime = date("Y-m-d", $value["ctime"]);
						$file -> fileDirId = $value["dir_id"];
						
						if($value["type"] != "") { // 如果类型不为空，代表是一个文件
							$file -> fileSize = $value["size"];
							$file -> fileBytes = $value["length"]; //获取文件字节数
							//$singleFileInfo = $vdisk->get_file_info($value["id"]); // 获取单个文件详细信息
							$file -> fileUrl = "/yun/fileDownload.php?drive=".$this->serviceId."&fileId=".$value["id"]; // 获取文件的下载地址

						} else { // 如果类型为空，代表是“文件夹”“目录”
							$file -> fileUrl = "/yun/frame.php?drive=".$this->serviceId."&dir=".$value["id"]."&path=".$_REQUEST["path"]."/".$value["name"];
						}
						
						$result[$value["id"]] = $file;
					}
					//print_r($result);
				} else {
				    
					// echo "错误".$r["err_msg"];
					$result = null;
				}
				
				break;
			case "dropbox":
                // Require the bootstrap
                //require_once('Dropbox/bootstrap.php');
                
                // Get the metadata for the file/folder specified in $path
                $fileList = $this -> getDropboxDisk()->metaData($dir == null ? "" : $dir);
				if($fileList["code"] == "200") {
					$i = 0;
					foreach ($fileList["body"] -> contents as $value){
                        //var_dump($value);
                        
                        $path_in_dropbox = $value->path;// Dropbox上获取到的完整路径
                        $filename = substr(strrchr($path_in_dropbox, "/"), 1); // 通过路径截取出文件名
						$file = new File();
						$file -> fileId = $path_in_dropbox;
						$file -> fileName = $filename;
						//$file -> fileDirId = $value["dir_id"];
						$file -> fileAddTime = date("Y-m-d", strtotime($value->modified));
						
						if(!$value->is_dir) { // 代表是一个文件
						    $file -> fileSize = $value->size;
						    $file -> fileBytes = $value->bytes; //获取文件字节数
						    $file -> fileType = $value->mime_type;
						    $file -> fileUrl = "/yun/fileDownload.php?drive=".$this->serviceId."&file_path=".$path_in_dropbox; // 获取文件的下载地址
						} else { // 如果类型为空，代表是“文件夹”“目录”
							$file -> fileUrl = "/yun/frame.php?drive=".$this->serviceId."&dir=".$path_in_dropbox;
						}
						
						$result[$value->rev] = $file;
					}
					//print_r($result);
				} else {
					// echo "错误".$r["err_msg"];
					$result = null;
				}
				break;
			case 'kingsoft':
    			$root_path = 'kuaipan'; // 应用拥有整个快盘的权限，否则可以使用ap_folder
	            
                // Get the metadata for the file/folder specified in $path
                $kp = $this -> getKingsoftDisk();
				try {
					//echo "string=".$root_path.urlencode(substr($dir, 1));
					//$params = array('path' => $dir == null ? "" : $dir);
				    $fileList = $kp->api ( 'metadata', $root_path.urlencode(substr($dir, 1)));
				    if (false === $fileList || isset($fileList['http_code'])) {
				        $fileList = $kp->getError ();
				        var_dump($fileList);
				        echo "<script>alert(\"错误编码：".$fileList['http_code']."\");</script>";
				        exit();
				    }
				} catch ( Exception $e ) {
				    error_log ( $e->getMessage () );
				    $fileList = array (
				            'exception' => $e->getMessage ()
				    );
				}
				// var_dump($fileList);

				if(false != $fileList && !isset($fileList['http_code']) ) {
					$i = 0;
					foreach ($fileList["files"] as $value){
                        //var_dump($value);
                        
						$file = new File();
						$file -> fileId = $value['file_id'];
						if($fileList['path'] == "/") {
							$file -> filePath = "/".$value['name'];
						}else {
							$file -> filePath = $fileList['path']."/".$value['name'];
						}
						$file -> fileName = $value['name'];
						//$file -> fileDirId = $value["dir_id"];
						$file -> fileAddTime = date("Y-m-d", strtotime($value['create_time']));
						
						if("file" == $value['type']) { // 代表是一个文件
						    $file -> fileSize = $value['size'];
						    $file -> fileBytes = $value['size']; //获取文件字节数
						    $file -> fileType = $value['type'];
						    $file -> fileUrl = "/yun/fileDownload.php?drive=".$this->serviceId."&file_path=".$file -> filePath; // 获取文件的下载地址
						} else { // 如果类型为空，代表是“文件夹”“目录”
							$file -> fileUrl = "/yun/frame.php?drive=".$this->serviceId."&dir=".urlencode($file -> filePath);
						}
						
						$result[$value['file_id']] = $file;
					}
					//var_dump($result);
					//exit();
				} else {
					// echo "错误".$r["err_msg"];
					$result = null;
				}
				break;
			default:
			    break;
		}
		return $result;
	}
	
	
	/* 获取指定目录的层级结构 */
	function get_dirStruct($dir) {
		$dirStruct = array();

		switch($this -> serviceId) {
			case "sina":
				$_path = substr($_REQUEST["path"], 1);
				if(strlen($_path) > 1) {
					$dirNameArray = explode("/", $_path);
				}
				
				$vdisk = $this -> getSinaVdisk();
				
				for ($i = 0; $i < count($dirNameArray); $i++) {
					$file = new File();
					$file -> fileName = $dirNameArray[$i];
					
					// 路径全名
					$curFullPath = "/".join("/", array_slice($dirNameArray, 0, $i+1));
					$theDirInfo = $vdisk->get_dirid_with_path($curFullPath); // 获取目录的编码
					//echo $theDirInfo["err_msg"];
					$file -> fileId = $theDirInfo["data"]["id"];
					$file -> fileUrl = "/yun/frame.php?drive=".$this->serviceId."&dir=".$theDirInfo["data"]["id"]."&path=".$curFullPath;
					$dirStruct[$i] = $file;
				}

				break;
				
			case "dropbox":
				$_path = substr($dir, 1);
				if(strlen($_path) > 1) {
					$dirNameArray = explode("/", $_path);
				}
				// print_r($dirNameArray);
				
				for ($i = 0; $i < count($dirNameArray); $i++) {
					$file = new File();
					$file -> fileName = $dirNameArray[$i];
					
					// 路径全名
					$curFullPath = "/".join("/", array_slice($dirNameArray, 0, $i+1));
					//echo $theDirInfo["err_msg"];
					$file -> fileUrl = "/yun/frame.php?drive=".$this->serviceId."&dir=".$curFullPath;
					$dirStruct[$i] = $file;
				}				
			
			    break;
			case "kingsoft":
				$_path = substr($dir, 1);
				if(strlen($_path) > 1) {
					$dirNameArray = explode("/", $_path);
				}
				// print_r($dirNameArray);
				
				for ($i = 0; $i < count($dirNameArray); $i++) {
					$file = new File();
					$file -> fileName = $dirNameArray[$i];
					
					// 路径全名
					$curFullPath = "/".join("/", array_slice($dirNameArray, 0, $i+1));
					//echo $theDirInfo["err_msg"];
					$file -> fileUrl = "/yun/frame.php?drive=".$this->serviceId."&dir=".$curFullPath;
					$dirStruct[$i] = $file;
				}				
			
			    break;
			default:
				break;
		}
		return $dirStruct;
	}
	
	
	
	/* 删除指定的目录或者文件 */
	function delete_file_or_dir($file) {

		switch($this -> serviceId) {
			case "sina":
				$vdisk = $this -> getSinaVdisk();
				
				if($file->fileType != null || $file->fileType != "") { // 删除文件
					$result = $vdisk->delete_file($file->fileId);
				}else { // 删除文件夹
					$result = $vdisk->delete_dir($file->fileId);
				}
			
				break;
			case "dropbox":
			    $delete_result = $this -> getDropboxDisk() -> delete($file->fileId);
			    if($delete_result["code"] == "200") {
			        $result = array("err_code" => 0, "err_msg" => "success");
			    }else {
			        $result = array("err_code" => -1, "err_msg" => $delete_result["body"]->error);
			    }
			    break;
			default:
				break;
		}
		// err_code 错误编码，0为成功
		// err_msg 错误原因
		return $result;
	}
	
	
	/* 创建文件夹、目录 */
	function create_dir($create_name, $parent_dir_id) {

		switch($this -> serviceId) {
			case "sina":
				$vdisk = $this -> getSinaVdisk();
				
				if($parent_dir_id == null || $parent_dir_id == "") {
					$parent_dir_id = 0;
				}

				$result = $vdisk->create_dir($create_name, $parent_dir_id);
				break;
			case "dropbox":
			    $file_dropbox_path = $parent_dir_id."/".$create_name;
			    $create_dir_result = $this -> getDropboxDisk() -> create($file_dropbox_path);
			    if($create_dir_result["code"] == "200") {
			        $result = array("err_code" => 0, "err_msg" => "success");
			    }else {
			        $result = array("err_code" => -1, "err_msg" => $create_dir_result["body"]->error);
			    }
			    break;
			default:
				break;
		}
		// err_code 错误编码，0为成功
		// err_msg 错误原因
		return $result;
	}
	
	
		
	/* 创建文件夹、目录 */
	function upload_file($file_path, $parent_dir_id, $if_cover=true) {

		switch($this -> serviceId) {
			case "sina":

				
				$vdisk = $this -> getSinaVdisk();
				
				if($parent_dir_id == null || $parent_dir_id == "") {
					$parent_dir_id = 0;
				}
				
				$cover = $if_cover == true ? "yes" : "no";
				$result = $vdisk->upload_file($file_path, $parent_dir_id, $cover);
				break;
			default:
				break;
		}
		// err_code 错误编码，0为成功
		// err_msg 错误原因
		return $result;
	}
}




?>
<?php
$drive = $_REQUEST["drive"]; //云存储服务商
$dir = $_REQUEST["dir"]; // 当前目录

$aService = new Service($drive, $dir);


//echo $aService -> serviceId
?>
