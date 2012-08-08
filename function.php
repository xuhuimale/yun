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
	
//	private $vdisk; // 新浪微盘
	
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
		$config = require_once('kuaipan/config.inc.php');

		$kp = new Kuaipan ( $config ['consumer_key'], $config ['consumer_secret'] );

		$oauth_token = isset ( $_REQUEST ['oauth_token'] ) ? $_REQUEST ['oauth_token'] : '';
		$oauth_verifier = isset ( $_REQUEST ['oauth_verifier'] ) ? $_REQUEST ['oauth_verifier'] : '';
		$_SESSION["oauth_token"] = $oauth_token;
		$_SESSION["oauth_verifier"] = $oauth_verifier;

		$token = $kp->getAccessToken($_SESSION["oauth_token"], $_SESSION["oauth_verifier"]);
		
		// var_dump($token);
        if (empty ( $token ['oauth_token'] ) || empty ( $token ['oauth_token_secret'] )) {
			$authorization_uri = $kp->getAuthorizationUri ( $config ['cb_uri'] );
			if (false === $authorization_uri) {
			    echo 'request token error' . '<br />';
			    echo nl2br ( var_export ( $kp->getError () ) );
			    exit ();
			} else {
			    header ( 'Location:' . $authorization_uri );
			}
        }
		return $kp;
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
						$file -> fileSize = $value["size"];
						$file -> fileType = $value["type"];
						$file -> fileAddTime = date("Y-m-d", $value["ctime"]);
						$file -> fileDirId = $value["dir_id"];
						$file -> fileBytes = $value["length"]; //获取文件字节数
						
						if($value["type"] != "") { // 如果类型不为空，代表是一个文件
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
						
						if(!$value->is_dir) { // 代表是一个文件
						    $file -> fileAddTime = date("Y-m-d", strtotime($value->modified));
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
					// 
					 "错误".$r["err_msg"];
					$result = null;
				}
				break;
			case 'kingsoft':
    			$root_path = 'kuaipan'; // 应用拥有整个快盘的权限，否则可以使用ap_folder
	            
                // Get the metadata for the file/folder specified in $path
                $kp = $this -> getKingsoftDisk();
				try {
				    $ret = $kp->api ( 'metadata', $root_path, $params );
				    if (false === $ret) {
				        $ret = $kp->getError ();
				    }
				} catch ( Exception $e ) {
				    error_log ( $e->getMessage () );
				    $ret = array (
				            'exception' => $e->getMessage ()
				    );
				}
				var_dump($ret);


                exit();
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
						
						if(!$value->is_dir) { // 代表是一个文件
						    $file -> fileAddTime = date("Y-m-d", strtotime($value->modified));
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
					// 
					 "错误".$r["err_msg"];
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
