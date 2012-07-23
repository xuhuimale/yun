<?php
  header("Content-Type:text/html; charset=utf-8");
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
				
				require('vDisk.class.php');

 
				break;
			case "dropbox":
				$this -> serviceId = "dropbox";
				$this -> serviceName = "DROPBOX";
				$this -> homepage = "https://www.dropbox.com/";
				$this -> logoUrl = "./image/logo/dropbox.png";
				$this -> dir = $dir;
				break;
		}
	}
   
	/* 获取网盘的容量信息 */
	function get_quota() {
		$result = array();
		switch($this -> serviceId) {
			case "sina":
				//include_once('vDisk.class.php');
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
				$r = ($vdisk->get_quota());
				//print_r($r);
				$result["used"] = $r['data']['used'];
				$result["total"] = $r['data']['total'];
				return $result;
				break;
			default:
				break;
		}
	}
	
	/* 通过文件编号获取文件信息 */
	function get_file_info($fileId) {
		
		switch($this -> serviceId) {
			case "sina":
				$list = $this -> get_list($_REQUEST["dir"]);
				$file = $list[$fileId];
				break;
			default:
				break;
		}
		return $file;
	}
	   
	/* 获取指定目录下的文件列表 */
	function get_list($dir) {
		$result = array();
		switch($this -> serviceId) {
			case "sina":
				//include_once('vDisk.class.php');
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
						
						if($value["type"] != "") { // 如果类型不为空，代表是一个文件
							$singleFileInfo = $vdisk->get_file_info($value["id"]); // 获取单个文件详细信息
							$file -> fileUrl = $singleFileInfo["data"]["s3_url"]; // 获取文件的下载地址
							$file -> fileBytes = $singleFileInfo["data"]["size"]; //获取文件字节数
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
				//echo "path=$_path======".strlen($_path);
				if(strlen($_path) > 1) {
					$dirNameArray = explode("/", $_path);
					//echo "目录名称数据：";
					//print_r($dirNameArray);
				}
				//include_once('vDisk.class.php');
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
//				echo "dir=$dir";
				
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
				// 添加根目录链接
				//$rootDir = new File();
				//$rootDir -> fileId = 0;
				//$rootDir -> fileName = "RRROOT";
				//$rootDir -> fileUrl = "/yun/frame.php?drive=".$this->serviceId;
				//$dirStruct = array_pad($dirStruct, (0-(count($dirStruct)+1)), $rootDir);

				
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
				
				if($file->fileType != null || $file->fileType != "") { // 删除文件
					$result = $vdisk->delete_file($file->fileId);
				}else { // 删除文件夹
					$result = $vdisk->delete_dir($file->fileId);
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
				
				if($parent_dir_id == null || $parent_dir_id == "") {
					$parent_dir_id = 0;
				}

				$result = $vdisk->create_dir($create_name, $parent_dir_id);
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
$drive = $_REQUEST["drive"]; //云存储服务商
$dir = $_REQUEST["dir"]; // 当前目录

$aService = new Service($drive, $dir);


//echo $aService -> serviceId
?>